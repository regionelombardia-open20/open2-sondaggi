<?php

use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use lispa\amos\core\forms\CreatedUpdatedWidget;
use lispa\amos\core\forms\RequiredFieldsTipWidget;
use lispa\amos\core\forms\TextEditorWidget;
use lispa\amos\sondaggi\AmosSondaggi;
use lispa\amos\sondaggi\assets\ModuleSondaggiPublicAsset;
use lispa\amos\sondaggi\models\SondaggiStato;
use kartik\select2\Select2;
use yii\bootstrap\Tabs;
use yii\helpers\ArrayHelper;

ModuleSondaggiPublicAsset::register($this);

/**
 * @var yii\web\View $this
 * @var lispa\amos\sondaggi\models\Sondaggi $model
 * @var yii\widgets\ActiveForm $form
 */

$postPublic = 'null';
if (isset($public)) {
    if (strlen($public)) {
        $postPublic = $public;
    }
}
$js = 'var publicPost = \'' . $postPublic . '\';';
$this->registerJs($js, yii\web\View::POS_BEGIN);
?>

<div class="sondaggi-form col-xs-12">

    <?php
    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
    ?>

    <?php $this->beginBlock('generale'); ?>
    <div class="row">
        <div class="col-sm-8">
            <?= $form->field($model, 'titolo')->textarea(['rows' => 2]) ?>
            <?= $form->field($model, 'descrizione')->textarea(['rows' => 4]) ?>
        </div>

        <div class="col-sm-4">
            <div class="col-lg-8 col-sm-8 pull-right">
                <?= $form->field($model,
                    'file')->widget(\lispa\amos\attachments\components\AttachmentsInput::classname(), [
                    'options' => [ // Options of the Kartik's FileInput widget
                        'multiple' => false, // If you want to allow multiple upload, default to false
                        'accept' => "image/*"
                    ],
                    'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                        'maxFileCount' => 1,
                        'showRemove' => false,// Client max files,
                        'indicatorNew' => false,
                        'allowedPreviewTypes' => ['image'],
                        'previewFileIconSettings' => false,
                        'overwriteInitial' => false,
                        'layoutTemplates' => false
                    ]
                ])->label(AmosSondaggi::t('amosnews', 'Immagine')) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-sm-6">
            <?= $form->field($model, 'compilazioni_disponibili')->textInput() ?>
        </div>
        <div class="col-lg-6 col-sm-6">
            <?php if (Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')): ?>
                <?php if ($model->sondaggi_stato_id == 3): ?>
                    <?= $form->field($model, 'sondaggi_stato_id')->dropDownList(ArrayHelper::map(SondaggiStato::find()->all(), 'id', 'descrizione')) ?>
                <?php else: ?>
                    <?= $form->field($model, 'sondaggi_stato_id')->dropDownList(ArrayHelper::map(SondaggiStato::find()->andWhere(['!=', 'id', 3])->all(), 'id', 'descrizione')) ?>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($model->isNewRecord): ?>
                    <?php $model->sondaggi_stato_id = 1;
                    ?>
                    <input type="hidden" name="sondaggi_stato_id" value="1">
                <?php else: ?>
                    <input type="hidden" name="sondaggi_stato_id"
                           value="<?= ($model->sondaggi_stato_id) ? $model->sondaggi_stato_id : 1; ?>"
                <?php endif; ?>
                <?= $form->field($model, 'sondaggi_stato_id')->dropDownList(ArrayHelper::map(SondaggiStato::find()->all(), 'id', 'descrizione'), ['disabled' => true]) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <h4><?= AmosSondaggi::tHtml('amossondaggi', 'Regole di pubblicazione') ?></h4>
            <?php
            if ($model->isNewRecord) {
                $model->pubblico = 1;
            } else if (!isset($model->pubblico)) {
                $model->pubblico = ($model->getSondaggiPubblicaziones()->one()['ruolo'] == 'PUBBLICO') ? 1 : 0;
            }
            ?>
            <?= $form->field($model, 'pubblico')->dropDownList([1 => 'Pubblica', 0 => 'Riservata agli utenti'], ['id' => 'sondaggio-pubblico'])->label('Tipologia Pubblicazione'); ?>
            <?php
            $model->tipologie_entita = 0;
            if ($model->getSondaggiPubblicaziones()->count() > 0) {
                $dati = [];
                foreach ($model->getSondaggiPubblicaziones()->all() as $Destinatari) {
                    $dati[] = $Destinatari['ruolo'];
                }
                $model->destinatari_pubblicazione = $dati;
                $datiAtt = [];
                foreach ($model->getSondaggiPubblicaziones()->all() as $tipologieAtt) {
                    $datiAtt[] = $tipologieAtt['tipologie_entita'];
                }
                $model->tipologie_entita = $datiAtt;
            }
            ?>
            <?php
            $ruoli = [];
            foreach (Yii::$app->authManager->getRoles() as $key => $value) {
                if ($key != 'ADMIN') {
                    $ruoli[] = $value;
                }
            }
            ?>
            <?= $form->field($model, 'destinatari_pubblicazione')->widget(Select2::classname(), [
                'data' => ArrayHelper::map($ruoli, 'name', 'description'),
                //'showToggleAll' => FALSE,
                'options' => [
                    'placeholder' => AmosSondaggi::t('amossondaggi', 'Digita il nome della classe di utenti ...'),
                    'multiple' => true,
                    'id' => 'destinatari-pubblicazione-ruolo',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]); ?>
            <?php
            $tipologieA = ArrayHelper::map(\lispa\amos\tag\models\Tag::find()->andWhere(['root' => 1])->andWhere(['>', 'lvl', 0])->asArray()->all(), 'id', 'nome');
            //$tipologieB = [0 => 'Non selezionato'];
            //$datiTipologie = ArrayHelper::merge($tipologieB, $tipologieA);            
            ?>
            <?= $form->field($model, 'tipologie_entita')->widget(Select2::classname(), [
                'data' => $tipologieA,
                'options' => [
                    'placeholder' => AmosSondaggi::t('amossondaggi', 'Digita il nome della tipologia di entità ...'),
                    'id' => 'tipologie-attivita-pubblicazione',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]); ?>
            <?php
            //Da abilitare per l'associazione di sondaggi a singoli corsi (modificare anche il controller poi...)
            /*
              $model->attivita_formativa = $model->getSondaggiPubblicaziones()->one()['entita_id'];
              $datiAttivita = ArrayHelper::map(\backend\modules\attivitaformative\models\PeiAttivitaFormative::find()->andWhere(['pei_entita_formative_stati_id' => 2])->asArray()->all(), 'id', 'titolo', function($model) {
              $pei = \backend\modules\peipoint\models\PeiPointSedi::findOne($model['pei_point_sedi_id'])->peiPoint;
              if ($pei) {
              return $pei->denominazione;
              } else {
              return 'Non associato a nessun Punto PEI';
              }
              });
              ?>
              <?=
              $form->field($model, 'attivita_formativa')->widget(Select2::classname(), [
              'data' => $datiAttivita,
              'options' => [
              'placeholder' => AmosSondaggi::t('amossondaggi', 'Digita il nome dell\'attività formativa ...'),
              'id' => 'attivita-formativa-pubblicazione'
              ],
              ])->label('Attività Formativa'); */
            ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php $this->endBlock(); ?>
    <?php
    $itemsTab[] = [
        'label' => AmosSondaggi::tHtml('amossondaggi', 'Generale '),
        'content' => $this->blocks['generale'],
    ];
    ?>

    <?php $this->beginBlock('messaggi'); ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'text_not_compilable_html')->dropDownlist([0 => AmosSondaggi::t('amossondaggi', 'NO'), 1 => AmosSondaggi::t('amossondaggi', 'SI')]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" id="areatext-not-compilable">
            <?=
            $form->field($model, 'text_not_compilable')->textarea(['rows' => 4])
            ?>
        </div>
        <div class="col-lg-12" id="htmltext-not-compilable">
            <?= $form->field($model, 'text_not_compilable')->widget(TextEditorWidget::className(), [
                'clientOptions' => [
                    'placeholder' => AmosSondaggi::t('amossondaggi', '#insert_text'),
                    'lang' => substr(Yii::$app->language, 0, 2)
                ],
                'options' => [
                    'id' => 'text_not_compilable-id-html',
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'text_end_html')->dropDownlist([0 => AmosSondaggi::t('amossondaggi', 'NO'), 1 => AmosSondaggi::t('amossondaggi', 'SI')]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'text_end_title')->textinput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" id="areatext-end">
            <?= $form->field($model, 'text_end')->textarea(['rows' => 4]) ?>
        </div>
        <div class="col-lg-12" id="htmltext-end">
            <?= $form->field($model, 'text_end')->widget(TextEditorWidget::className(), [
                'clientOptions' => [
                    'placeholder' => AmosSondaggi::t('amossondaggi', '#insert_text'),
                    'lang' => substr(Yii::$app->language, 0, 2)
                ],
                'options' => [
                    'id' => 'text_end-id-html',
                ]
            ]) ?>
        </div>
    </div>
    <div class="clearfix"></div>

    <?php $this->endBlock(); ?>
    <?php
    $itemsTab[] = [
        'label' => AmosSondaggi::tHtml('amossondaggi', 'Messaggi '),
        'content' => $this->blocks['messaggi'],
    ];
    ?>
    <?php
    if ($this->context->module->enableNotificationEmailByRoles) {
        $this->beginBlock('notifica');
        ?>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'mail_subject')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'mail_message')->widget(TextEditorWidget::className(), [
                    'clientOptions' => [
                        'placeholder' => AmosSondaggi::t('amossondaggi', '#insert_text'),
                        'lang' => substr(Yii::$app->language, 0, 2)
                    ]
                ]) ?>
            </div>
        </div>
        <div class="clearfix"></div>

        <?php $this->endBlock(); ?>
        <?php
        $itemsTab[] = [
            'label' => AmosSondaggi::tHtml('amossondaggi', 'Notifica '),
            'content' => $this->blocks['notifica'],
        ];
    }
    ?>

    <?= Tabs::widget([
        'encodeLabels' => false,
        'items' => $itemsTab
    ]); ?>

    <?= RequiredFieldsTipWidget::widget() ?>
    <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>
    <?= CloseSaveButtonWidget::widget([
        'model' => $model,
        'buttonNewSaveLabel' => $model->isNewRecord ? AmosSondaggi::tHtml('amossondaggi', 'Inserisci') : AmosSondaggi::tHtml('amossondaggi', 'Salva'),
    ]); ?>
    <?php ActiveForm::end(); ?>
</div>
