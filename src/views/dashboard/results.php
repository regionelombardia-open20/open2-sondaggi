<?php

use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\CloseSaveButtonWidget;
use open20\amos\core\views\DataProviderView;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\helpers\Html;
use open20\amos\taskmanager\models\TaskManagerJob;

/**
 * @var \open20\amos\sondaggi\models\Sondaggi $model
 * @var array $formatoChoices
 * @var \open20\amos\sondaggi\models\SondaggiRisultati $risultatiModel
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 * @var string $urlBC
 */

$this->title = AmosSondaggi::t('amossondaggi', "#download_participants");
if(\Yii::$app->user->can('SONDAGGI_LETTORE_RISULTATI')) {
    $this->params['forceBreadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => $urlBC];
    $this->params['forceBreadcrumbs'][] = ['label' => $model->titolo, 'url' => ['dashboard/dashboard', 'id' => $model->id]];
    $this->params['forceBreadcrumbs'][] = ['label' => $this->title];
}
$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => ['sondaggi/manage']];
$this->params['breadcrumbs'][] = $this->title;
if (!AmosSondaggi::instance()->enableBreadcrumbs) {
    $this->params['breadcrumbs'] = [];
    $this->params['forceBreadcrumbs'] = [];
}



$js = <<<JS

let selector = $('#sondaggi-risultati-formato-id input[type=radio]:checked');
splitResultsSection(selector.val());

$('#sondaggi-risultati-formato-id input[type=radio]').change(function (e) {
    splitResultsSection(this.value);
});

// Mostra o nasconde la sezione DIVIDI I RISULTATI, va mostrata se selezionata la voce EXCEL
function splitResultsSection(v) {
    if (v == 2) {
        $('#excel-rows-id').prop("disabled", true);
        $('.section-split-results').fadeOut();
    } else if(v == 1){
        $('#excel-rows-id').prop("disabled", false);
        $('.section-split-results').fadeIn();
    }
}

JS;

$this->registerJs($js);

$css = "
.large-names {
    word-wrap: break-word;
    max-width: 250px;
}";

$this->registerCss($css);

?>

<div class="sondaggi-results">

    <div class="sondaggi-results-list">
        <?php
        echo DataProviderView::widget([
            'dataProvider' => $dataProvider,
            'currentView' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt') . Html::tag('p', AmosSondaggi::tHtml('amossondaggi', 'Tabella')),
                'url' => '?currentView=grid'
            ],
            'gridView' => [
                'columns' => [
                    'status' => [
                        'label' => 'Stato',
                        'value' => function ($model) {
                            /** @var \open20\amos\sondaggi\models\search\SondaggiRisultatiSearch $model */
                            /** @var \open20\amos\taskmanager\TaskManager $taskManager */
                            $taskManager = Yii::$app->getModule('taskmanager');
                            $job = $taskManager->getJob($model->task_manager_job_id);
                            switch ($job['status']) {
                                case TaskManagerJob::STATUS_SCHEDULED:
                                    return 'Programmato';
                                case TaskManagerJob::STATUS_STARTED:
                                    return 'In corso';
                                case TaskManagerJob::STATUS_COMPLETED:
                                    return 'Completato';
                                case TaskManagerJob::STATUS_FAILED:
                                    return 'In errore';
                                default:
                                    return '';
                            }
                        }
                    ],
                    'request' => [
                        'label' => 'Richiesto il',
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime($model->created_at, 'humanalwaysdatetime');
                        }
                    ],
                    'executed' => [
                        'label' => 'Eseguito il',
                        'value' => function ($model) {
                            /** @var \open20\amos\sondaggi\models\search\SondaggiRisultatiSearch $model */
                            /** @var \open20\amos\taskmanager\TaskManager $taskManager */
                            $job = TaskManagerJob::findOne(['id' => $model->task_manager_job_id]);
                            return $job['start_execution_time']? Yii::$app->formatter->asDatetime($job['start_execution_time'], 'humanalwaysdatetime'): '';
                        }
                    ],
                    'applicant' => [
                        'label' => 'Richiedente',
                        'value' => function ($model) {
                            /** @var \open20\amos\sondaggi\models\search\SondaggiRisultatiSearch $model */
                            /** @var \open20\amos\admin\models\UserProfile $up */
                            $up = \open20\amos\admin\models\UserProfile::findOne(['user_id' => $model->created_by]);
                            return $up->nomeCognome;
                        }
                    ],
                    'file' => [
                        'label' => 'Nome file',
                        'format' => 'html',
                        'value' => function ($model) {
                            /** @var \open20\amos\sondaggi\models\search\SondaggiRisultatiSearch $model */
                            /** @var \open20\amos\taskmanager\TaskManager $taskManager */
                            $job = TaskManagerJob::findOne(['id' => $model->task_manager_job_id]);
                            if ($job->output && $job->status == TaskManagerJob::STATUS_COMPLETED) {
                                return $job['filename'] ? ("<p class='large-names'>" . basename($job['filename']) . "</p>") : '';
                            }
                            return '';
                        }
                    ],
                    [
                        'class' => 'open20\amos\core\views\grid\ActionColumn',
                        'template' => '{download}',
                        'buttons' => [
                            'download' => function ($url, $model) {
                                /** @var \open20\amos\sondaggi\models\search\SondaggiRisultatiSearch $model */
                                /** @var \open20\amos\taskmanager\TaskManager $taskManager */
                                /** @var TaskManagerJob $job */
                                $job = TaskManagerJob::findOne(['id' => $model->task_manager_job_id]);
//                                \yii\helpers\VarDumper::dump($job->output,2,1);
                                if ($job->output && ($job->status == TaskManagerJob::STATUS_COMPLETED)) {
                                    return Html::a('<span class="glyphicon glyphicon-download"></span>',
                                        [$job->output->getUrl()], ['title' => 'Scarica']);
                                }
                                return '';
                            },

                        ]
                    ],
                ],
            ],
        ]);

        ?>
    </div>

    <hr class="m-t-30">

    <div class="sondaggi-results-form">
        <h3>Generazione risultati del sondaggio:<br><?= $model->titolo ?></h3>
        <?php
        $form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data'
            ]
        ]);
        ?>

        <div class="col-xs-12">
            <?= $form->field($risultatiModel, 'sondaggi_risultati_formato_id')
                ->radioList($formatoChoices, [
                    'id' => 'sondaggi-risultati-formato-id'
                ]) ?>
        </div>

        <div class="col-xs-12 section-split-results" style="display: none;">
            <h3>Dividi i risultati</h3>
            <?= $form->field($risultatiModel, 'excel_rows')->textInput([
                'type' => 'number',
                'id' => 'excel-rows-id',
                'value' => $risultatiModel->excel_rows? $risultatiModel->excel_rows: '1000',
            ]) ?>
        </div>

        <div class="col-xs-12">
            <h3>Opzioni</h3>
            <?= $form->field($risultatiModel, 'only_completed')->checkbox() ?>
        </div>

        <div class="col-xs-12">
            <?= CloseSaveButtonWidget::widget([
                'model' => $risultatiModel,
                'buttonNewSaveLabel' => AmosSondaggi::t('amossondaggi', 'Avvia generazione risultati'),
                'buttonSaveLabel' => AmosSondaggi::t('amossondaggi', 'Avvia generazione risultati'),
                'closeButtonLabel' => AmosSondaggi::t('amossondaggi', 'Chiudi'),
                'urlClose' => ['/sondaggi/pubblicazione/all']
            ]);
            ?>
        </div>

        <?php
        ActiveForm::end();
        ?>
    </div>

</div>