<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\views\pubblicazione
 * @category   CategoryName
 */

use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\forms\ItemAndCardHeaderWidget;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\models\Sondaggi;

/**
 * @var \open20\amos\sondaggi\models\Sondaggi $model
 */

?>

<div class="listview-container documents">
    <div class="post-horizonatal">
        <?= ItemAndCardHeaderWidget::widget([
            'model' => $model,
            'publicationDateField' => 'publication_date_begin',
            'publicationDateAsDateTime' => true,
        ]);
        ?>
        <div class="col-sm-7 col-xs-12 nop">
            <div class="post-content col-xs-12 nop">
                <div class="post-title col-xs-10">
                    <a href="<?= $model->getFullViewUrl() ?>">
                        <h2><?= $model->titolo ?></h2>
                    </a>
                </div>
                <?= ContextMenuWidget::widget([
                    'model' => $model,
                    'actionModify' => "/sondaggi/sondaggi/update?id=" . $model->id,
                    'actionDelete' => "/sondaggi/sondaggi/delete?id=" . $model->id,
                    'mainDivClasses' => 'col-xs-1 nop'
                ]) ?>
                <div class="clearfix"></div>
                <div class="row nom post-wrap">
                    <?php
                    $url = '/img/img_default.jpg';
                    if (!is_null($model->filemanager_mediafile_id)) :
                        $url = $model->getAvatarUrl('medium');
                        ?>
                        <div class="post-image-left nop col-sm-3 col-xs-12">
                            <?= Html::img($url, [
                                'alt' => AmosSondaggi::t('amossondaggi', 'Immagine del sondaggio')
                            ]); ?>
                        </div>
                    <?php
                    endif;
                    ?>
                    <div class="post-text m-b-15">
                        <p>
                            <?php
                            if (strlen($model->descrizione) > 300) {
                                $stringCut = substr($model->descrizione, 0, 300);
                                echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                            } else {
                                echo $model->descrizione;
                            }
                            ?>

                            <a class="underline" href="<?= $model->getFullViewUrl() ?>"><?= AmosSondaggi::tHtml('amossondaggi', 'Leggi tutto') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar col-sm-5 col-xs-12">
            <div class="container-sidebar">
                <div class="box">
                    <h4 class="title-sidebar-list"><?= AmosSondaggi::t('amossondaggi', 'Dettagli') ?></h4>
                    <p><strong><?= AmosSondaggi::t('amossondaggi', 'Partecipanti') . ':' ?></strong> <?= $model->getNumeroPartecipazioni() ?></p>
                    <p>
                        <strong><?= AmosSondaggi::t('amossondaggi', 'Stato') ?>:</strong> <?= $model->hasWorkflowStatus() ? $model->getWorkflowStatus()->getLabel() : '--'; ?>
                    </p>
                </div>

                <div class="box">
                    <h4 class="title-sidebar-list"><?= AmosSondaggi::t('amossondaggi', 'Azioni') ?></h4>
                    <div class="clearfix"></div>
                    <div class="sidebar-actions">
                        <ul>
                            <li>
                                <?php
                                /** @var \open20\amos\sondaggi\models\search\SondaggiSearch $model */
                                $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SondaggiValidate', ['model' => $model])) {
                                    if ($model->verificaSondaggioPubblicabile()) {
                                        if ($model->status == Sondaggi::WORKFLOW_STATUS_VALIDATO) {
                                            echo Html::a(AmosIcons::show('globe-alt', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                                '/' . $this->context->module->id . '/pubblicazione/pubblica',
                                                'idSondaggio' => $model->id,
                                                'url' => $url
                                            ]), ['data-confirm' => AmosSondaggi::t('amossondaggi', 'ATTENZIONE!!! La ripubblicazione del sondaggio sovrascriverà il vecchio, le risposte al precedente sondaggio non verranno cancellate, sei sicuro di voler continuare?'),
                                                'title' => AmosSondaggi::t('amossondaggi', 'Ripubblica sondaggio'),
                                            ]);
                                        } else {
                                            echo Html::a(AmosIcons::show('globe-alt', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                                '/' . $this->context->module->id . '/pubblicazione/pubblica',
                                                'idSondaggio' => $model->id,
                                                'url' => $url,
                                            ]));
                                        }
                                    } else {
                                        echo Html::a(AmosIcons::show('globe-alt', ['class' => 'btn btn-tool-secondary', 'style' => 'color:red;']), NULL, [
                                            'title' => AmosSondaggi::t('amossondaggi', 'Sondaggio non pubblicabile in quanto la sua configurazione non è corretta, verificare le pagine, le domande e le risposte predefinite.'),
                                            'data-confirm' => AmosSondaggi::t('amossondaggi', 'Sondaggio non pubblicabile in quanto la sua configurazione non è corretta, verificare le pagine, le domande e le risposte predefinite.'),
                                        ]);
                                    }
                                }
                                ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="col-xs-12 list-primary-btn">
            < ?= Html::a(AmosSondaggi::t('amossondaggi', 'Nuovo sondaggio'), ['create', 'url' => yii\helpers\Url::current()], ['class' => 'btn btn-success']); ?>
            < ?= Html::a(AmosSondaggi::t('amossondaggi', 'Nuovo sondaggio (Wizard)'), ['create'], ['class' => 'btn btn-success']); ?>
        </div> -->
    </div>
</div>
