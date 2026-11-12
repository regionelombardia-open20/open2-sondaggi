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
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;

/**
 * @var yii\web\View $this
 * @var open20\amos\sondaggi\models\search\SondaggiSearch $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="pubblicazione-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'class' => 'row'
    ]); ?>

    <div class="col-md-6"><?php echo $form->field($model, 'titolo') ?></div>

    <div class="col-md-6"><?php
    if (AmosSondaggi::instance()->enableCompilationWorkflow)
      echo $form->field($model, 'compilazioniStatus')
      ->dropdownList([
        null => AmosSondaggi::t('amossondaggi', '#all'),
        SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA => AmosSondaggi::t('amossondaggi', SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA),
        SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO => AmosSondaggi::t('amossondaggi', SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO),
        SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO => AmosSondaggi::t('amossondaggi', SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO)
      ]);
    ?></div>

    <?php // echo $form->field($model, 'sondaggi_id') ?>

    <?php // echo $form->field($model, 'ordinamento') ?>

    <?php // echo $form->field($model, 'sondaggi_domande_pagine_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'deleted_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'deleted_by') ?>

    <?php // echo $form->field($model, 'version') ?>

    <div class="form-group">
        <?= Html::submitButton(AmosSondaggi::tHtml('amossondaggi', 'Cerca'), ['class' => 'btn btn-navigation-primary']) ?>
        <?= Html::resetButton(AmosSondaggi::tHtml('amossondaggi', 'Reset'), ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
