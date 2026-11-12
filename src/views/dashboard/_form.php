<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\views\sondaggi
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

use open20\amos\attachments\components\AttachmentsInput;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\AccordionWidget;
use open20\amos\core\forms\RequiredFieldsTipWidget;
use open20\amos\core\helpers\Html;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\assets\ModuleSondaggiPublicAsset;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\workflow\widgets\WorkflowTransitionButtonsWidget;
use open20\amos\workflow\widgets\WorkflowTransitionStateDescriptorWidget;
use yii\web\View;

// $wizardAsset = WizardEventAsset::register($this);
// $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);

/**
 * @var yii\web\View $this
 * @var open20\amos\sondaggi\models\Sondaggi $model
 * @var yii\widgets\ActiveForm $form
 * @var \open20\amos\cwh\AmosCwh $moduleCwh
 * @var array $scope
 */
ModuleSondaggiPublicAsset::register($this);

$postPublic = 'null';
if (isset($public)) {
    if (strlen($public)) {
        $postPublic = $public;
    }
}
$js = 'var publicPost = \'' . $postPublic . '\';';
$this->registerJs($js, View::POS_BEGIN);

$statusToRenderToHide = $model->getStatusToRenderToHide();

$standardId = \open20\amos\sondaggi\models\base\SondaggiTypes::SONDAGGI_TYPE_STANDARD;
$liveId = \open20\amos\sondaggi\models\base\SondaggiTypes::SONDAGGI_TYPE_LIVE;
$sondaggiModule = AmosSondaggi::instance();

$js2 = <<<JS

    var criteri = $('#abilita_criteri_valutazione-id').val();

    if(criteri == 1){
        $('#n_max_valutatori-id').prop('disabled', false);
    } else {
        $('#n_max_valutatori-id').val(0);
        $('#n_max_valutatori-id').prop('disabled', true);
    }

    $("#abilita_criteri_valutazione-id").change(function() {
        var criterio1 = $('#abilita_criteri_valutazione-id').val();

        if(criterio1 == 1){
            $('#n_max_valutatori-id').prop('disabled', false);
        } else {
            $('#n_max_valutatori-id').val(0);
            $('#n_max_valutatori-id').prop('disabled', true);
        }
    });

    var front = $('#sondaggi-frontend').val();
    if(front == 1){
        $('#sondaggi-thank_you_page').prop('disabled', false);
        $('#sondaggi-forza_lingua').prop('disabled', false);
        $('#sondaggi-abilita_registrazione').prop('disabled', false);
        $('#sondaggi-url_sondaggio_non_compilabile').prop('disabled', false);
        $('#compilabile_in_frontend').show();
        $('#no-frontend').hide();
        $('#si-frontend').show();
    } else {
        $('#sondaggi-thank_you_page').prop('disabled', true);
        $('#sondaggi-forza_lingua').prop('disabled', true);
        $('#sondaggi-abilita_registrazione').prop('disabled', true);
        $('#sondaggi-url_sondaggio_non_compilabile').prop('disabled', true);
        $('#compilabile_in_frontend').hide();
        $('#no-frontend').show();
        $('#si-frontend').hide();
    }

    var confermaCommunity = $("#sondaggi-mail_conf_community").val();
    if(confermaCommunity == 1){
        $("#mail_custom-conferma-community").show();
    } else {
        $("#mail_custom-conferma-community").hide();
    }

    $("#sondaggi-mail_conf_community").change(function() {
        var confermaCommunity = $("#sondaggi-mail_conf_community").val();
        if(confermaCommunity == 1){
            $("#mail_custom-conferma-community").show();
        } else {
            $("#mail_custom-conferma-community").hide();
        }
    });

    var mailNewAccountCustom = $("#sondaggi-mail_registrazione_custom").val();
    if(mailNewAccountCustom == 1){
        $("#mail_custom-nuovi-utenti").show();
    } else {
        $("#mail_custom-nuovi-utenti").hide();
    }

    $("#sondaggi-mail_registrazione_custom").change(function() {
    var mailNewAccountCustom = $("#sondaggi-mail_registrazione_custom").val();
        if(mailNewAccountCustom == 1){
            $("#mail_custom-nuovi-utenti").show();
        } else {
            $("#mail_custom-nuovi-utenti").hide();
        }
    });

    $('#sondaggi-frontend').change(function() {
        var front = $('#sondaggi-frontend').val();
        if(front == 1){
            $('#sondaggi-thank_you_page').prop('disabled', false);
            $('#sondaggi-forza_lingua').prop('disabled', false);
            $('#sondaggi-abilita_registrazione').prop('disabled', false);
            $('#sondaggi-url_sondaggio_non_compilabile').prop('disabled', false);
            $('#compilabile_in_frontend').show();
            $('#no-frontend').hide();
            $('#si-frontend').show();
        } else {
            $('#sondaggi-thank_you_page').prop('disabled', true);
            $('#sondaggi-forza_lingua').prop('disabled', true);
            $('#sondaggi-abilita_registrazione').prop('disabled', true);
            $('#sondaggi-url_sondaggio_non_compilabile').prop('disabled', true);
            $('#compilabile_in_frontend').hide();
            $('#no-frontend').show();
            $('#si-frontend').hide();
        }
   });

   $('#advanced-options-check').change(function() {
        if(this.checked){
            $('.container-advanced').show();
        } else {
            $('.container-advanced').hide();
        }
   });

    $('#sondaggio-type-id').on('select2:select', function(){
        hideShowSondaggiLive($(this).val());
    });


    function hideShowSondaggiLive(sondaggiotype){
          if(sondaggiotype == '$liveId'){
            $('.container-not-live').hide();
            $('.container-live').show();
        }else {
            $('.container-not-live').show();
            $('.container-live').hide();
        }
    }

    $('.container-advanced').hide();

    hideShowSondaggiLive($('#sondaggio-type-id').val());
