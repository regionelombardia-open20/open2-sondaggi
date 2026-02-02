<?php

use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\CloseSaveButtonWidget;
use open20\amos\core\forms\CreatedUpdatedWidget;
use open20\amos\core\forms\editors\FileInput;
use open20\amos\sondaggi\AmosSondaggi;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var open20\amos\sondaggi\models\SondaggiDomandePagine $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="sondaggi-domande-pagine-form">

    <?php
    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
    $customView = Yii::$app->getViewPath() . '/imageField.php';
    ?>

    <?php $this->beginBlock('generale'); ?>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <?php if (!$model->isNewRecord) { ?>
                <?= $form->field($model, 'sondaggi_domande_pagine_id', ['options' => ['style' => 'display: none;']])->hiddenInput()->label(false) ?>
                <?= Html::beginTag('div', ['class' => 'form-group field-sondaggio']) ?>
                <?= Html::tag('h2', AmosSondaggi::t('amossondaggi', 'Pagina in cui è applicata la regola') . ': ' . $model->sondaggiDomandePagine->titolo) ?>

                <?= Html::endTag('div') ?>          
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>      
            <?=
            $form->field($model, 'rule')->dropDownList(open20\amos\sondaggi\utility\SondaggiUtility::RULES,
                    ['prompt' => AmosSondaggi::t('amossondaggi', 'Regola da applicare')]);
            ?>    
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php
            $query = open20\amos\sondaggi\models\SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine.sondaggi_id' => $idSondaggio])
                    ->joinWith('sondaggiDomandePagine')
                    ->andWhere(['sondaggi_domande_tipologie_id' => 5])
                    ->orderBy('sondaggi_domande_pagine.ordinamento, sondaggi_domande.ordinamento')
                    ->select(['sondaggi_domande.id', 'sondaggi_domande.domanda', 'sondaggi_domande_pagine.titolo']);
            $query2 = clone ($query);
            //print_r($query->createCommand()->rawSql);die;
            $count = $query2->count();
            ?>
            <?=
            $form->field($model, 'fields2')->dropDownList(
                    yii\helpers\ArrayHelper::map($query->asArray()->all(), 'id', 'domanda', 'titolo'),
                    ['prompt' => AmosSondaggi::t('amossondaggi',
                                'Seleziona la domande o le domande a cui applicare la regola'), 'id' => 'regola-id',
                        'multiple' => true, 'size' => $count + 5]
            )
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?=
            $form->field($model, 'logical_operator')->dropDownList(open20\amos\sondaggi\utility\SondaggiUtility::LOGIC_OPERATOR,
                    ['prompt' => AmosSondaggi::t('amossondaggi', 'Scegli operatore')]);
            ?>  
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'value')->textInput() ?>     
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php
            if ($model->isNewRecord) {
                $model->message = AmosSondaggi::t('amossondaggi', \open20\amos\sondaggi\utility\SondaggiUtility::MESSAGE_RULE[1]);
            }
            ?>
            <?= $form->field($model, 'message')->textInput(['maxlength' => true]) ?>   

        </div>
    </div>
    <div class="clearfix"></div>
    <?php $this->endBlock(); ?>

    <?php
    $itemsTab[] = [
        'label' => AmosSondaggi::tHtml('amossondaggi', 'generale '),
        'content' => $this->blocks['generale'],
    ];
    ?>

    <?=
    Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => $itemsTab
            ]
    );
    ?>
    <div class="col-xs-12 note_asterisk nop">
        <p>I campi <span class="red">*</span> sono obbligatori.</p>
    </div>
    <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>
    <?=
    CloseSaveButtonWidget::widget([
        'model' => $model,
        'buttonNewSaveLabel' => $model->isNewRecord ? AmosSondaggi::tHtml('amossondaggi', 'Crea') : AmosSondaggi::tHtml('amossondaggi', 'Salva'),
        'closeButtonLabel' => AmosSondaggi::t('amossondaggi', 'Chiudi'),
    ]);
    ?>

    <?php ActiveForm::end(); ?>
</div>
