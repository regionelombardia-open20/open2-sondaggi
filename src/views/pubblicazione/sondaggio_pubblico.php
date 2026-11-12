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

use open20\amos\sondaggi\AmosSondaggi;

if ($libero) {
    $link = "/" . $this->context->module->id . "/pubblicazione/sondaggi-pubblici";
    $testoLink = "Sondaggi pubblici";
    $quest = open20\amos\sondaggi\models\Sondaggi::findOne($id);
    $breadcrumb = $quest->titolo;
    $this->title = $breadcrumb;
    $descrizione = $quest->descrizione;
} else { //TODO PER ENTITA' SPECIFICHE
    $link = "/" . $this->context->module->id . "/pubblicazione/sondaggio-pubblico-attivita";
    $testoLink = "Sondaggio di gradimento";
    $quest = open20\amos\sondaggi\models\Sondaggi::findOne($id);
    $breadcrumb = 'DA CONFIGURARE';//backend\modules\attivitaformative\models\PeiAttivitaFormative::findOne(['codice_attivita' => $attivita])->titolo;
    $this->title = $breadcrumb;
    $descrizione = $quest->descrizione;
}
?>
<!--<div class="container">
    <nav role="navigation" aria-label="breadcrumbs" aria-labelledby="bc-title" id="bc">
        <h5 id="bc-title" class="vis-off">Sei qui:</h5>
        <ol class="breadcrumb">
            <li><a href="/site/index">Home</a></li>  
            <li><a href="< ? =$link;?>">< ? =$testoLink;?></a></li>  
            <li class="active">< ? = $breadcrumb ?></li>
        </ol>
    </nav>
</div>-->
<main role="main" id="mainContent">
    <div class="container">
        <div class="page" role="contentinfo">
            <h1><?= $this->title; ?></h1>
            <div class="sondaggi-compilazione marginTB">
                <?= $this->render('/q' . $id . '/Pagina_' . $idPagina, [
                    'model' => $model,
                    'idSessione' => $idSessione,
                    'idPagina' => $idPagina,
                    'attivita' => $attivita,
                    'libero' => $libero,
                    'utente' => NULL
                ]) ?>
            </div>
        </div>
    </div>
</main>
