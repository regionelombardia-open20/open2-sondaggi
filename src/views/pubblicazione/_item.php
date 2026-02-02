<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\views\pubblicazione
 * @category   CategoryName
 */

/**
 * @var yii\web\View $this
 */
use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\helpers\Html;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\assets\ModuleSondaggiAsset;
use open20\design\utility\DateUtility;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\utility\SondaggiUtility;

/** @var AmosSondaggi $sondaggiModule */
$sondaggiModule = AmosSondaggi::instance();

$js = <<<JS

    $('.change_status_workflow').on('click', function(e) {
        e.preventDefault();
        var element =  $(this);
      var workflowId = '';
        var idModel = element.data('id');
        var str = element.attr('id');
        var state = str.split(/[\s-]+/).pop();
        if(state){
            $.ajax({
                url:"/sondaggi/ajax/change-status-session?id="+idModel+"&new_state="+state+"&modelObj=open20\\\\amos\\\\sondaggi\\\\models\\\\SondaggiRisposteSessioni",
                type: "GET",
                data: {},
                success:function(result){
                    location.reload();
                },
                error: function(richiesta,stato,errori){

                }
              });
        }
       return false;
   });

JS;

$this->registerJs($js, \yii\web\View::POS_READY);

ModuleSondaggiAsset::register($this);
/**
 * @var \open20\amos\sondaggi\models\Sondaggi $model
 */
$hideStatusPoll = (isset($hideStatusPoll)) ? $hideStatusPoll : false;
$dateStart = $model->publish_date;
$timeStart = DateUtility::getDate($dateStart, 'php:H:i');
$dayStart = DateUtility::getDate($dateStart, 'php:d');
$monthStart = DateUtility::getDate($dateStart, 'php:M');
$yearStart = DateUtility::getDate($dateStart, 'php:Y');
$dateStart = DateUtility::getDate($dateStart);
$dateEnd = $model->close_date;

$hideDateEnd = empty($model->close_date);
if (isset($dateEnd)) {
    $timeEnd = DateUtility::getDate($dateEnd, 'php:H:i');
    $dayEnd = DateUtility::getDate($dateEnd, 'php:d');
    $monthEnd = DateUtility::getDate($dateEnd, 'php:M');
    $yearEnd = DateUtility::getDate($dateEnd, 'php:Y');
    $dateEnd = DateUtility::getDate($dateEnd);
}

$moduleNameSondaggi = $moduleName ?: $this->context->module->id;

$canCompila = true;
$scope = \open20\amos\cwh\AmosCwh::getInstance()->getCwhScope();
if (isset($scope['community'])) {
    $communityModule = \Yii::$app->getModule('community');
    if ($communityModule) {
        $community = \open20\amos\community\models\Community::findOne($scope['community']);
        if (!\Yii::$app->user->isGuest && $community) {
            $count = \open20\amos\community\models\CommunityUserMm::find()
                    ->andWhere(['community_id' => $community->id, 'user_id' => \Yii::$app->user->id])
                    ->andWhere(['status' => \open20\amos\community\models\CommunityUserMm::STATUS_ACTIVE])
                    ->count();
            if ($count == 0) {
                $canCompila = false;
            }
        }
    }
}
?>

