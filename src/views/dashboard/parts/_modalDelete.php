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

use open20\amos\core\icons\AmosIcons;
use open20\amos\sondaggi\AmosSondaggi;
use yii\helpers\Html;

?>
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel"><?= AmosSondaggi::t('amossondaggi', 'Eliminazione sondaggio') ?></h5>
            </div>
            <div class="modal-body">
                <?= AmosSondaggi::t('amossondaggi', 'E\' necessario disattivare il sondaggio per procedere con l\'eliminazione. Procedere con la disattivazione del sondaggio?') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="<?= AmosSondaggi::t('amossondaggi', 'Annulla') ?>"><?= AmosSondaggi::t('amossondaggi', 'Annulla') ?></button>
                <?= Html::a(
                        AmosIcons::show('minus-circle') . '&nbsp;' . AmosSondaggi::t('amossondaggi', 'Disattiva'),
                        ['/sondaggi/pubblicazione/depubblica',
                            'idSondaggio' => $model->id,
                            'url' => '/sondaggi/dashboard/dashboard?id=' . $model->id
                        ],
                        [
                            'class' => 'btn btn-danger',
                            'title' => AmosSondaggi::t('amossondaggi', 'Disattiva')
                        ]
                ); ?>
            </div>
        </div>
    </div>
</div>