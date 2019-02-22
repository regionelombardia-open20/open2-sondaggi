<?php

use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\views\DataProviderView;
use lispa\amos\sondaggi\AmosSondaggi;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \lispa\amos\sondaggi\models\search\SondaggiDomandePagineSearch $searchModel
 */
$this->title = AmosSondaggi::t('amossondaggi', 'Pagine dei sondaggi');
if ($url) {
    $this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => '/' . $this->context->module->id . '/sondaggi/index'];
    $this->title = AmosSondaggi::t('amossondaggi', 'Pagine del sondaggio');
    if (filter_input(INPUT_GET, 'idSondaggio')) {
        $this->title = AmosSondaggi::t('amossondaggi', 'Pagine del sondaggio: ' . lispa\amos\sondaggi\models\Sondaggi::findOne(['id' => filter_input(INPUT_GET, 'idSondaggio')])->titolo);
    }
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sondaggi-domande-pagine-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
        <?php /* echo         Html::a(AmosSondaggi::t('amossondaggi', 'Nuovo {modelClass}', [
          'modelClass' => 'Sondaggi Domande Pagine',
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
                        $url = '/img/img_default.jpg';

                        if ($model->file) {
                            $url = $model->file->getUrl('square_small');
                        }

                        return Html::img($url, [
                            'class' => 'gridview-image'
                        ]);
                    }
                ],
                'titolo',
                'descrizione:ntext',
                'sondaggi_id' => [
                    'attribute' => 'sondaggi_id',
                    'value' => function ($model) {
                        return $model->sondaggi['titolo'];
                    }
                ],
//            ['attribute'=>'created_at','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']], 
//            ['attribute'=>'updated_at','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']], 
//            ['attribute'=>'deleted_at','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']], 
//            'created_by', 
//            'updated_by', 
//            'deleted_by', 
//            'version', 
                [
                    'class' => 'lispa\amos\core\views\grid\ActionColumn',
                    'template' => '{update} {domande} {aggdomande} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiDomandePagineSearch $model */
                            $url = \yii\helpers\Url::current();
                            if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                return Html::a(AmosIcons::show('edit', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                    '/' . $this->context->module->id . '/sondaggi-domande-pagine/update',
                                    'id' => $model->id,
                                    'url' => $url,
                                ]), [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Modifica'),
                                    ]
                                );
                            } else {
                                return '';
                            }
                        },
                        'domande' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiDomandePagineSearch $model */
                            $url = \yii\helpers\Url::current();
                            if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                return Html::a(AmosIcons::show('collection-text', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                    '/' . $this->context->module->id . '/sondaggi-domande/index',
                                    'idSondaggio' => $model->getSondaggi()->one()['id'],
                                    'idPagina' => $model->id,
                                    'url' => $url,
                                ]), [
                                    'title' => AmosSondaggi::t('amossondaggi', 'Gestisci domande'),
                                ]);
                            } else {
                                return '';
                            }
                        },
                        'aggdomande' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiDomandePagineSearch $model */
                            $url = \yii\helpers\Url::current();
                            if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                return Html::a(AmosIcons::show('plus', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                    '/' . $this->context->module->id . '/sondaggi-domande/create',
                                    'idSondaggio' => $model->getSondaggi()->one()['id'],
                                    'idPagina' => $model->id,
                                    'url' => $url,
                                ]), [
                                    'title' => AmosSondaggi::t('amossondaggi', 'Aggiungi domanda'),
                                ]);
                            } else {
                                return '';
                            }
                        },
                        'delete' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiDomandePagineSearch $model */
                            $url = \yii\helpers\Url::current();
                            if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                return Html::a(AmosIcons::show('delete', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                    '/' . $this->context->module->id . '/sondaggi-domande-pagine/delete',
                                    'id' => $model->id,
                                    'idSondaggio' => $model->sondaggi_id,
                                    'url' => $url,
                                ]), [
                                    'title' => AmosSondaggi::t('amossondaggi', 'Cancella'),
                                ]);
                            } else {
                                return '';
                            }
                        },
                    ]
                ],
            ],
        ],
        /* 'listView' => [
          'itemView' => '_item'
          ],
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
    <?php
    if (isset($url)) :
        echo Html::a(AmosSondaggi::t('amossondaggi', 'Aggiungi pagina'), ['create', 'idSondaggio' => filter_input(INPUT_GET, 'idSondaggio'), 'idPagina' => filter_input(INPUT_GET, 'idPagina'), 'url' => yii\helpers\Url::current()], ['class' => 'btn btn-success']);
    endif;
    ?>
</div> 