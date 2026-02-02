<?php

namespace open20\amos\sondaggi\models\base;

use Yii;

/**
 * This is the base-model class for table "sondaggi_context".
 *
 * @property integer $id
 * @property integer $sondaggi_id
 * @property string $context_model_id
 * @property integer $context_id
 * @property string $context_url
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\sondaggi\models\Sondaggi $sondaggi
 */
class  SondaggiContext extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sondaggi_context';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sondaggi_id', 'context_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['context_model_id', 'context_url'], 'string', 'max' => 255],
            [['sondaggi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sondaggi::className(), 'targetAttribute' => ['sondaggi_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amossondaggi', 'ID'),
            'sondaggi_id' => Yii::t('amossondaggi', 'Sondaggi ID'),
            'context_model_id' => Yii::t('amossondaggi', 'Context Name'),
            'context_id' => Yii::t('amossondaggi', 'context id'),
            'context_url' => Yii::t('amossondaggi', 'Url'),
            'created_at' => Yii::t('amossondaggi', 'Creato il'),
            'updated_at' => Yii::t('amossondaggi', 'Aggiornato il'),
            'deleted_at' => Yii::t('amossondaggi', 'Cancellato il'),
            'created_by' => Yii::t('amossondaggi', 'Creato da'),
            'updated_by' => Yii::t('amossondaggi', 'Aggiornato da'),
            'deleted_by' => Yii::t('amossondaggi', 'Cancellato da'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggi()
    {
        return $this->hasOne(\open20\amos\sondaggi\models\Sondaggi::className(), ['id' => 'sondaggi_id']);
    }
}
