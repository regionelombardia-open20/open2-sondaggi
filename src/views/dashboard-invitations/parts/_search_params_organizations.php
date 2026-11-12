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

use kartik\select2\Select2;
use open20\amos\sondaggi\models\search\SondaggiInvitationsSearch;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\core\icons\AmosIcons;

if ($count == 0) {
    $model->include_exclude = null;
    echo $form->field($model, 'include_exclude')->hiddenInput()->label(false);
    $model->field = null;
    echo $form->field($model, 'field')->hiddenInput()->label(false);
    $model->value = null;
    echo $form->field($model, 'value')->hiddenInput()->label(false);
}
?>
<?php for ($i = 1; $i <= $count; $i++) { ?>
    <div id="row-search-<?= $i ?>-organizations" class="row d-flex flex-wrap align-item-center">
        <div class="col-xs-12 col-sm-12 col-md-3">
            <?= $form->field($model, "include_exclude[$i]")->widget(Select2::className(), [
                'data' => [
                    SondaggiInvitationsSearch::FILTER_INCLUDE => AmosSondaggi::t('amossondaggi', '#invite'),
                    SondaggiInvitationsSearch::FILTER_EXCLUDE => AmosSondaggi::t('amossondaggi', "#no_invite")
                ],
                'options' => [
                    //                'placeholder' => AmosSondaggi::t('AmosSondaggi', "Select...")

                ]
            ])->label(AmosSondaggi::t('amossondaggi', '#rule')) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <?= $form->field($model, "field[$i]")->widget(Select2::className(), [
                'data' => SondaggiInvitationsSearch::getListOfAttributes(),
                'options' => [
                    'class' => 'filter-field',
                    'id' => 'field-' . $i . '-organizations',
                    'placeholder' => AmosSondaggi::t('amossondaggi', "#select")
                ]
            ])->label(AmosSondaggi::t('amossondaggi', '#field')) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <?php
                $typeValues = $model->field[$i] ? SondaggiInvitationsSearch::getAttributesValues('organizations', $model->field[$i])  : null;
                $output = null;
                if (!empty($typeValues))
                    foreach($typeValues as $item) {
                        $output[$item->id] = $item->name;
                    }
                $data = !empty($output) ? $output : null;
            ?>
            <?= $form->field($model, "value[$i]")->widget(\kartik\depdrop\DepDrop::className(), [
                'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                'data' => $data,
                'options' => [
                    'class' => 'filter-value',
                    'id' => 'value-' . $i,
                    //'multiple' => true
                ],
                'pluginOptions' => [
                    'url' => \yii\helpers\Url::to(['/sondaggi/dashboard-invitations/get-values', 'target' => 'organizations']),
                    'depends' => ['field-' . $i . '-organizations'],
                    'placeholder' => AmosSondaggi::t('amossondaggi', "#select"),
                ],
                'select2Options' => [
                    // 'options' => [
                    //     'multiple' => true,
                    // ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        // 'minimumInputLength' => 1,
                        // 'ajax' => [
                        //     'url' => \yii\helpers\Url::to(['/sondaggi/dashboard-invitations/get-values']),
                        //     'dataType' => 'json',
                        //     'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                        // ],
                    ],
                ]
            ])->label(AmosSondaggi::t('amossondaggi', '#value')); ?>
        </div>

        <div class="col-xs-2 col-sm-2 col-md-1 text-right">
            <?= \yii\helpers\Html::a(Amosicons::show('delete'), '', [
                'class' => 'btn btn-danger-inverse m-t-15',
                'id' => 'btn-remove-rule-organizations',
                'data' => $i
            ]) ?>

        </div>
    </div>
    <?= $form->field($model, 'sondaggi_id')->hiddenInput()->label(false) ?>
<?php } ?>