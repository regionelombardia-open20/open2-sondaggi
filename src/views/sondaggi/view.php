<?php

use lispa\amos\sondaggi\AmosSondaggi;
use kartik\detail\DetailView;

/**
 * @var yii\web\View $this
 * @var lispa\amos\sondaggi\models\Sondaggi $model
 */

$this->title = $model;
$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sondaggi-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'titolo:ntext',
            'descrizione:ntext',
            'sondaggi_stato_id' => [
                'attribute' => 'sondaggi_stato_id',
                'value' => $model->sondaggiStato->descrizione,
            ]
            //'filemanager_mediafile_id',
            /* [
              'attribute'=>'created_at',
              'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
              ],
              [
              'attribute'=>'updated_at',
              'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
              ],
              [
              'attribute'=>'deleted_at',
              'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
              ],
              'created_by',
              'updated_by',
              'deleted_by',
              'version', */
        ],
    ]) ?>
</div>
