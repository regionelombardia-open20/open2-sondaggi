<?php

namespace open20\amos\sondaggi\models\base;

use Yii;
use open20\amos\core\record\Record;

/**
 * This is the base-model class for table "sondaggi_risultati_formato".
 *
 * @property integer $id
 * @property string $label
 * @property string $code
 * @property integer $excel_rows
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class SondaggiRisultatiFormato extends Record
{
    const FORMAT_CODE_EXCEL = 'excel';
    const FORMAT_CODE_PDF = 'pdf';
    const FORMAT_EXCEL = 1;
    const FORMAT_PDF = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sondaggi_risultati_formato';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at', 'label', 'code'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amossondaggi', 'ID'),
            'created_at' => Yii::t('amossondaggi', 'Creato il'),
            'updated_at' => Yii::t('amossondaggi', 'Aggiornato il'),
            'deleted_at' => Yii::t('amossondaggi', 'Cancellato il'),
            'created_by' => Yii::t('amossondaggi', 'Creato da'),
            'updated_by' => Yii::t('amossondaggi', 'Aggiornato da'),
            'deleted_by' => Yii::t('amossondaggi', 'Cancellato da'),
        ];
    }
}