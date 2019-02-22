<?php

use lispa\amos\sondaggi\AmosSondaggi;

/**
 * @var yii\web\View $this
 * @var lispa\amos\sondaggi\models\Sondaggi $model
 */

$this->title = AmosSondaggi::t('amossondaggi', 'Inserisci sondaggio');
$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sondaggi-create">
    <?=
    $this->render('_form', [
        'model' => $model,
        'url' => (isset($url)) ? $url : NULL,
        'public' => isset($public) ? $public : NULL,
    ])
    ?>
</div>
