<?php

use lispa\amos\sondaggi\AmosSondaggi;

/**
 * @var yii\web\View $this
 * @var lispa\amos\sondaggi\models\SondaggiRisposte $model
 */

$this->title = AmosSondaggi::t('amossondaggi', 'Create {modelClass}', [
    'modelClass' => 'Sondaggi Risposte',
]);
$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi Rispostes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sondaggi-risposte-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