<div class="sondaggi-item-list-wrapper">
    <div class="card-wrapper">
        <div class="card">
            <div class="card-body border-bottom border-light px-0">                
                <div class="flexbox">
                    <div class="date-list">
                        <div class="content-date">
                            <div class="date-start">
                                <span class="small"><?= AmosSondaggi::t('amossondaggi', 'Aperto il') ?></span>
                                <span class="card-day bold lead"><?= $dayStart ?></span>
                                <span class="card-mounth bold"><?= $monthStart ?></span>
                                <span class="card-year"><?= $yearStart ?></span>
                                <span class="small mt-2">-</span>
                                <span class="small"><?= $timeStart ?></span>                            
                            </div>
                        </div>              
                    </div>
                    <div class="image-list">
                        <?php
                        $url = '/img/img_default.jpg';
                        if ($model->file) {
                            $url = $model->file->getUrl('dashboard_news');
                        }
                        ?>
                        <div class="img-sondaggio">
                            <?=
                            Html::img($url, [
                                'alt' => AmosSondaggi::t('amossondaggi', 'Immagine del sondaggio'),
                                'class' => 'img-responsive'
                            ]);
                            ?>
                        </div>

                    </div>                                   
                    <div class="description-list">
                        <div class="content-text-sondaggi">


                            <?php if (!$hideDateEnd) : ?>
                                <div class="date-end-poll">
                                    <div class="category-top small text-muted">
                                        <span class="font-weight-light"> <?= AmosSondaggi::t('amossondaggi', 'Fino al') . ' ' ?></span>
                                        <span class="card-day"><?= $dayEnd ?></span>
                                        <span class="card-month"><?= $monthEnd ?></span>
                                        <span class="card-year"><?= $yearEnd ?></span>
                                        <span class="card-year">- <?= $timeEnd ?></span>
                                    </div>
                                </div>
                            <?php endif ?>

                            <div class="list-title">
                                <?php if ($model->isCompilable() && \Yii::$app->user->can('COMPILA_SONDAGGIO', ['model' => $model]) && $canCompila) { ?>
                                    <a href="<?php
                                    $path = 'pubblicazione/compila';
                                    if (Yii::$app->controller->action->id == 'manage')
                                        $path = 'dashboard/dashboard';
                                    echo Yii::$app->urlManager->createUrl([
                                        '/' . AmosSondaggi::getModuleName() . '/' . $path,
                                        'id' => $model->id,
                                        'url' => $url,
                                    ])
                                    ?>" title="<?= $model->titolo ?>" class="link-list-title title-two-line">
                                        <h5 class="card-title font-weight-bold big-heading mb-2 "><strong><?= $model->titolo ?></strong></h5>
                                    </a>
                                <?php } else { ?>
                                    <h5 class="card-title font-weight-bold big-heading mb-2 "><strong><?= $model->titolo ?></strong></h5>
                                    <?php
                                }
                                if ($model->isCommunitySurvey()) {
                                    $community = \open20\amos\community\models\Community::findOne($model->community_id);
                                    if ($community && $community->name) {
                                        ?>
                                        <a href="javascript:void(0)" data-toggle="tooltip" title="dalla community <?= $community->name ?>">

                                            <span class="mdi mdi-account-supervisor-circle text-muted m-l-5"></span>

                                            <span class="sr-only"><?= $community->name ?></span>
                                        </a>
                                        <?php
                                    }
                                }
                                ?>

                                <div class="ml-auto">

                                    <?php
                                    if (
                                            \Yii::$app->getUser()->can('DASHBOARD_VIEW') ||
                                            (\Yii::$app->user->can('SONDAGGI_UPDATE', ['model' => $sondaggio]) && $sondaggio->status === \open20\amos\sondaggi\models\Sondaggi::WORKFLOW_STATUS_BOZZA) ||
                                            \Yii::$app->user->can('communityManagerAdminSondaggi', ['model' => $sondaggio])
                                    ):
                                        ?>
                                        <?php
                                        $labelDeleteConfirm = null;
                                        $actionDelete = '/sondaggi/sondaggi/delete?id=' . $model->id;
                                        $checkDeletePermission = true;
                                        $labelModify = \Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') ? AmosSondaggi::t('amossondaggi', 'Modifica') : AmosSondaggi::t('amossondaggi', 'Gestisci');
                                        if ($model->status == Sondaggi::WORKFLOW_STATUS_VALIDATO) {
                                            $labelDeleteConfirm = AmosSondaggi::t('amossondaggi', 'E\' necessario disattivare il sondaggio per procedere con l\'eliminazione. Procedere con la disattivazione del sondaggio?');
                                            $actionDelete = ['/sondaggi/pubblicazione/depubblica', 'idSondaggio' => $model->id, 'url' => '/sondaggi/dashboard/dashboard?id=' . $model->id];
                                            $checkDeletePermission = false;
                                        }
                                        ?>
                                        <?=
                                        ContextMenuWidget::widget([
                                            'model' => $model,
                                            'actionModify' => "/sondaggi/dashboard/dashboard?id=" . $model->id,
                                            'actionDelete' => $actionDelete,
                                            'labelDeleteConfirm' => $labelDeleteConfirm,
                                            'mainDivClasses' => '',
                                            'checkModifyPermission' => false,
                                            'checkDeletePermission' => $checkDeletePermission,
                                            'labelModify' => $labelModify
                                        ])
                                        ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                            if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGI_READ', ['model' => $model])) {
                                ?>
                                <div class="desc-poll text-muted">
                                    <p>
                                        <?php
                                        if (strlen($model->descrizione) > 300) {
                                            $stringCut = substr($model->descrizione, 0, 300);
                                            echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                        } else {
                                            echo $model->descrizione;
                                        }
                                        ?>
                                    </p>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="footer-list">
                                <?php if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGI_MANAGE') || \Yii::$app->getUser()->can('SONDAGGI_LETTORE_RISULTATI')) { ?>
                                    <span class="partecipanti-poll"><?= AmosSondaggi::t('amossondaggi', 'Partecipanti') . ':' ?><strong> <?= $model->getNumeroPartecipazioni() ?></strong></span>
                                    <?php
                                    if (!$hideStatusPoll || \Yii::$app->getUser()->can('SONDAGGI_LETTORE_RISULTATI')) {
                                        $statusLabel = '--';
                                        if ($model->hasWorkflowStatus()) {
                                            if ($model->getWorkflowStatus()->id == Sondaggi::WORKFLOW_STATUS_VALIDATO && SondaggiUtility::isTerminated($model)) {
                                                $statusLabel = AmosSondaggi::t('amossondaggi', Sondaggi::STATUS_CONCLUSO);
                                            } else {
                                                $statusLabel = AmosSondaggi::t('amossondaggi', $model->getWorkflowStatus()->getLabel());
                                            }
                                        }
                                        ?>
                                        <span class="status-poll"><?= AmosSondaggi::t('amossondaggi', 'Stato') ?>:
                                            <strong>
                                                <?= $statusLabel ?>
                                            </strong>
                                        </span>
                                    <?php } ?> 
                                    <?php
                                }
                                /** @var \open20\amos\sondaggi\models\search\SondaggiSearch $model */
                                $url = \yii\helpers\Url::current();
                                //if (\Yii::$app->getUser()->can('PARTECIPANTE') || TRUE) {
                                if (\Yii::$app->getUser()->can('RESPONSABILE_ENTE')) {
                                    echo Html::a(
                                            AmosSondaggi::tHtml('amossondaggi', '#assign_compiler'),
                                            '#',
                                            [
                                                'class' => 'assign-compiler',
                                                'title' => AmosSondaggi::t('amossondaggi', '#assign_compiler'),
                                                'data' => ['id' => $model->id]
                                            ]
                                    ) . '&nbsp;';
                                }
                                if ($model->isCompilable() && \Yii::$app->user->can('COMPILA_SONDAGGIO', ['model' => $model]) && $canCompila) {
                                    ?>
                                    <div class="read-more-container">
                                        <?php
                                        echo Html::a(
                                                AmosSondaggi::tHtml('amossondaggi', 'Partecipa'),
                                                Yii::$app->urlManager->createUrl([
                                                    '/' . $sondaggiModule->id . '/pubblicazione/compila',
                                                    'id' => $model->id,
                                                    'url' => $url,
                                                ]),
                                                [
                                                    'class' => 'read-more',
                                                    'title' => AmosSondaggi::t('amossondaggi', 'Partecipa'),
                                                ]
                                        );
                                        ?>
                                    </div>
                                <?php } elseif (\Yii::$app->getUser()->can('SONDAGGI_LETTORE_RISULTATI')) { ?>
                                    <div class="read-more-container">
                                        <?php
                                        $url = \yii\helpers\Url::current();
                                        echo Html::a(
                                                AmosSondaggi::tHtml('amossondaggi', 'View results'),
                                                Yii::$app->urlManager->createUrl([
                                                    '/' . $sondaggiModule->id . '/dashboard/dashboard',
                                                    'id' => $model->id,
                                                    'url' => $url
                                                ]),
                                                [
                                                    'class' => 'read-more',
                                                    'title' => AmosSondaggi::t('amossondaggi', 'View results'),
                                                ]
                                        );
                                        ?>
                                    </div>
                                <?php }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
