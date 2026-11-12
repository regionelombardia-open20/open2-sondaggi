<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\views\pubblicazione
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\forms\ItemAndCardHeaderWidget;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\assets\ModuleSondaggiAsset;
use open20\design\utility\DateUtility;

ModuleSondaggiAsset::register($this);
/**
 * @var \open20\amos\sondaggi\models\Sondaggi $model
 */
$hideStatusPoll = (isset($hideStatusPoll)) ? $hideStatusPoll : false;
$dateStart = $model->created_at;
$dayStart   = DateUtility::getDate($dateStart, 'php:d');
$monthStart = DateUtility::getDate($dateStart, 'php:M');
$yearStart  = DateUtility::getDate($dateStart, 'php:Y');
$dateStart  = DateUtility::getDate($dateStart);

$hideDateEnd = (isset($hideDateEnd) ? $hideDateEnd : false);
if (isset($dateEnd)) {
    $dayEnd   = DateUtility::getDate($dateEnd, 'php:d');
    $monthEnd = DateUtility::getDate($dateEnd, 'php:M');
    $yearEnd  = DateUtility::getDate($dateEnd, 'php:Y');
    $dateEnd  = DateUtility::getDate($dateEnd);
}

?>
<div class="sondaggi-item-list-wrapper">
    <div class="card-wrapper">
        <div class="card">
            <div class="card-body border-bottom border-light px-0">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="content-date-img">
                            <div class="date-start">

                                <span class="small"><?= AmosSondaggi::t('amossondaggi', 'APERTO IL') ?></span>
                                <span class="card-day bold lead"><?= $dayStart ?></span>
                                <span class="card-mounth bold"><?= $monthStart ?></span>
                                <span class="card-year"><?= $yearStart ?></span>
                            </div>
                            <?php
                            $url = '/img/img_default.jpg';
                            if (!is_null($model->filemanager_mediafile_id)) {
                                $url = $model->getAvatarUrl('medium');
                            };
                            ?>
                            <div class="img-sondaggio">
                                <?= Html::img($url, [
                                    'alt' => AmosSondaggi::t('amossondaggi', 'Immagine del sondaggio'),
                                    'class' => 'img-responsive'
                                ]); ?>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="content-text-sondaggi">
                            <?php if (!$hideDateEnd) : ?>
                                <div class="date-end-poll">
                                    <div class="category-top small text-muted">
                                        <span class="font-weight-light"> <?= AmosSondaggi::t('amossondaggi', 'Fino al') . ' ' ?></span>
                                        <span class="card-day"><?= $dayEnd ?></span>
                                        <span class="card-month"><?= $monthEnd ?></span>
                                        <span class="card-year"><?= $yearEnd ?></span>
                                    </div>
                                </div>
                            <?php endif ?>

                            <div class="list-title">
                                <a href="<?= $model->getFullViewUrl() ?>" title="<?= $model->titolo ?>" class="link-list-title title-two-line">
                                    <h5 class="card-title font-weight-bold big-heading mb-2 "><strong><?= $model->titolo ?></strong></h5>
                                </a>
                                <div class="ml-auto">


                                    <?php
                                    /** @var \open20\amos\sondaggi\models\search\SondaggiSearch $model */
                                    $url = \yii\helpers\Url::current();
                                    if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SondaggiValidate', ['model' => $model])) {
                                        if ($model->verificaSondaggioPubblicabile()) {
                                            if ($model->status == Sondaggi::WORKFLOW_STATUS_VALIDATO) {
                                                echo Html::a(AmosIcons::show('globe-alt', ['class' => 'm-t-5 m-r-10']), Yii::$app->urlManager->createUrl([
                                                    '/' . $this->context->module->id . '/pubblicazione/pubblica',
                                                    'idSondaggio' => $model->id,
                                                    'url' => $url
                                                ]), [
                                                    'data-confirm' => AmosSondaggi::t('amossondaggi', 'ATTENZIONE!!! La ripubblicazione del sondaggio sovrascriverà il vecchio, le risposte al precedente sondaggio non verranno cancellate, sei sicuro di voler continuare?'),
                                                    'title' => AmosSondaggi::t('amossondaggi', 'Ripubblica sondaggio'),
                                                    'data-toggle' => 'tooltip'
                                                ]);
                                            } else {
                                                echo Html::a(AmosIcons::show('globe-alt', ['class' => 'm-t-5 m-r-10']), Yii::$app->urlManager->createUrl([
                                                    '/' . $this->context->module->id . '/pubblicazione/pubblica',
                                                    'idSondaggio' => $model->id,
                                                    'url' => $url,
                                                ]));
                                            }
                                        } else {
                                            echo Html::a(AmosIcons::show('globe-alt', ['class' => 'm-t-5 m-r-10', 'style' => 'color:red;']), NULL, [
                                                'title' => AmosSondaggi::t('amossondaggi', 'Sondaggio non pubblicabile in quanto la sua configurazione non è corretta, verificare le pagine, le domande e le risposte predefinite.'),
                                                'data-confirm' => AmosSondaggi::t('amossondaggi', 'Sondaggio non pubblicabile in quanto la sua configurazione non è corretta, verificare le pagine, le domande e le risposte predefinite.'),
                                                'data-toggle' => 'tooltip'
                                            ]);
                                        }
                                    }
                                    ?>
                                    <?= ContextMenuWidget::widget([
                                        'model' => $model,
                                        'actionModify' => "/sondaggi/dashboard/dashboard?id=" . $model->id,
                                        'actionDelete' => "/sondaggi/sondaggi/delete?id=" . $model->id,
                                        'mainDivClasses' => ''
                                    ]) ?>
                                </div>
                            </div>
                            <div class="footer-list">
                                <span class="partecipanti-poll"><?= AmosSondaggi::t('amossondaggi', 'Partecipanti') . ':' ?><strong> <?= $model->getNumeroPartecipazioni() ?></strong></span>
                                <?php
                                if (!$hideStatusPoll) { ?>
                                    <span class="status-poll"><?= \Yii::t('amossondaggi', 'Stato') ?>:<strong> <?= $model->hasWorkflowStatus() ? $model->getWorkflowStatus()->getLabel() : '--'; ?></strong></span>
                                <?php }; ?>
                                <?php
                                /** @var \open20\amos\sondaggi\models\search\SondaggiSearch $model */
                                $url = \yii\helpers\Url::current();
                                //if (\Yii::$app->getUser()->can('PARTECIPANTE') || TRUE) {

                                if (!$model->hasCompilazioniSuperate()) { ?>
                                    <div class="read-more-container">

                                        <?php echo Html::a(
                                            AmosSondaggi::tHtml('amossondaggi', 'Partecipa'),
                                            Yii::$app->urlManager->createUrl([
                                                '/' . $this->context->module->id . '/pubblicazione/compila',
                                                'id' => $model->id,
                                                'url' => $url,
                                            ]),
                                            [
                                                'class' => 'read-more',
                                                'title' => AmosSondaggi::t('amossondaggi', 'Partecipa'),
                                            ]
                                        ); ?>
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