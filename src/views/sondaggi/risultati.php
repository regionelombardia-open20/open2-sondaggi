<?php

use yii\helpers\Html;
use lispa\amos\core\views\DataProviderView;
use yii\widgets\Pjax;
use lispa\amos\sondaggi\assets\ModuleRisultatiAsset;
use lispa\amos\sondaggi\components\GraficiGoogle;
use lispa\amos\sondaggi\models\SondaggiDomandePagine;
use lispa\amos\core\forms\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use lispa\amos\sondaggi\AmosSondaggi;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;

ModuleRisultatiAsset::register($this);

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\sondaggi\models\search\SondaggiSearch $searchModel
 */
$this->title = AmosSondaggi::t('amossondaggi', 'Risultati: ' . $model->titolo);
$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$funcResize = "";
for ($i = 0; $i < count($risposte); $i++) {
    $funcResize .= 'drawChartw' . $i . '();';
}

$this->registerJs('  
    jQuery(document).ready(function() {
    $(window).resize(function(){        
           ' . $funcResize . '           
    });   
  });
    ', yii\web\View::POS_END);
?>
<div class="sondaggi-risultati">     
    <div class="row">
        <div class="grafico-index">
            <div class="col-lg-12 menu-navigazione">
                <br>                
                <?=
                Html::a(AmosSondaggi::t('amossondaggi', '&nbsp;&nbsp;<strong><<</strong>&nbsp;&nbsp;'), (($idPagina != -1) ? ['risultati', 'id' => $model->id, 'idPagina' => -1, 'filter' => $filter] : NULL), ['class' => 'btn btn-success', 'disabled' => (($idPagina != -1) ? FALSE : TRUE)]) . "&nbsp;&nbsp;&nbsp;" .
                Html::a(AmosSondaggi::t('amossondaggi', '&nbsp;&nbsp;<strong><</strong>&nbsp;&nbsp;'), (($idPagina != -1) ? ['risultati', 'id' => $model->id, 'idPagina' => $paginaPrecedente, 'filter' => $filter] : NULL), ['class' => 'btn btn-success', 'disabled' => (($idPagina != -1) ? FALSE : TRUE)]) . "&nbsp;&nbsp;&nbsp;" .
                Html::a(AmosSondaggi::t('amossondaggi', '&nbsp;&nbsp;<strong>></strong>&nbsp;&nbsp;'), (($idPagina != 0) ? ['risultati', 'id' => $model->id, 'idPagina' => $prossimaPagina, 'filter' => $filter] : NULL), ['class' => 'btn btn-success', 'disabled' => (($idPagina != 0) ? FALSE : TRUE)]) . "&nbsp;&nbsp;&nbsp;" .
                Html::a(AmosSondaggi::t('amossondaggi', '&nbsp;&nbsp;<strong>>></strong>&nbsp;&nbsp;'), (($idPagina != 0) ? ['risultati', 'id' => $model->id, 'idPagina' => 0, 'filter' => $filter] : NULL), ['class' => 'btn btn-success', 'disabled' => (($idPagina != 0) ? FALSE : TRUE)]);
                ?>     
            </div>
            <?php if ($idPagina == -1): ?>
                <div class="col-lg-12">
                    <?php if ($tipo == 0): ?>
                        <h3><?= $model->titolo ?></h3>
                        <h4><?= $model->descrizione ?></h4>                                        
                        <?php
                        echo GraficiGoogle::widget([
                            'visualization' => 'ColumnChart',
                            'data' => $risposte,
                            //'scriptAfterChartInstantiate' => new \yii\web\JsExpression($script),
                            'options' => [
                                //'title' => '',
                                //'subtitle' => '',
                                //'titleTextStyle' => ['color' => 'red', 'fontSize' => 45],
                                //'is3D' => true,
                                'sliceVisibilityThreshold' => 0,
                                'showTip' => TRUE,
                                'legend' => ['position' => 'top'],
                                //'isStacked' => TRUE,
                                'orientation' => 'vertical',
                                'colors' => ['#8ec44e', '#3aa060', '#ea5c6f', '#0dc988', '#53cfc4', '#f8b439'],
                                /* 'slices' => [
                                  0 => ['color' => '#33adff'],
                                  1 => ['color' => '#ff33bb'],
                                  ], */
                                'height' => 700,
                                'hAxis' => ['title' => 'Numero dei partecipanti',
                                    'gridlines' => [
                                        'color' => null, //set grid line transparent                                    
                                    ]],
                                'vAxis' => ['title' => NULL, 'slantedText' => FALSE],
                            ]
                        ]);
                        ?> 
                    <?php elseif ($tipo == 1 || $tipo == 2): ?>
                        <h3><?= $model->titolo ?></h3>
                        <h4><?= $model->descrizione ?></h4> 
                        <?php
                        foreach ($risposte as $Risposta) {
                            echo GraficiGoogle::widget([
                                'visualization' => 'ColumnChart',
                                'data' => $Risposta,
                                //'scriptAfterChartInstantiate' => new \yii\web\JsExpression($script),
                                'options' => [
                                    //'title' => '',
                                    //'subtitle' => '',
                                    //'titleTextStyle' => ['color' => 'red', 'fontSize' => 45],
                                    //'is3D' => true,
                                    'sliceVisibilityThreshold' => 0,
                                    'showTip' => TRUE,
                                    'legend' => ['position' => 'top'],
                                    //'isStacked' => TRUE,
                                    'orientation' => 'vertical',
                                    'colors' => [$Risposta[1][2]],
                                    /* 'slices' => [
                                      0 => ['color' => '#33adff'],
                                      1 => ['color' => '#ff33bb'],
                                      ], */
                                    'height' => 700,
                                    'hAxis' => ['title' => 'Numero partecipanti',
                                        'gridlines' => [
                                            'color' => null, //set grid line transparent                                    
                                        ]],
                                    'vAxis' => ['title' => NULL, 'slantedText' => FALSE],
                                ]
                            ]);
                        } elseif ($tipo == 3 || $tipo == 4):
                        ?>
                        <h3><?= $model->titolo ?></h3>
                        <h4><?= $model->descrizione ?></h4> 
                        <?php
                        foreach ($risposte as $Risposta) {
                            if (isset($Risposta[0][0])) {
                                if ($Risposta[0][0] == 'Provincia') {
                                    if ($this->context->module->enableGeoChart) {
                                        ?>
                                        <h4>Accessi e compilazioni per provincia</h4>
                                        <?php
                                        echo GraficiGoogle::widget([
                                            'visualization' => 'GeoChart',
                                            'data' => $Risposta,
                                            //'scriptAfterChartInstantiate' => new \yii\web\JsExpression($script),
                                            'options' => [
                                                'region' => 'IT',
                                                'displayMode' => 'markers',
                                                'resolution' => 'provinces',
                                                'datalessRegionColor' => '#f5f5f5',
                                                'colorAxis' => [
                                                    'colors' => ['#8ec44e', '#3aa060',],
                                                ],
                                                'explorer' => [
                                                    'maxZoomIn' => 0.5,
                                                    'maxZoomOut' => 8
                                                ],
                                                'colors' => [0xFF8747, 0xFFB581, 0xc06000],
                                                /* 'sizeAxis' => [
                                                  'minValue' => 0,
                                                  'maxValue' =>  100
                                                  ], */
                                                //'title' => '',
                                                //'subtitle' => '',
                                                //'titleTextStyle' => ['color' => 'red', 'fontSize' => 45],
                                                //'is3D' => true,
                                                'sliceVisibilityThreshold' => 0,
                                                'showTip' => TRUE,
                                                'legend' => ['position' => 'top'],
                                                //'isStacked' => TRUE,
                                                //'orientation' => 'vertical',
                                                //'colors' => [$Risposta[1][2]],
                                                /* 'slices' => [
                                                  0 => ['color' => '#33adff'],
                                                  1 => ['color' => '#ff33bb'],
                                                  ], */
                                                'height' => 700,
                                            /* 'hAxis' => ['title' => 'Numero partecipanti',
                                              'gridlines' => [
                                              'color' => null, //set grid line transparent
                                              ]],
                                              'vAxis' => ['title' => NULL, 'slantedText' => FALSE], */
                                            ]
                                        ]);
                                    }
                                } else if ($Risposta[0][0] == 'Cognome') {
                                    $arrRisposta = $Risposta;
                                    array_shift($arrRisposta);
                                    echo \kartik\grid\GridView::widget([
                                        'dataProvider' => new ArrayDataProvider([
                                            'allModels' => $arrRisposta,
                                                ]),                                        
                                        'showPageSummary' => true,                                       
                                        'pjax' => true,                                        
                                        'striped' => true,
                                        'hover' => true,
                                        'panel' => ['type' => 'info', 'heading' => AmosSondaggi::t('amossondaggi', 'Report sui partecipanti al sondaggio')],
                                        'toggleDataOptions' => [
                                            'all' => [
                                                'label' => 'Tutto'
                                            ],
                                        ],
                                        'columns' => [
                                            'cognome' => [
                                                'class' => '\kartik\grid\DataColumn',
                                                'attribute' => 'cognome',
                                                'label' => AmosSondaggi::t('amossondaggi', 'Partecipante'),
                                                'value' => function ($model) {
                                                    return $model['cognome'] . ' ' . $model['nome'];
                                                },
                                            ],
                                            'begin_date' => [
                                                'class' => '\kartik\grid\DataColumn',
                                                'attribute' => 'begin_date',
                                                'label' => AmosSondaggi::t('amossondaggi', 'Data inizio'),
                                                'value' => function ($model) {
                                                    return $model['begin_date'];
                                                },
                                            ],
                                            'end_date' => [
                                                'class' => '\kartik\grid\DataColumn',
                                                'attribute' => 'end_date',
                                                'label' => AmosSondaggi::t('amossondaggi', 'Data fine'),
                                                'value' => function ($model) {
                                                    return $model['end_date'];
                                                },
                                            ],
                                            'dati' => [
                                                'class' => '\kartik\grid\DataColumn',
                                                'attribute' => 'dati',
                                                'label' => AmosSondaggi::t('amossondaggi', 'Dati'),
                                                'format' => 'html',
                                                'value' => function ($model) {
                                                    return '<strong>' . AmosSondaggi::t('amossondaggi', 'E-mail') . ': </strong>' . $model['email'] . '<br>' .
                                                            '<strong>' . AmosSondaggi::t('amossondaggi', 'Username') . ': </strong>' . $model['username'] . '<br>' .
                                                            '<strong>' . AmosSondaggi::t('amossondaggi', 'Telefono') . ': </strong>' . $model['telefono'] . '<br>' .
                                                            '<strong>' . AmosSondaggi::t('amossondaggi', 'Ruolo') . ': </strong>' . $model['role'] . '<br>';
                                                },
                                            ],
                                            'stato' => [
                                                'class' => '\kartik\grid\DataColumn',
                                                'attribute' => 'stato',
                                                'label' => AmosSondaggi::t('amossondaggi', 'Stato'),
                                                'value' => function ($model) {
                                                    return $model['stato'];
                                                },
                                                'hAlign' => 'center',
                                                'group' => true,
                                            ],
                                        ]
                                    ]);
                                } else {
                                    echo GraficiGoogle::widget([
                                        'visualization' => 'ColumnChart',
                                        'data' => $Risposta,
                                        //'scriptAfterChartInstantiate' => new \yii\web\JsExpression($script),
                                        'options' => [
                                            //'title' => '',
                                            //'subtitle' => '',
                                            //'titleTextStyle' => ['color' => 'red', 'fontSize' => 45],
                                            //'is3D' => true,
                                            'sliceVisibilityThreshold' => 0,
                                            'showTip' => TRUE,
                                            'legend' => ['position' => 'top'],
                                            //'isStacked' => TRUE,
                                            'orientation' => 'vertical',
                                            //'colors' => [$Risposta[1][2]],
                                            /* 'slices' => [
                                              0 => ['color' => '#33adff'],
                                              1 => ['color' => '#ff33bb'],
                                              ], */
                                            'height' => 700,
                                            'hAxis' => ['title' => 'Numero partecipanti',
                                                'gridlines' => [
                                                    'color' => null, //set grid line transparent                                    
                                                ]],
                                            'vAxis' => ['title' => NULL, 'slantedText' => FALSE],
                                        ]
                                    ]);
                                }
                            }
                        }
                    endif;
                    ?>
                </div>   
            <?php elseif ($idPagina == 0): ?>               
                <div class="col-lg-12">   
                    <h3><?= AmosSondaggi::t('amossondaggi', 'RISPOSTE LIBERE') ?></h3><hr>
                    <?php if ($risposte && $risposte->getCount()): ?>                   
                        <?=
                        \kartik\grid\GridView::widget([
                            'dataProvider' => $risposte,
                            //'layout' => "{items}\n{summary}\n{pager}",
                            //'showPageSummary' => true,
                            'striped' => true,
                            'hover' => true,
                            'panel' => ['type' => 'info', 'heading' => AmosSondaggi::t('amossondaggi', 'Risposte libere')],
                            'toggleDataOptions' => [
                                'all' => [
                                    'label' => 'Tutto'
                                ],
                            ],
                            'columns' => [
                                'pagina' => [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'pagina',
                                    'label' => 'Pagine',
                                    'value' => function ($model) {
                                        return $model['pagina'];
                                    },
                                    'group' => true,
                                ],
                                'descrizione' => [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'descrizione',
                                    'format' => 'html',
                                    'label' => 'Descrizioni',
                                    'value' => function ($model) {
                                        return $model['descrizione'];
                                    },
                                    //'hAlign' => 'center',
                                    'group' => true,
                                ],
                                'domanda' => [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'domanda',
                                    'label' => 'Domande',
                                    'value' => function ($model) {
                                        return $model['domanda'];
                                    },
                                    //'hAlign' => 'center',
                                    'group' => true,
                                ],
                                'risposta' => [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'risposta',
                                    'label' => 'Risposte',
                                    'value' => function ($model) {
                                        return $model['risposta'];
                                    },
                                ],
                            ]
                        ])
                        ?>
                    <?php else: ?>
                        <h4>Nessuna risposta libera per il sondaggio.</h4>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="col-lg-12">                
                    <?php
                    $ind = 0;
                    foreach ($domande->all() as $Domanda) {
                        ?>
                        <?php if ($ind == 0): ?>                    
                            <h3><?= $Domanda->sondaggiDomandePagine->titolo ?></h3>
                            <h4><?= $Domanda->sondaggiDomandePagine->descrizione ?></h4>                                     
                            <?php
                            $ind++;
                        endif;
                        ?>
                        <?php
                        if (!empty($risposte[$Domanda->id]) && count($risposte[$Domanda->id]) > 0):
                            echo GraficiGoogle::widget([
                                'visualization' => 'PieChart',
                                'data' => $risposte[$Domanda->id],
                                'options' => [
                                    'title' => $Domanda->domanda,
                                    //'subtitle' => 'Tutti i partecipanti, iscritti al primo giorno',
                                    //'titleTextStyle' => ['color' => 'red', 'fontSize' => 45],
                                    'is3D' => true,
                                    'showTip' => TRUE,
                                    'sliceVisibilityThreshold' => 0,
                                    //'isStacked' => FALSE,
                                    /* 'slices' => [
                                      0 => ['color' => '#8ec44e'],
                                      1 => ['color' => '#3aa060'],
                                      2 => ['color' => '#ea5c6f'],
                                      3 => ['color' => '#0dc988'],
                                      4 => ['color' => '#53cfc4'],
                                      ], */
                                    'height' => 500,
                                    'legend' => [
                                    // 'position' => 'none',
                                    ],
                                    'vAxis' => [
                                        'title' => 'Numero occorrenze',
                                        'gridlines' => [
                                            'color' => NULL  //set grid line transparent
                                        ]],
                                    'hAxis' => ['title' => 'Risposte'],
                                ]
                            ]);
                        else :
                            ?> 
                            <strong><?= $Domanda->domanda ?></strong>
                            <br>Non sono presenti risposte.<br><br>
                        <?php endif; ?>
                    <?php } ?>
                </div>   
            <?php endif; ?>
        </div> 
        <?php $form = ActiveForm::begin(); ?>
        <div class="col-lg-4"><?=
            $form->field($filter, 'data_inizio')->widget(DateControl::className(), [
                'type' => DateControl::FORMAT_DATE,
                'options' => [
                    'layout' => '{remove}{input}'
                ]
            ])->label('Data inizio')
            ?></div>
        <div class="col-lg-4"><?=
            $form->field($filter, 'data_fine')->widget(DateControl::className(), [
                'type' => DateControl::FORMAT_DATE,
                'options' => [
                    'layout' => '{remove}{input}'
                ]
            ])->label('Data fine')
            ?></div>

        <?php if ($model->getSondaggiPubblicaziones()->one()['tipologie_entita'] > 0) { ?> 
            <div class="col-lg-4"><?=
                $form->field($filter, 'area_formativa')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(\lispa\amos\tag\models\Tag::find()->andWhere(['root' => 1])->andWhere(['lvl' => 1])->asArray()->all(), 'id', 'nome'),
                    //'showToggleAll' => FALSE,
                    'options' => [
                        'placeholder' => AmosSondaggi::t('amossondaggi', 'Digita il nome dell\'Area formativa ...'),
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
                ?></div>                                           
            <div class="col-lg-4">
                <?php $nomeAtt = new \yii\db\Expression("concat('[', pei_entita_formative.codice_entita, '] ', pei_entita_formative.titolo) as titolo"); ?>
                <?=
                $form->field($filter, 'attivita')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(backend\modules\attivitaformative\models\PeiAttivitaFormative::find()
                                    ->innerJoin('pei_point_sedi as S', 'S.id = pei_entita_formative.pei_point_sedi_id')
                                    ->leftJoin('pei_point as PP', 'S.pei_point_id = PP.id')
                                    ->select(['pei_entita_formative.id as id', $nomeAtt])
                                    ->orderBy('titolo')
                                    ->asArray()->all(), 'id', 'titolo'),
                    'showToggleAll' => FALSE,
                    'options' => [
                        'placeholder' => AmosSondaggi::t('amossondaggi', 'Digita il nome o il codice dell\'Attività formativa ...'),
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
                ?></div>
            <div class="col-lg-8"><?= Html::submitButton('Cerca', ['class' => 'btn btn-success', 'style' => 'margin-top:25px;']); ?></div>
        <?php } else { ?>
            <div class="col-lg-4"><?= Html::submitButton('Cerca', ['class' => 'btn btn-success', 'style' => 'margin-top:25px;']); ?></div>      
        <?php } ?>

    </div>

    <?php ActiveForm::end(); ?>
</div>
<div class="col-lg-12 menu-sondaggio-chiudi">
    <?= Html::a(AmosSondaggi::t('amossondaggi', 'Chiudi'), ['index'], ['class' => 'btn btn-success']); ?>
</div>
