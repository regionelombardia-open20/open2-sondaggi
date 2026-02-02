<?php

namespace open20\amos\sondaggi\models\base;

use Yii;
use open20\amos\core\record\Record;

/**
 * This is the base-model class for table "sondaggi_risultati".
 *
 * @property integer $id
 * @property integer $excel_rows
 * @property integer $sondaggi_id
 * @property integer $only_completed
 * @property integer $sondaggi_risultati_formato_id
 * @property integer $task_manager_job_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property SondaggiRisultatiFormato $risultatiFormato
 */
class SondaggiRisultati extends Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sondaggi_risultati';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sondaggi_id', 'sondaggi_risultati_formato_id'], 'required'],
            [['sondaggi_id', 'sondaggi_risultati_formato_id', 'created_by', 'updated_by', 'deleted_by', 'task_manager_job_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['only_completed'], 'boolean'],
            [['excel_rows'], 'integer', 'max' => 1048576],
            [['excel_rows'], 'integer', 'min' => 1],
            [['excel_rows'], 'required', 'when' => function ($model) {
                return $model->sondaggi_risultati_formato_id == 1;
            }, 'whenClient' => "function (attribute, value) {
                return $(\"input:radio[name='SondaggiRisultati[sondaggi_risultati_formato_id]']:checked\").val() == '1';
            }"]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amossondaggi', 'ID'),
            'sondaggi_risultati_formato_id' => Yii::t('amossondaggi', '#risultati_formato_label'),
            'excel_rows' => Yii::t('amossondaggi', '#excel_rows_label_max_files'),
            'only_completed' => Yii::t('amossondaggi', '#only_completed'),
            'sondaggi_id' => Yii::t('amossondaggi', 'Sondaggi ID'),
            'task_manager_job_id' => Yii::t('amossondaggi', 'Task manager job ID'),
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
    public function getRisultatiFormato()
    {
        return $this->hasOne(\open20\amos\sondaggi\models\SondaggiRisultatiFormato::className(),
            ['id' => 'sondaggi_risultati_formato_id']);
    }
}