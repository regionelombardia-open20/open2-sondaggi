<?php

use lispa\amos\core\icons\AmosIcons;
use lispa\amos\sondaggi\AmosSondaggi;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\sondaggi\models\search\SondaggiSearch $searchModel
 * @var \yii\db\ActiveQuery $pubblicazioni
 */

$this->title = AmosSondaggi::t('amossondaggi', 'Sondaggio terminato');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="sondaggi-index text-center">
    <?php
    if (!empty($pubblicazioni->one()->text_end_title) && strlen(trim($pubblicazioni->one()->text_end_title))) {
        ?>
        <?= AmosIcons::show('check-circle', [
            'class' => 'am-4 success m-t-15'
        ]) ?>
        <h2 class="p-t-5 nom-b"><?= $pubblicazioni->one()->text_end_title ?></h2>
        <?php
    } else {
        ?>
        <?= AmosIcons::show('check-circle', [
            'class' => 'am-4 success m-t-15'
        ]) ?>
        <h2 class="p-t-5 nom-b"><?= AmosSondaggi::t('amossondaggi', 'CONGRATULAZIONI!!!') ?></h2>
    <?php } ?>

    <?php
    if (!empty($pubblicazioni->one()->text_end) && strlen(trim($pubblicazioni->one()->text_end))) {
        if ($pubblicazioni->one()->text_end_html == 1) {
            ?>
            <?= $pubblicazioni->one()->text_end ?>
            <?php
        } else {
            ?>
            <h3><?= $pubblicazioni->one()->text_end ?></h3>
            <?php
        }
    } else {
        ?>
        <h3><?= AmosSondaggi::tHtml('amossondaggi', 'Sondaggio completato.') ?></h3>
    <?php } ?>
    <?= Html::a(AmosSondaggi::t('amossondaggi', 'Chiudi'), Url::previous(), [
        'class' => 'btn btn-secondary undo-edit mr10'
    ]); ?>
</div>
