<?php

use lispa\amos\sondaggi\AmosSondaggi;

/**
 * @var yii\web\View $this
 * @var lispa\amos\sondaggi\models\SondaggiDomandeTipologie $model
 */

$this->title = AmosSondaggi::t('amossondaggi', 'Aggiorna tipologia', [
    'modelClass' => 'Sondaggi Domande Tipologie',
]);
$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Tipologie domande'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = AmosSondaggi::t('amossondaggi', 'Aggiorna');
?>
<div class="sondaggi-domande-tipologie-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
