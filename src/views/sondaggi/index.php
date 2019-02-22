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
$this->title = AmosSondaggi::t('amossondaggi', 'Sondaggi');
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
                        $url = '/img/img_default.jpg';

                        if ($model->file) {
                            $url = $model->file->getUrl('square_small');
                        }

                        return Html::img($url, [
                            'class' => 'gridview-image'
                        ]);
                    }
                ],
                'titolo:ntext',
                'descrizione:ntext',
                'partecipazioni' => [
                    'label' => 'Partecipanti',
                    'value' => function ($model) {
                        /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                        return ($model->getNumeroPartecipazioni()) ? $model->getNumeroPartecipazioni() : 'Nessuno';
                    }
                ],
                'sondaggi_stato_id' => [
                    'attribute' => 'sondaggi_stato_id',
                    'value' => function ($model) {
                        /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                        return $model->sondaggiStato->descrizione;
                    },
                    'label' => 'Stato'
                ],
                'pubblico' => [
                    'label' => 'Tipologia',
                    'value' => function ($model) {
                        /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                        if (!is_array($model->getSondaggiPubblicaziones()->one()['ruolo'])) {
                            if ($model->getSondaggiPubblicaziones()->one()['ruolo'] == 'PUBBLICO') {
                                if ($model->getSondaggiPubblicaziones()->one()['tipologie_entita'] > 0) {
                                    return 'Pubblico per attività';
                                } else {
                                    return 'PUBBLICO';
                                }
                            }
                        }
                        return 'Riservato';
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
                    'template' => '{update} {pagine} {domande} {delete} {risultati}',
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
                        'risultati' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                            $url = \yii\helpers\Url::current();
                            $partecipazioni = $model->getNumeroPartecipazioni();
                            if (\Yii::$app->getUser()->can('SONDAGGI_READ') && $partecipazioni) {
                                return Html::a(AmosIcons::show('bar-chart', ['class' => 'btn btn-tool-secondary'], 'dash'), Yii::$app->urlManager->createUrl([
                                    '/' . $this->context->module->id . '/sondaggi/risultati',
                                    'id' => $model->id,
                                ]), [
                                    'title' => AmosSondaggi::t('amossondaggi', 'Risultati del sondaggio'),
                                ]);
                            } else {
                                return '';
                            }
                        },
                        'update' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                            $url = \yii\helpers\Url::current();
                            if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                return Html::a(AmosIcons::show('edit', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                    '/' . $this->context->module->id . '/sondaggi/update',
                                    'id' => $model->id,
                                    'url' => $url,
                                ]), [
                                    'title' => AmosSondaggi::t('amossondaggi', 'Modifica intestazione'),
                                ]);
                            } else {
                                return '';
                            }
                        },
                        'pagine' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                            $url = \yii\helpers\Url::current();
                            if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                return Html::a(AmosIcons::show('collection-item', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                    '/' . $this->context->module->id . '/sondaggi-domande-pagine/index',
                                    'idSondaggio' => $model->id,
                                    'url' => $url,
                                ]), [
                                    'title' => 'Gestisci pagine',
                                ]);
                            } else {
                                return '';
                            }
                        },
                        'domande' => function ($url, $model) {
                            /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                            if ($model->getSondaggiDomandePagines()->count() == 0) {
                                $url = Yii::$app->urlManager->createUrl(['/' . $this->context->module->id . '/sondaggi-domande-pagine/index', 'idSondaggio' => $model->id, 'url' => yii\helpers\Url::current()]);

                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                    return Html::a(AmosIcons::show('plus', ['class' => 'btn btn-tool-secondary', 'style' => 'color:red;']), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/sondaggi-domande-pagine/create',
                                        'idSondaggio' => $model->id,
                                        'url' => $url,
                                    ]), [
                                        'title' => 'Aggiungi pagina - Nessuna pagina ancora presente',
                                    ]);
                                } else {
                                    return '';
                                }
                            } else {
                                $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                    if ($model->getSondaggiDomandes()->count() == 0) {
                                        $url = Yii::$app->urlManager->createUrl(['/' . $this->context->module->id . '/sondaggi-domande-pagine/index', 'idSondaggio' => $model->id, 'url' => yii\helpers\Url::current()]);
                                        return Html::a(AmosIcons::show('playlist-plus', ['class' => 'btn btn-tool-secondary', 'style' => 'color:red;']), Yii::$app->urlManager->createUrl([
                                            '/' . $this->context->module->id . '/sondaggi-domande/index',
                                            'idSondaggio' => $model->id,
                                            'url' => $url,
                                        ]), [
                                            'title' => 'Gestisci domande - E\' necessario aggiungere delle domande al sondaggio.',
                                        ]);
                                    } else {
                                        $url = Yii::$app->urlManager->createUrl(['/' . $this->context->module->id . '/sondaggi-domande-pagine/index', 'idSondaggio' => $model->id, 'url' => yii\helpers\Url::current()]);
                                        return Html::a(AmosIcons::show('playlist-plus', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                            '/' . $this->context->module->id . '/sondaggi-domande/index',
                                            'idSondaggio' => $model->id,
                                            'url' => $url,
                                        ]), [
                                            'title' => 'Gestisci domande',
                                        ]);
                                    }
                                } else {
                                    return '';
                                }
                            }
                        },
                    ]
                ],
            ],
        ],
         'listView' => [
          'itemView' => '_item'
          ],
        /*  'iconView' => [
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
