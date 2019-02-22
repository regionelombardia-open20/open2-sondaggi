<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\models
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi\models;

use lispa\amos\attachments\behaviors\FileBehavior;
use lispa\amos\sondaggi\AmosSondaggi;
use yii\helpers\ArrayHelper;

/**
 * Class Sondaggi
 * This is the model class for table "sondaggi".
 * @package lispa\amos\sondaggi\models
 */
class Sondaggi extends \lispa\amos\sondaggi\models\base\Sondaggi
{
    //public $regola_pubblicazione;
    //public $destinatari;
    //public $validatori;
    public $file;
    public $destinatari_pubblicazione;
    public $tipologie_entita;
    public $pubblico;
    public $attivita_formativa;
    public $punto_pei;
    public $mail_subject;
    public $mail_message;
    public $text_not_compilable;
    public $text_end;
    public $text_end_title;
    public $text_end_html;
    public $text_not_compilable_html;

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'titolo'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            //[['regola_pubblicazione', 'destinatari', 'validatori'], 'safe'],
            [['file'], 'file'],
            [['destinatari_pubblicazione'], 'safe'],
            //[['destinatari_pubblicazione', 'tipologie_entita'], 'required'],
            [['tipologie_entita'], 'safe']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return
            ArrayHelper::merge(
                parent::attributeLabels(), [
//      'tagValues' => '',
//      'regola_pubblicazione' => 'Pubblicata per',
//      'destinatari' => 'Per i condominii',
                'text_end_html' => AmosSondaggi::t('amossondaggi', 'Messaggio di fine sondaggio in HTML'),
                'text_not_compilable_html' => AmosSondaggi::t('amossondaggi', 'Messaggio di sondaggio non compilabile in HTML'),
                'mail_message' => AmosSondaggi::t('amossondaggi', 'Testo della e-mail di notifica'),
                'mail_subject' => AmosSondaggi::t('amossondaggi', 'Oggetto della e-mail di notifica'),
                'text_not_compilable' => AmosSondaggi::t('amossondaggi', 'Messaggio di sondaggio non compilabile'),
                'text_end' => AmosSondaggi::t('amossondaggi', 'Messaggio di fine sondaggio'),
                'text_end_title' => AmosSondaggi::t('amossondaggi', 'Titolo della pagina di fine sondaggio'),
            ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ],
        ]);
    }

    /*
          public static function find()
          {
          $SondaggiQuery = new SondaggiQuery(get_called_class());
          $SondaggiQuery->andWhere('sondaggi.deleted_at IS NULL');
          return $SondaggiQuery;
          }
         */

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->file = $this->getFile()->one();
    }

    /**
     * Getter for $this->file;
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOneFile('file');
    }

    public function getAvatarUrl($dimension = 'original')
    {
        $url = '/img/img_default.jpg';
        if ($this->file) {
            $url = $this->file->getUrl($dimension);
        }
        return $url;
    }

    /**
     * Funzione che verifica se il sondaggio è pubblicabile o meno
     * @return boolean True | False - se il sondaggio è pubblicabile restituisce true, altrimenti false
     */
    public function verificaSondaggioPubblicabile()
    {
        $verifica = true;
        $pagine = $this->getSondaggiDomandePagines();
        if ($pagine->count() > 0) {
            foreach ($pagine->all() as $Pagina) {
                $domande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $Pagina['id']]);
                if ($domande->count() > 0) {
                    foreach ($domande->all() as $Domanda) {
                        if (in_array($Domanda['sondaggi_domande_tipologie_id'], [1, 2, 3, 4, 7, 8])) {
                            $risposte = SondaggiRispostePredefinite::find()->andWhere(['sondaggi_domande_id' => $Domanda['id']]);
                            if ($risposte->count() == 0) {
                                $verifica = FALSE;
                            } else {
                                if ($Domanda['min_int_multipla'] > 0) {
                                    $numRisp = $Domanda['min_int_multipla'];
                                    if ($numRisp >= $risposte->count()) {
                                        return FALSE;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $verifica = FALSE;
                }
            }
            $domande = $this->getSondaggiDomandes();
        } else {
            $verifica = FALSE;
        }
        return $verifica;
    }

    /**
     * Restituisce il numero di partecipazioni al sondaggio, se non viene specificato un'utente
     * restituisce il numero totale
     * @param integer $personale 0 | 1 se viene inserito 0 restituirà il numero totale delle risposte al sondaggio, altrimenti il numero di partecipazioni personali
     * @return integer Numero di partecipazioni inclusive di quelle parziali
     */
    public function getNumeroPartecipazioni($personale = 0)
    {
        if ($personale) {
            $utente = \Yii::$app->getUser()->getId();
            $condition1 = new \yii\db\Expression("count(distinct(IF(sondaggi_risposte_sessioni.end_date IS NOT NULL OR sondaggi_risposte.id is not null and sondaggi_risposte_sessioni.end_date is null, sondaggi_risposte_sessioni.id, null))) partecipanti");

            $sessioni = SondaggiRisposteSessioni::find()
                ->innerJoin('sondaggi_risposte', 'sondaggi_risposte.sondaggi_risposte_sessioni_id = sondaggi_risposte_sessioni.id')
                ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $this->id])
                ->andWhere(['sondaggi_risposte_sessioni.user_profile_id' => $utente])
                ->select($condition1);
            return $sessioni->asArray()->one()['partecipanti'];
        } else {
            $condition1 = new \yii\db\Expression("count(distinct(IF(sondaggi_risposte_sessioni.end_date IS NOT NULL OR sondaggi_risposte.id is not null and sondaggi_risposte_sessioni.end_date is null, sondaggi_risposte_sessioni.id, null))) partecipanti");

            $sessioni = SondaggiRisposteSessioni::find()
                ->innerJoin('sondaggi_risposte', 'sondaggi_risposte.sondaggi_risposte_sessioni_id = sondaggi_risposte_sessioni.id')
                ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $this->id])
                ->select($condition1);
            return $sessioni->asArray()->one()['partecipanti'];
        }
    }

    /**
     *
     * @param array $values
     */
    public function getOtherAttributes($values = NULL)
    {
        if (!empty($values)) {
            if (isset($values['Sondaggi']['mail_subject'])) {
                $this->mail_subject = $values['Sondaggi']['mail_subject'];
            }
            if (isset($values['Sondaggi']['mail_message'])) {
                $this->mail_message = $values['Sondaggi']['mail_message'];
            }
            if (isset($values['Sondaggi']['text_end'])) {
                $this->text_end = $values['Sondaggi']['text_end'];
            }
            if (isset($values['Sondaggi']['text_end_html'])) {
                $this->text_end_html = $values['Sondaggi']['text_end_html'];
            }
            if (isset($values['Sondaggi']['text_end_title'])) {
                $this->text_end_title = $values['Sondaggi']['text_end_title'];
            }
            if (isset($values['Sondaggi']['text_not_compilable'])) {
                $this->text_not_compilable = $values['Sondaggi']['text_not_compilable'];
            }
            if (isset($values['Sondaggi']['text_not_compilable_html'])) {
                $this->text_not_compilable_html = $values['Sondaggi']['text_not_compilable_html'];
            }
        } else {
            $oldValues = $this->getSondaggiPubblicaziones()->one();
            $this->mail_subject = $oldValues['mail_subject'];
            $this->mail_message = $oldValues['mail_message'];
            $this->text_end = $oldValues['text_end'];
            $this->text_end_html = $oldValues['text_end_html'];
            $this->text_end_title = $oldValues['text_end_title'];
            $this->text_not_compilable = $oldValues['text_not_compilable'];
            $this->text_not_compilable_html = $oldValues['text_not_compilable_html'];
        }
    }

    /**
     * @return bool
     * in base all'utente loggato, si recupera il numero di volte che è stato compilato il sondaggio
     * se questo è >= del valore inserito in 'compilazioni_disponibili' allora non sarà possibile compilare ulteriormente il sondaggio.
     * NB: se il valore in 'compilazioni_disponibili' è 0 (zero) si assume non ci sia limite
     */
    public function hasCompilazioniSuperate(){
        $utente_id = \Yii::$app->getUser()->getId();

        //se il numero di compilazioni è 0 (zero) => nessun limite di compilazione
        $compilazioni_disponibili = $this->compilazioni_disponibili;
        if(!$compilazioni_disponibili || $compilazioni_disponibili === 0){
            return false;
        }

        if($utente_id){
            $q = SondaggiRisposteSessioni::find()
                ->select('sondaggi_risposte_sessioni.id, sondaggi_risposte_sessioni.user_id, sondaggi_risposte_sessioni.sondaggi_id')
                ->innerJoin('sondaggi_risposte', 'sondaggi_risposte.sondaggi_risposte_sessioni_id = sondaggi_risposte_sessioni.id')
                ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $this->id])
                ->andWhere(['sondaggi_risposte_sessioni.user_id' => $utente_id])
                ->groupBy('sondaggi_risposte_sessioni.id')
            ;

            $numero_compilazioni_x_utente = $q->count();

            //pr( $numero_compilazioni_x_utente >= $compilazioni_disponibili, "superate?");
            return $numero_compilazioni_x_utente >= $compilazioni_disponibili;
        }

        return true;

    }
}
