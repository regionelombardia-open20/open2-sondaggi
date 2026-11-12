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
?>
use open20\amos\core\helpers\Html;
/*
 * Personalizzare a piacimento la vista
 * $model è il model legato alla tabella del db
 * $buttons sono i tasti del template standard {view}{update}{delete}
 */
 
<div id="listViewListContainer">
    <div class="row">
        <div id="bk-listViewElementSondaggi-domande" class="col-xs-12 bk-listViewElementSondaggi-domande">
            <div class="col-xs-8 col-md-8">
                <h2><?= $model ?></h2>              
                <div class="bk-infoElementList">
                    <p>################# PERSONALIZZARE A PIACIMENTO L&#39;HTML ################</p>                    
                </div>
                <div class="col-xs-4 col-md-4">
                    <?= $buttons ?>
                </div>
                <div class="clear"></div>
            </div>
            <div class="col-xs-4 col-md-4">
                <p>### PERSONALIZZA ###</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>