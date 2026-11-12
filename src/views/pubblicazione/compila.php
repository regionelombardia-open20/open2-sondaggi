<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-sondaggi
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

use open20\amos\sondaggi\AmosSondaggi;

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\views\pubblicazione
 * @category   CategoryName
 */
/**
 * @var $model
 * @var $idSessione
 * @var $idPagina
 * @var $utente
 */
$arrayModelRisposte = [];
foreach ((Array) $risposteWithFiles as $rispostaWithFile) {
    $nomeVariabileDomanda = 'file_' . $rispostaWithFile->sondaggi_domande_id;
    $arrayModelRisposte [$nomeVariabileDomanda] = $rispostaWithFile;
}
if (AmosSondaggi::instance()->customBreadCrumbs) {
    $this->params['forceBreadcrumbs'][] = ['label' => $this->title];
}

?>

<?php if (isset($id) && isset($idPagina)) { ?>
    <div class="sondaggi-compilazione container sondaggi-compilazione-sondaggio<?= $id ?>">
        <?=
        $this->render('@common/uploads/' . $this->context->module->id . '/pubblicazione/views/q' . $id . '/Pagina_' . $idPagina, \yii\helpers\ArrayHelper::merge([
                    'model' => $model,
                    'idSessione' => $idSessione,
                    'idPagina' => $idPagina,
                    'utente' => $utente,
                    'ultimaPagina' => $ultimaPagina,
                    'url' => $url,
                    'read' => $read,
                    'useSession' => $useSession,
                    'primaPagina' => $primaPagina,
                    'paginaPrec' => $paginaPrec,
                        ], $arrayModelRisposte))
        ?>
    </div>

    <?php
} else {

    $this->params['breadcrumbs'][] = $titolo;
    if (!AmosSondaggi::instance()->enableBreadcrumbs)
        $this->params['breadcrumbs'] = [];
    ?>

<?php } ?>
