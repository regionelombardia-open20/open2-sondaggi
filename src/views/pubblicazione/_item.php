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

/**
 * @var \open20\amos\sondaggi\models\Sondaggi $model
 */

?>

<div class="listview-container documents">
    <div class="post-horizonatal">
        <?= ItemAndCardHeaderWidget::widget([
            'model' => $model,
            'publicationDateField' => 'created_at',
        ]); ?>
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
                        <strong><?= \Yii::t('amossondaggi', 'Stato') ?>:</strong> <?= $model->hasWorkflowStatus() ? $model->getWorkflowStatus()->getLabel() : '--'; ?>
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
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGI_READ', ['model' => $model])) {
                                    echo Html::a(AmosIcons::show('eye', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/sondaggi/view',
                                        'id' => $model->id,
                                        'url' => $url,
                                    ]), [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Visualizza anteprima'),
                                    ]);
                                }
                                ?>
                            </li>
                            <li>
                                <?php
                                /** @var \open20\amos\sondaggi\models\search\SondaggiSearch $model */
                                $url = \yii\helpers\Url::current();
                                //if (\Yii::$app->getUser()->can('PARTECIPANTE') || TRUE) {

                                if (!$model->hasCompilazioniSuperate()) {
                                    echo Html::a(AmosIcons::show('spellcheck', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/pubblicazione/compila',
                                        'id' => $model->id,
                                        'url' => $url,
                                    ]), [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Compila sondaggio'),
                                    ]);
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
