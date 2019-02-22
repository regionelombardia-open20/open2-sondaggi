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
* This is the base-model class for table "sondaggi_risposte_predefinite".
*
    * @property integer $id
    * @property string $risposta
    * @property integer $sondaggi_domande_id
    * @property integer $ordinamento
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property integer $version
    *
            * @property \lispa\amos\sondaggi\models\SondaggiDomandeCondizionate[] $sondaggiDomandeCondizionates
            * @property \lispa\amos\sondaggi\models\SondaggiDomande $sondaggiDomande
    */
class SondaggiRispostePredefinite extends \lispa\amos\core\record\Record
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'sondaggi_risposte_predefinite';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['risposta'], 'string'],
            [['sondaggi_domande_id'], 'required'],
            [['sondaggi_domande_id', 'ordinamento', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe']
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => AmosSondaggi::t('amossondaggi', 'ID'),
    'risposta' => AmosSondaggi::t('amossondaggi', 'Risposta predefinita'),
    'sondaggi_domande_id' => AmosSondaggi::t('amossondaggi', 'Domanda'),
    'ordinamento' => AmosSondaggi::t('amossondaggi', 'Ordinamento'),
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
    public function getSondaggiDomandeCondizionates()
    {
    return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiDomandeCondizionate::className(), ['sondaggi_risposte_predefinite_id' => 'id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getSondaggiDomande()
    {
    return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiDomande::className(), ['id' => 'sondaggi_domande_id']);
    }
}