JS;

$this->registerJs($js2, View::POS_READY);

$sondaggi_types = (new open20\amos\sondaggi\models\base\SondaggiTypes())->types;
?>

<?php
$form = ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]);

$dateTootlip = AmosSondaggi::t('amossondaggi', "Data impostata in automatico, è possibile in qualsiasi momento modificare tale data, ma in fase di pubblicazione sondaggio sarà necessario che la data di pubblicazione sia maggiore o uguale alla data in corso");
$iconDate = " <span  data-toggle=\"tooltip\" data-html=\"true\" data-placement=\"top\" title=\"$dateTootlip\">" . \open20\amos\core\icons\AmosIcons::show('info') . "</span>"; ?>

<!-- <?=
WorkflowTransitionStateDescriptorWidget::widget([
    'form' => $form,
    'model' => $model,
    'workflowId' => Sondaggi::WORKFLOW,
    'classDivIcon' => '',
    'classDivMessage' => 'message',
    'viewWidgetOnNewRecord' => false
]);
?> -->
<div class="row">

<div class="sondaggi-form col-xs-12">
    <div class="row">
        <div class="col-xs-12">
            <?= $form->field($model, 'titolo')->limitedCharsTextInput([
                  'maxlength' => 255,
                  'showCount' => false
            ])->hint(AmosSondaggi::t('amossondaggi', 'Max {x} caratteri', ['x' => 255])); ?>
            <?php
            $textTootlip = AmosSondaggi::t('amossondaggi', "#poll_type_description");
            $icon = " <span  data-toggle=\"tooltip\" data-html=\"true\" data-placement=\"top\" title=\"$textTootlip\">" . \open20\amos\core\icons\AmosIcons::show('info') . "</span>"; ?>
            <?php
            if (empty($model->sondaggio_type)) {
                $model->sondaggio_type = $standardId;
            } ?>
            <span class="hidden">
                <?= $form->field($model, 'sondaggio_type')->hiddenInput()->label(false);
                // $form->field($model, 'sondaggio_type')->widget(\kartik\select2\Select2::className(), [
                //     'data' => $sondaggi_types,
                //     'options' => [
                //         'id' => 'sondaggio-type-id',
                //         'placeholder' => AmosSondaggi::t('amossondaggi', "Seleziona...")
                //     ]
                // ])->label(AmosSondaggi::t('amossondaggi', "Tipologia sondaggio") . $icon);
                ?>
            </span>
            <?= $form->field($model, 'sottotitolo')->limitedCharsTextInput([
                    'maxlength' => 255,
                    'showCount' => false
            ])->hint(AmosSondaggi::t('amossondaggi', 'Max {x} caratteri', ['x' => 255])); ?>
            <?= $form->field($model, 'visualizza_solo_titolo')->checkbox() ?>
            <?= $form->field($model, 'descrizione')->textarea(['rows' => 4]) ?>
            <?= $form->field($model, 'customTags')->widget(\xj\tagit\Tagit::className(),
               [
                  'options' => [
                     'id' => 'custom-tags-id',
                     'placeholder' => AmosSondaggi::t('amossondaggi', '#insert_tag')
                  ],
               ])->label(AmosSondaggi::t('amossondaggi', '#tags')) ?>
        </div>
    </div>

    <div class="row container-live" style="display:none">
    <div class="col-xs-12">
    <h5><?= AmosSondaggi::t('amossondaggi', "Configurazioni sondaggi live") ?></h5>
    </div>

        <div class="col-md-6">
            <?= $form->field($model, 'begin_date_hour_live')->widget(\kartik\datecontrol\DateControl::className(), [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'end_date_hour_live')->widget(\kartik\datecontrol\DateControl::className(), [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME
            ]) ?>
        </div>
        <div class="col-md-6">
            <?php if (empty($model->graphics_live)) {
                $model->graphics_live = Sondaggi::SONDAGGI_LIVE_CHART_PIE;
            } ?>
            <?= $form->field($model, 'graphics_live')->widget(\kartik\select2\Select2::className(), [
                'data' => Sondaggi::sondaggiLiveTypeCharts(),

            ])->label(AmosSondaggi::t('amossondaggi', "Grafico di sintesi da mostare")) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'how_show_live')->widget(\kartik\select2\Select2::className(), [
                'data' => [
                    '1' => 'Dopo compilazione',
                    '2' => 'Sempre',
                ]

            ])->label(AmosSondaggi::t('amossondaggi', "#show_poll_stats_realtime"))?>
        </div>
    </div>

    <div class="row">
       <div class="col-md-6">
          <?php if (empty($model->publish_date)) $model->publish_date = (new \Datetime('+30 minutes'))->format('Y-m-d H:i:00'); ?>
          <?= $form->field($model, 'publish_date')->widget(\kartik\datecontrol\DateControl::className(), [
           'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME
       ])->label(AmosSondaggi::t('amossondaggi', "Data di pubblicazione sondaggio") . $iconDate) ?>
       </div>
       <div class="col-md-6">
           <?= $form->field($model, 'close_date')->widget(\kartik\datecontrol\DateControl::className(), [
            'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME
        ]) ?>
        </div>
    </div>
     <div class="row">
       <div class="col-md-6">
          <?php if ($sondaggiModule->enableAnonimousPoll) ?>
            <?= $form->field($model, 'anonimous_poll')->checkbox(['disabled' => (!($model->isNewRecord))]) ?>
       </div>
    </div>

    <div class="row">
        <div class="col-xs-12">

            <?=
            $form->field($model, 'file')->widget(AttachmentsInput::classname(),
                [
                    'options' => [// Options of the Kartik's FileInput widget
                        'multiple' => false, // If you want to allow multiple upload, default to false
                        'accept' => "image/*"
                    ],
                    'pluginOptions' => [// Plugin options of the Kartik's FileInput widget
                        'maxFileCount' => 1,
                        'showRemove' => false, // Client max files,
                        'indicatorNew' => false,
                        'allowedPreviewTypes' => ['image'],
                        'previewFileIconSettings' => false,
                        'overwriteInitial' => false,
                        'layoutTemplates' => false
                    ]
                ])->label(AmosSondaggi::t('amossondaggi', 'Immagine di copertina'))
            ?>

       </div>
     </div>

       <?php if ($sondaggiModule->enableAdvancedSettings) { ?>

        <div>

            <?=
            AccordionWidget::widget([
                'items' => [
                    [
                        'header' => AmosSondaggi::t('amossondaggi', "Avanzate"),
                        'content' => $this->render('views/_advanced', ['model' => $model, 'form' => $form, 'sondaggiModule' => $sondaggiModule]),
                    ]
                ],
                'headerOptions' => ['tag' => 'h2'],
                'clientOptions' => [
                    'collapsible' => true,
                    'active' => 'false',
                    'icons' => [
                        'header' => 'ui-icon-amos am am-plus-square',
                        'activeHeader' => 'ui-icon-amos am am-minus-square',
                    ]
                ],
            ]);
            ?>

        </div>
    <?php } ?>

    <?php if ($model->isNewRecord) { ?>
        <div style="display:none; width: 0px;">
            <?=
            \open20\amos\cwh\widgets\DestinatariPlusTagWidget::widget([
                'model' => $model,
                'moduleCwh' => $moduleCwh,
                'scope' => $scope
            ]);
            ?>
        </div>

        <span class="hidden">
            <?= $form->field($model, 'community_id')->hiddenInput([
                'value' => !empty($scope['community']) ? $scope['community'] : null
            ])->label(false) ?>
        </span>
    <?php } ?>

    <?= RequiredFieldsTipWidget::widget() ?>

    <?php
        if(
            Yii::$app->user->can('AMMINISTRAZIONE_SONDAGGI') ||
            (Yii::$app->user->can('CREATORE_SONDAGGI') && $model->status !== Sondaggi::WORKFLOW_STATUS_VALIDATO)
        ) :
    ?>

   <?= Html::submitButton($model->id ? AmosSondaggi::t('amossondaggi', 'Salva') : AmosSondaggi::t('amossondaggi', 'Crea'),
     ['class' => 'btn btn-primary pull-right']) ?>

    <!-- <?=
    WorkflowTransitionButtonsWidget::widget([
        'form' => $form,
        'model' => $model,
        'workflowId' => Sondaggi::WORKFLOW,
        'viewWidgetOnNewRecord' => true,
        'closeButton' => Html::a(AmosSondaggi::t('amossondaggi', 'Annulla'), \Yii::$app->request->referrer,
            ['class' => 'btn btn-secondary']),
        'initialStatusName' => "BOZZA",
        'initialStatus' => Sondaggi::WORKFLOW_STATUS_BOZZA,
        'statusToRender' => $statusToRenderToHide['statusToRender'],
        //gli utenti validatore/facilitatore o ADMIN possono sempre salvare il sondaggio => parametro a false altrimenti se stato VALIDATO => pulsante salva nascosto
        'hideSaveDraftStatus' => $statusToRenderToHide['hideDraftStatus'],
        'draftButtons' => [
            Sondaggi::WORKFLOW_STATUS_DAVALIDARE => [
                'button' => Html::submitButton(AmosSondaggi::t('amossondaggi', 'Salva'), ['class' => 'btn btn-workflow']),
                'description' => 'le modifiche e mantieni il sondaggio in "richiesta di pubblicazione"'
            ],
//            Sondaggi::WORKFLOW_STATUS_VALIDATO => [
//                'button' => Html::submitButton(AmosSondaggi::t('amossondaggi', 'Salva'), ['class' => 'btn btn-workflow']),
//                'description' => AmosSondaggi::t('amossondaggi', 'le modifiche e mantieni il sondaggio "pubblicato"'),
//            ],
            'default' => [
                'button' => Html::submitButton(AmosSondaggi::t('amossondaggi', 'Crea'),
                    ['class' => 'btn btn-primary']),
                'description' => AmosSondaggi::t('amossondaggi', 'potrai aggiungere pagine e domande in seguito'),
            ]
        ]
    ]);
    ?> -->

    <?php endif; ?>

    <?php ActiveForm::end(); ?>
</div>
</div>
