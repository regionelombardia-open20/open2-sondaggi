<?php

use lispa\amos\sondaggi\AmosSondaggi;

/**
 * @var yii\web\View $this
 * @var lispa\amos\sondaggi\models\Sondaggi $model
 */
$this->title = AmosSondaggi::t('amossondaggi', 'Aggiorna sondaggio');
$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = AmosSondaggi::t('amossondaggi', 'Aggiorna');
?>
<div class="sondaggi-update">
    <?=
    $this->render('_form', [
        'model' => $model,
        'public' => isset($public) ? $public : NULL,
    ])
    ?>
</div>
