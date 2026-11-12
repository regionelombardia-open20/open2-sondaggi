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
use open20\amos\sondaggi\controllers\DashboardInvitationsController;
use open20\amos\sondaggi\models\SondaggiInvitations;

/** @var $target DashboardInvitationsController */
if ($target == SondaggiInvitations::TARGET_USERS) {
    $message = AmosSondaggi::t('amossondaggi', 'Sono stati trovati {n} utenti', ['n' => $count]);
}
else if ($target == SondaggiInvitations::TARGET_ORGANIZATIONS) {
    $message = AmosSondaggi::t('amossondaggi', '#organizations_found', ['n' => $count]);
}
?>

<div id="form-results" class="mb-5 p-4 neutral-1-bg-a1">
    <div class="row">
        <div class="col-xs-12">
            <div class="my-4 d-flex">
                <h5 class="font-weight-bold ">
                    <?= AmosSondaggi::t('amossondaggi', '#search_results') ?>
                </h5>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="save-inputs" class="col-xs-12">
            <p class="text-danger m-b-20">
                <strong>
                    <?= $message ?>
                </strong>
            </p>
        </div>
    </div>
</div>