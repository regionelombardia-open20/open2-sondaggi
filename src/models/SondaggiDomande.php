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

use lispa\amos\sondaggi\AmosSondaggi;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class SondaggiDomande
 * This is the model class for table "sondaggi_domande".
 * @package lispa\amos\sondaggi\models
 */
class SondaggiDomande extends \lispa\amos\sondaggi\models\base\SondaggiDomande
{
    //public $regola_pubblicazione;
    //public $destinatari;
    //public $validatori;
    public $condizione_necessaria;
    public $ordine;
    public $ordina_dopo;

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'domanda'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            //[['regola_pubblicazione', 'destinatari', 'validatori'], 'safe'],
            [['condizione_necessaria', 'ordina_dopo'], 'integer'],
            ['ordine', 'string'],
            ['min_int_multipla', 'number', 'min' => 0],
            ['max_int_multipla', 'number', 'min' => 0],
            ['max_int_multipla', 'checkIntervalloSelezioniMultiple'],
            ['nome_classe_validazione', 'required', 'when' => function ($model) {
                return $model->sondaggi_domande_tipologie_id == 9;
            }, 'whenClient' => "function (attribute, value) {"
                . "return $('#sondaggidomande-sondaggi_domande_tipologie_id').val() == 9;"
                . "}"], //da aggiornare nel caso di modifiche alla tipologia custom
        ]);
    }

    public function checkIntervalloSelezioniMultiple($model, $attribute)
    {
        $min = $this->min_int_multipla;
        $max = $this->max_int_multipla;
        if ($min > $max) {
            if ($max != 0) {
                $this->addError($model, AmosSondaggi::t('amossondaggi', 'Le selezioni massime non possono essere minori delle selezioni minime, se non si vogliono limiti massimi impostare il valore a 0'));
            }
        }
    }

    /*
      public function attributeLabels()
      {
      return
      ArrayHelper::merge(
      parent::attributeLabels(),
      [
      'tagValues' => '',
      'regola_pubblicazione' => 'Pubblicata per',
      'destinatari' => 'Per i condominii',
      ]);
      }

      public function behaviors()
      {
      return ArrayHelper::merge(parent::behaviors(), [
      'CwhNetworkBehaviors' => [
      'class' => CwhNetworkBehaviors::className(),
      ]
      ]);
      }

      public static function find()
      {
      $Sondaggi DomandeQuery = new Sondaggi DomandeQuery(get_called_class());
      $Sondaggi DomandeQuery->andWhere('sondaggi Domande.deleted_at IS NULL');
      return $Sondaggi DomandeQuery;
      }
     */

    /**
     * Funzione che restituisce tutte le domande del sondaggio
     *
     * @return ActiveRecord Ritorna un oggetto con la risposta della query con tutte le domande del sondaggio
     */
    public function getTutteDomandeSondaggio()
    {
        return $this->find()->andWhere(['sondaggi_id' => $this->sondaggi->id]);
    }

    /**
     * Restituisce tutte le risposte predefinite tra le quali scegliere quella che condiziona la domanda
     */
    public function getRispostePredefiniteSondaggio()
    {
        $idSondaggio = $this->getSondaggi()->one()->id;
        if ($this->id) {
            $domande = SondaggiDomande::find()->andWhere(['sondaggi_id' => $idSondaggio])->andWhere(['!=', 'id', $this->id]);
        } else {
            $domande = SondaggiDomande::find()->andWhere(['sondaggi_id' => $idSondaggio]);
        }
        $arrDomande = [];
        foreach ($domande->all() as $Domande) {
            $arrDomande[] = $Domande['id'];
        }
        $preQuery = (new \yii\db\Query())->from('sondaggi_risposte_predefinite as R')->where(['IN', 'sondaggi_domande_id', $arrDomande]);
        $risultato = $preQuery->innerJoin('sondaggi_domande as D', 'D.id = R.sondaggi_domande_id')
            ->select('D.id as domandaid, D.domanda as domanda, R.id, R.risposta, R.ordinamento');
        return $risultato;
        /* return SondaggiRispostePredefinite::find()
          ->andWhere(['IN', 'sondaggi_domande_id', $arrDomande])
          ->innerJoin('sondaggi_domande as D', 'D.id = sondaggi_risposte_predefinite.sondaggi_domande_id')
          ->select('D.id as domandaid, D.domanda as domanda, sondaggi_risposte_predefinite.id, sondaggi_risposte_predefinite.risposta, sondaggi_risposte_predefinite.ordinamento'); */
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiRispostePreCondMm()
    {
        return $this->hasOne(SondaggiDomandeCondizionate::className(), ['sondaggi_domande_id' => 'id']);
    }

    /**
     * Ordina le domande in funzione di quella appena salvata
     * @param string $tipo Tipologia di ordinamento che può essere 'inizio', 'fine' e 'dopo'
     * @param integer $dopo Id della domanda dopo la quale inserire la nuova, se non settata è 0 quindi questa funzione è disabilitata e la domanda verrà inserita alla fine
     * @param integer $condizionata Indica se la domanda è condizionata e in questo caso anche se si dovesse scegliere un ordine precedente alla domanda dalla quale dipende verrà inserita subito dopo
     */
    public function setOrdinamento($tipo, $dopo = 0, $condizionata = 0)
    {
        if ($dopo > 0 && $dopo != NULL && $tipo == 'dopo') {
            if ($condizionata == 0) {
                $ordDopo = SondaggiDomande::findOne(['id' => $dopo])->ordinamento;
                $DomandeDopo = $this->getTutteDomandeSondaggio()->andWhere(['>', 'ordinamento', $ordDopo])->andWhere(['!=', 'id', $this->id]);
                $this->ordinamento = $ordDopo + 1;
                $this->save();
                foreach ($DomandeDopo->all() as $Domande) {
                    $aggiorna = SondaggiDomande::findOne(['id' => $Domande['id']]);
                    $aggiorna->ordinamento = ($aggiorna->ordinamento + 1);
                    $aggiorna->save();
                }
            } else {
                $ordDopo = SondaggiDomande::findOne(['id' => $dopo])->ordinamento;
                $condDom = SondaggiRispostePredefinite::findOne(['id' => $condizionata])->sondaggi_domande_id;
                $ordCond = SondaggiDomande::findOne(['id' => $condDom])->ordinamento;
                if ($ordDopo > $ordCond) {
                    $DomandeDopo = $this->getTutteDomandeSondaggio()->andWhere(['>', 'ordinamento', $ordDopo])->andWhere(['!=', 'id', $this->id]);
                    $this->ordinamento = $ordDopo + 1;
                    $this->save();
                    foreach ($DomandeDopo->all() as $Domande) {
                        $aggiorna = SondaggiDomande::findOne(['id' => $Domande['id']]);
                        $aggiorna->ordinamento = ($aggiorna->ordinamento + 1);
                        $aggiorna->save();
                    }
                } else {
                    $DomandeDopo = $this->getTutteDomandeSondaggio()->andWhere(['>', 'ordinamento', $ordCond])->andWhere(['!=', 'id', $this->id]);
                    $this->ordinamento = $ordCond + 1;
                    $this->save();
                    foreach ($DomandeDopo->all() as $Domande) {
                        $aggiorna = SondaggiDomande::findOne(['id' => $Domande['id']]);
                        $aggiorna->ordinamento = ($aggiorna->ordinamento + 1);
                        $aggiorna->save();
                    }
                }
            }
        } else {
            $TutteDomande = $this->getTutteDomandeSondaggio()->andWhere(['!=', 'id', $this->id]);
            if ($TutteDomande->count() == 0) {
                $this->ordinamento = 1;
                $this->save();
            } else {
                if ($tipo == 'inizio' && $condizionata == 0) {
                    $this->ordinamento = 1;
                    $this->save();
                    foreach ($TutteDomande->all() as $Domande) {
                        $aggiorna = SondaggiDomande::findOne(['id' => $Domande['id']]);
                        $aggiorna->ordinamento = ($aggiorna->ordinamento + 1);
                        $aggiorna->save();
                    }
                } else if ($tipo == 'inizio' && $condizionata > 0) {
                    $condDom = SondaggiRispostePredefinite::findOne(['id' => $condizionata])->sondaggi_domande_id;
                    $ordDopo = SondaggiDomande::findOne(['id' => $condDom])->ordinamento;
                    $DomandeDopo = $this->getTutteDomandeSondaggio()->andWhere(['>', 'ordinamento', $ordDopo])->andWhere(['!=', 'id', $this->id]);
                    $this->ordinamento = $ordDopo + 1;
                    $this->save();
                    foreach ($DomandeDopo->all() as $Domande) {
                        $aggiorna = SondaggiDomande::findOne(['id' => $Domande['id']]);
                        $aggiorna->ordinamento = ($aggiorna->ordinamento + 1);
                        $aggiorna->save();
                    }
                } else {
                    $this->ordinamento = ($TutteDomande->max('ordinamento')) ? ($TutteDomande->max('ordinamento') + 1) : 1;
                    $this->save();
                }
            }
        }
    }

    /**
     * Restituisce l'id della domanda precedente
     */
    public function getDomandaPrecedente()
    {
        if ($this->ordinamento) {
            $ordine = $this->ordinamento;
            $domande = SondaggiDomande::find()->andWhere(['<', 'ordinamento', $ordine])->andWhere(['sondaggi_domande_pagine_id' => $this->sondaggi_domande_pagine_id])->orderBy('ordinamento DESC');
            if ($domande->count() > 0) {
                if ($ordine > $domande->one()['ordinamento']) {
                    $domanda = SondaggiDomande::findOne(['ordinamento' => $domande->one()['ordinamento'], 'sondaggi_domande_pagine_id' => $this->sondaggi_domande_pagine_id]);
                    if (count($domanda) == 1) {
                        return $domanda->id;
                    }
                }
            }
        }
        return NULL;
    }
}
