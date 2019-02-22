<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\models\base
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi\models\base;

use Yii;
use lispa\amos\sondaggi\AmosSondaggi;

/**
* This is the base-model class for table "sondaggi_domande_condizionate".
*
    * @property integer $sondaggi_risposte_predefinite_id
    * @property integer $sondaggi_domande_id
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property integer $version
    *
            * @property \lispa\amos\sondaggi\models\SondaggiDomande $sondaggiDomande
            * @property \lispa\amos\sondaggi\models\SondaggiRispostePredefinite $sondaggiRispostePredefinite
    */
class SondaggiDomandeCondizionate extends \lispa\amos\core\record\Record
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'sondaggi_domande_condizionate';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['sondaggi_risposte_predefinite_id', 'sondaggi_domande_id'], 'required'],
            [['sondaggi_risposte_predefinite_id', 'sondaggi_domande_id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe']
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'sondaggi_risposte_predefinite_id' => AmosSondaggi::t('amossondaggi', 'Risposta attesa'),
    'sondaggi_domande_id' => AmosSondaggi::t('amossondaggi', 'Domanda condizionata'),
    'created_at' => AmosSondaggi::t('amossondaggi', 'Creato il'),
    'updated_at' => AmosSondaggi::t('amossondaggi', 'Aggiornato il'),
    'deleted_at' => AmosSondaggi::t('amossondaggi', 'Cancellato il'),
    'created_by' => AmosSondaggi::t('amossondaggi', 'Creato da'),
    'updated_by' => AmosSondaggi::t('amossondaggi', 'Aggiornato da'),
    'deleted_by' => AmosSondaggi::t('amossondaggi', 'Cancellato da'),
    'version' => AmosSondaggi::t('amossondaggi', 'Versione'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getSondaggiDomande()
    {
    return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiDomande::className(), ['id' => 'sondaggi_domande_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getSondaggiRispostePredefinite()
    {
    return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiRispostePredefinite::className(), ['id' => 'sondaggi_risposte_predefinite_id']);
    }
}
