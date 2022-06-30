<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    cruscottolavoro\activities\views\email
 * @category   CategoryName
 */

use open20\amos\core\helpers\Html;
use open20\amos\notificationmanager\utility\NotifyUtility;
use open20\amos\notificationmanager\widgets\ItemAndCardWidgetEmailSummaryWidget;
use open20\amos\sondaggi\AmosSondaggi;

/**
 * @var \open20\amos\sondaggi\models\Sondaggi[] $arrayModels
 * @var string $color
 */

$colors = NotifyUtility::getColorNetwork($color);

?>
<?php foreach ($arrayModels as $model): ?>
    <?php
    $modelTitle = $model->getTitle();
    $modelAbsoluteFullViewUrl = Yii::$app->urlManager->createAbsoluteUrl($model->getFullViewUrl());
    ?>
    <tr>
        <td colspan="2" style="padding-bottom:10px;">
            <table width="100%">
                <tr>
                    <td valigh="top" style="font-size:18px; font-weight:bold; font-family: sans-serif; text-align:left; vertical-align:top;">
                        <p style="margin:0 0 5px 0">
                            <?= Html::a($modelTitle, $modelAbsoluteFullViewUrl, [
                                'style' => 'color: #000; text-decoration:none;',
                                'title' => $modelTitle
                            ]) ?>
                        </p>
                        <p style="font-size:13px; color:#7d7d7d; padding:10px 0; font-family: sans-serif; font-weight:normal; margin:0; text-align: left;"><?= $model->getDescription(true) ?></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:0 0 10px 0; border-bottom:1px solid #D8D8D8;">
                        <table width="100%">
                            <tr>
                                <td width="400" style="text-align:left;">
                                    <table width="100%">
                                        <tr>
                                            <?= ItemAndCardWidgetEmailSummaryWidget::widget(['model' => $model]); ?>
                                        </tr>
                                    </table>
                                </td>
                                <td align="right" width="85" valign="bottom" style="text-align: center; padding-left: 10px;">
                                    <a href="<?= $modelAbsoluteFullViewUrl ?>"
                                       style="background: <?= $colors[1] ?>; border:3px solid <?= $colors[1] ?>; color: #ffffff; font-family: sans-serif; font-size: 11px; line-height: 22px; text-align: center; text-decoration: none; display: block; font-weight: bold; text-transform: uppercase; padding:1px"
                                       class="button-a">
                                        <!--[if mso]>&nbsp;&nbsp;&nbsp;&nbsp;<![endif]--><?= AmosSondaggi::t('amossondaggi', 'Partecipa'); ?><!--[if mso]>&nbsp;&nbsp;&nbsp;&nbsp;<![endif]-->
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php endforeach; ?>
