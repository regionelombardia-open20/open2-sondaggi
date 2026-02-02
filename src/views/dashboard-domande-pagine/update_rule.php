<?php

use open20\amos\sondaggi\AmosSondaggi;

/**
 * @var yii\web\View $this
 * @var open20\amos\sondaggi\models\SondaggiDomandePagine $model
 */
$this->title = AmosSondaggi::t('amossondaggi', 'Aggiorna regola');
$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => ['/' . $this->context->module->id . '/sondaggi/index']];

$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Gestione regole'), 'url' => ['/' . $this->context->module->id . '/sondaggi-domande-pagine/rules', 'idSondaggio' => $idSondaggio]];

$this->params['breadcrumbs'][] = $this->title;
if (!AmosSondaggi::instance()->enableBreadcrumbs)
    $this->params['breadcrumbs'] = [];
?>
<div class="sondaggi-domande-pagine-update">
    <?=
    $this->render('_formRule', [
        'model' => $model,
        'url' => (isset($url)) ? $url : null,
        'idSondaggio' => $idSondaggio,
    ])
    ?>
</div>
