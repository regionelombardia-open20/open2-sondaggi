<?php

use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\views\DataProviderView;
use lispa\amos\sondaggi\AmosSondaggi;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\sondaggi\models\search\SondaggiSearch $searchModel
 */
$this->title = AmosSondaggi::t('amossondaggi', 'Compila sondaggi');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sondaggi-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
        <?php /* echo         Html::a(AmosSondaggi::t('amossondaggi', 'Nuovo {modelClass}', [
          'modelClass' => 'Sondaggi',
          ])        , ['create'], ['class' => 'btn btn-success']) */ ?>
    </p>

    <?php
    echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                //'id',
                'filemanager_mediafile_id' => [
                    'label' => 'Immagine',
                    'format' => 'html',
                    'value' => function ($model) {
                        /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                        $mediafile = \pendalf89\filemanager\models\Mediafile::findOne($model->filemanager_mediafile_id);
                        $url = '/img/img_default.jpg';
                        if ($mediafile) {
                            $url = $model->getAvatarUrl('medium');
                        }
                        return Html::img($url, [
                            'class' => 'gridview-image'
                        ]);
                    }
                ],
                'titolo:ntext',
                'descrizione:ntext',
                'compilazioni' => [
                    'label' => 'Partecipanti',
                    'value' => function ($model) {
                        /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                        return ($model->getNumeroPartecipazioni()) ? $model->getNumeroPartecipazioni() : 'Nessuno';
                    }
                ],
                //['attribute'=>'created_at','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
//            ['attribute'=>'updated_at','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
//            ['attribute'=>'deleted_at','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
//            'created_by',
//            'updated_by',
//            'deleted_by',
//            'version',
                [
                    'class' => 'lispa\amos\core\views\grid\ActionColumn',
                    'template' => '{compila}',
                    'buttons' => [
                        'anteprima' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                            $url = \yii\helpers\Url::current();
                            if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                return Html::a(AmosIcons::show('eye', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                    '/' . $this->context->module->id . '/sondaggi/view',
                                    'id' => $model->id,
                                    'url' => $url,
                                ]), [
                                    'title' => AmosSondaggi::t('amossondaggi', 'Visualizza anteprima'),
                                ]);
                            } else {
                                return '';
                            }
                        },
                        'compila' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                            $url = \yii\helpers\Url::current();
                            //if (\Yii::$app->getUser()->can('PARTECIPANTE') || TRUE) {
                            if ( !$model->hasCompilazioniSuperate() ) {
                                return Html::a(AmosIcons::show('spellcheck', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                    '/' . $this->context->module->id . '/pubblicazione/compila',
                                    'id' => $model->id,
                                    'url' => $url
                                ]), [
                                    'title' => AmosSondaggi::t('amossondaggi', 'Compila sondaggio'),
                                ]);
                            } else {
                                return '';
                            }
                        }
                    ]
                ],
            ],
        ],
        'listView' => [
            'itemView' => '_item'
        ],/*
          'iconView' => [
          'itemView' => '_icon'
          ],
          'mapView' => [
          'itemView' => '_map',
          'markerConfig' => [
          'lat' => 'domicilio_lat',
          'lng' => 'domicilio_lon',
          ]
          ],
          'calendarView' => [
          'itemView' => '_calendar',
          'clientOptions' => [
          //'lang'=> 'de'
          ],
          'eventConfig' => [
          //'title' => 'titoloEvento',
          //'start' => 'data_inizio',
          //'end' => 'data_fine',
          //'color' => 'coloreEvento',
          //'url' => 'urlEvento'
          ],
          ] */
    ]);
    ?>
</div>
