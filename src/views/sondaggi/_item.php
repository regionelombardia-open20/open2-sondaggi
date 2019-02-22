<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti
 * @category   CategoryName
 */

use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\forms\ItemAndCardHeaderWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\sondaggi\AmosSondaggi;

/**
 * @var \lispa\amos\sondaggi\models\Sondaggi $model
 */

?>

<div class="listview-container documents">
    <div class="post-horizonatal">
        <?php
        $creatoreDocumenti = $model->getCreatedUserProfile()->one();
        $dataPubblicazione = Yii::$app->getFormatter()->asDatetime($model->created_at);
        $nomeCreatoreDocumenti = AmosSondaggi::tHtml('amossondaggi', 'Utente Cancellato');
        ?>
        <?= ItemAndCardHeaderWidget::widget([
            'model' => $model,
            'publicationDateField' => 'created_at',
        ]);
        ?>
        <div class="col-sm-7 col-xs-12 nop">
            <div class="post-content col-xs-12 nop">
                <div class="post-title col-xs-10">
                    <a href="/sondaggi/sondaggi/view?id=<?= $model->id ?>">
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

                    if ($model->file) {
                        $url = $model->file->getUrl('original');
                    }
                    ?>
                    <div class="post-image-left nop col-sm-3 col-xs-12">
                        <?= Html::img($url, [
                            'alt' => AmosSondaggi::t('amossondaggi', 'Immagine del sondaggio'),
                            'class' => 'img-responsive'
                        ]); ?>
                    </div>
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

                            <a class="underline" href=/sondaggi/sondaggi/view?id=<?= $model->id ?>><?= AmosSondaggi::tHtml('amossondaggi', 'Leggi tutto') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar col-sm-5 col-xs-12">
            <div class="container-sidebar">
                <div class="box">
                    <h4 class="title-sidebar-list"> Dettagli </h4>
                    <p><strong>Partecipanti:</strong> <?= $model->getNumeroPartecipazioni() ?></p>
                    <p><strong>Stato:</strong> <?= $model->sondaggiStato->descrizione ?></p>
                    <p><strong>Tipologia:</strong>
                        <?php
                        /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                        if (!is_array($model->getSondaggiPubblicaziones()->one()['ruolo'])) {
                            if ($model->getSondaggiPubblicaziones()->one()['ruolo'] == 'PUBBLICO') {
                                if ($model->getSondaggiPubblicaziones()->one()['tipologie_entita'] > 0) {
                                    echo 'Pubblico per attività';
                                } else {
                                    echo 'PUBBLICO';
                                }
                            } else if ($model->getSondaggiPubblicaziones()->one()['tipologie_entita'] == NULL) {
                                echo 'Riservato';
                            }
                        }

                        ?>

                    </p>


                </div>

                <div class="box">
                    <h4 class="title-sidebar-list"> Azioni </h4>
                    <div class="clearfix"></div>
                    <div class="sidebar-actions">
                        <ul>
                            <li>
                                <?php
                                $url = \yii\helpers\Url::current();
                                $partecipazioni = $model->getNumeroPartecipazioni();
                                if (\Yii::$app->getUser()->can('SONDAGGI_READ') && $partecipazioni) {
                                    echo Html::a(AmosIcons::show('bar-chart', ['class' => 'btn btn-tool-secondary'], 'dash'), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/sondaggi/risultati',
                                        'id' => $model->id,
                                        //'url' => $url,
                                    ]), [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Risultati del sondaggio'),
                                    ]);
                                }
                                ?>
                            </li>
                            <li>
                                <?php
                                /** @var \lispa\amos\sondaggi\models\search\SondaggiSearch $model */
                                $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                    echo Html::a(AmosIcons::show('collection-item', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/sondaggi-domande-pagine/index',
                                        'idSondaggio' => $model->id,
                                        'url' => $url,
                                    ]), [
                                        'title' => 'Gestisci pagine',
                                    ]);
                                }
                                ?>
                            </li>
                            <li>
                                <?php
                                $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                    if ($model->getSondaggiDomandes()->count() == 0) {
                                        $url = Yii::$app->urlManager->createUrl(['/' . $this->context->module->id . '/sondaggi-domande-pagine/index', 'idSondaggio' => $model->id, 'url' => yii\helpers\Url::current()]);
                                        echo Html::a(AmosIcons::show('playlist-plus', ['class' => 'btn btn-tool-secondary', 'style' => 'color:red;']), Yii::$app->urlManager->createUrl([
                                            '/' . $this->context->module->id . '/sondaggi-domande/index',
                                            'idSondaggio' => $model->id,
                                            'url' => $url,
                                        ]), [
                                            'title' => 'Gestisci domande - E\' necessario aggiungere delle domande al sondaggio.',
                                        ]);
                                    } else {
                                        $url = Yii::$app->urlManager->createUrl(['/' . $this->context->module->id . '/sondaggi-domande-pagine/index', 'idSondaggio' => $model->id, 'url' => yii\helpers\Url::current()]);
                                        echo Html::a(AmosIcons::show('playlist-plus', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                            '/' . $this->context->module->id . '/sondaggi-domande/index',
                                            'idSondaggio' => $model->id,
                                            'url' => $url,
                                        ]), [
                                            'title' => 'Gestisci domande',
                                        ]);
                                    }
                                }
                                ?>
                            </li>
                            <li>
                                <?php $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                    echo Html::a(AmosIcons::show('delete', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/sondaggi/delete',
                                        'id' => $model->id,
                                        'url' => $url,
                                    ]), [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Cancella'),
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
