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
use open20\amos\core\icons\AmosIcons;
use open20\amos\sondaggi\AmosSondaggi;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\sondaggi\models\search\SondaggiSearch $searchModel
 * @var \yii\db\ActiveQuery $pubblicazioni
 */
$this->title                   = AmosSondaggi::t('amossondaggi', 'Sondaggio terminato');
$this->params['breadcrumbs'][] = $this->title;
if (!AmosSondaggi::instance()->enableBreadcrumbs) $this->params['breadcrumbs'] = [];
if (!empty($forzato)) {
    \Yii::$app->language = $forzato;
}
?>
<div class="sondaggi-index text-center sondaggi-success my-5 m-b-20">
    <?php
    if (!empty($sondaggio->titolo_fine_sondaggio_front) && strlen(trim($sondaggio->titolo_fine_sondaggio_front))) {
        ?>
        <?=
        AmosIcons::show('check-circle', [
            'class' => 'am-4 success m-t-30'
        ])
        ?>
        <h2 class="mb-3"><?= $sondaggio->titolo_fine_sondaggio_front ?></h2>
        <?php
    } else {
        ?>
        <?=
        AmosIcons::show('check-circle', [
            'class' => 'am-4 success m-t-30'
        ])
        ?>
        <h2 class="mb-3"><?= AmosSondaggi::tHtml('amossondaggi', 'Grazie per aver compilato il sondaggio.')
        ?></h2>
        <?php } ?>

    <?php
    if (!empty($sondaggio->testo_fine_sondaggio_front) && strlen(trim($sondaggio->testo_fine_sondaggio_front))) {
        if ($pubblicazioni->one()->text_end_html == 1) {
            ?>
            <?= $sondaggio->testo_fine_sondaggio_front ?>
            <?php
        } else {
            ?>
            <h3><?= \Yii::$app->formatter->asNtext($sondaggio->testo_fine_sondaggio_front) ?></h3>
            <?php
        }
    } else {
        if ($sondaggio->frontend != 1):
        ?>
        <h3 class="m-b-30"><?=
            AmosSondaggi::tHtml('amossondaggi', 'Ti abbiamo inviato un’email di riepilogo con tutte le informazioni.')
            ?></h3>
        <?php
        endif;
    }

    $closeUrl = trim($sondaggio->url_chiudi_sondaggio);
    ?>
    <?=
    Html::a(AmosSondaggi::t('amossondaggi', 'Chiudi'), (!empty($closeUrl) && AmosSondaggi::instance()->enableRedirectionUrl) ? 'http://' . $closeUrl: Url::previous(),
        [
        'class' => 'btn btn-secondary undo-edit m-t-30'
    ]);
    ?>
</div>
