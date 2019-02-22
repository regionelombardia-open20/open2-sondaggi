<?php

/**
 * @var $model
 * @var $idSessione
 * @var $idPagina
 * @var $utente
 */

?>
<div class="sondaggi-compilazione">
    <?= $this->render('@backend/' . $this->context->module->id . '/pubblicazione/views/q' . $id . '/Pagina_' . $idPagina, [
        'model' => $model,
        'idSessione' => $idSessione,
        'idPagina' => $idPagina,
        'utente' => $utente,
    ]) ?>
</div>
