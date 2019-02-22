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

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class SondaggiRispostePredefinite
 * This is the model class for table "sondaggi_risposte_predefinite".
 * @package lispa\amos\sondaggi\models
 */
class SondaggiRispostePredefinite extends \lispa\amos\sondaggi\models\base\SondaggiRispostePredefinite
{
    public $tipo_domanda;
    public $ordine;
    public $ordina_dopo;

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'risposta'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            //[['regola_pubblicazione', 'destinatari', 'validatori'], 'safe'],
            [['tipo_domanda', 'ordina_dopo'], 'integer'],
            ['ordine', 'string'],
        ]);
    }

    /**
     * Funzione che restituisce tutte le domande del sondaggio
     * @param integer $domanda Id della domanda
     * @return ActiveRecord Ritorna un oggetto con la risposta della query con tutte le domande del sondaggio
     */
    public function getTutteRisposteSondaggio()
    {
        return $this->find()->andWhere(['sondaggi_domande_id' => $this->sondaggi_domande_id]);
    }

    /**
     * Funzione che prende il nome della tipologia di domanda
     * @param integer $id Id della tipologia di domanda
     * @return ActiveRecord Ritorna l'oggetto relativo alla tipologia di domanda
     */
    public function getTipologiaDomanda()
    {
        return SondaggiDomandeTipologie::find()->andWhere(['id' => $this->tipo_domanda]);
    }

    /**
     * Ordina le risposte in base alla posizione di quella appena salvata
     * @param string $tipo Tipologia di ordinamento che può essere 'inizio', 'fine' e 'dopo'
     * @param integer $dopo Id della domanda dopo la quale inserire la nuova, se non
     * è settata è 0 quindi questa funzione è disabilitata e la domanda verrà inserita alla fine
     */
    public function setOrdinamento($tipo, $dopo = 0)
    {
        if ($dopo > 0 && $dopo != NULL && $tipo == 'dopo') {

            $ordDopo = SondaggiRispostePredefinite::findOne(['id' => $dopo])->ordinamento;
            $RisposteDopo = $this->getTutteRisposteSondaggio()->andWhere(['>', 'ordinamento', $ordDopo])->andWhere(['!=', 'id', $this->id]);
            $this->ordinamento = $ordDopo + 1;
            $this->save();
            foreach ($RisposteDopo->all() as $Risposte) {
                $aggiorna = SondaggiRispostePredefinite::findOne(['id' => $Risposte['id']]);
                $aggiorna->ordinamento = ($aggiorna->ordinamento + 1);
                $aggiorna->save();
            }

        } else {
            $TutteRisposte = $this->getTutteRisposteSondaggio()->andWhere(['!=', 'id', $this->id]);
            if ($TutteRisposte->count() == 0) {
                $this->ordinamento = 1;
                $this->save();
            } else {
                if ($tipo == 'inizio') {
                    $this->ordinamento = 1;
                    $this->save();
                    foreach ($TutteRisposte->all() as $Risposte) {
                        $aggiorna = SondaggiRispostePredefinite::findOne(['id' => $Risposte['id']]);
                        $aggiorna->ordinamento = ($aggiorna->ordinamento + 1);
                        $aggiorna->save();
                    }
                } else {
                    $this->ordinamento = ($TutteRisposte->max('ordinamento')) ? ($TutteRisposte->max('ordinamento') + 1) : 1;
                    $this->save();
                }
            }
        }
    }
}
