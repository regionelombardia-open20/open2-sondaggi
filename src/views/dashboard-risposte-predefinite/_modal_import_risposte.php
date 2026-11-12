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

use \yii\bootstrap\Modal;
use \open20\amos\core\helpers\Html;
use \kartik\widgets\FileInput;
use \yii\base\InvalidConfigException;
use open20\amos\core\forms\ActiveForm;

//$assetBundle = \open20\amos\showcaseprojects\assets\ShowcaseProjectAsset::register($this);
$linkDownloadAsset = '/sondaggi/dashboard/download-import-file-example';
$form = ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]);


    Modal::begin([
        'header' => '<h2>' . Yii::t('amossondaggi', 'Importa risposte predefinite') . '</h2>',
        'size' => Modal::SIZE_LARGE,
        'id' => 'modalImport',
        'footer' => Html::button(
            Yii::t('amossondaggi', '#import'),
            [
                'class' => 'btn btn-primary',
                'value' => 'import',
                'type' => 'submit',
                'name' => 'submit-import',
                'id' => 'submitImport'
            ]
        ),
    ]);

    $linkDownload = Html::a(Yii::t('amossondaggi', 'qui'), $linkDownloadAsset);
    echo Yii::t('amossondaggi', "L'importazione delle risposte può essere fatta solo utilizzando il file predisposto. Segui i seguenti passi:");
    echo '<ol>';
    echo '<li>' . Yii::t('amossondaggi', 'scarica il file facendo click {linkdownload} e salvalo.', ['linkdownload' => $linkDownload]) . '</li>';
    echo '</ol>';

    echo '<label class="control-label">' . Yii::t('amossondaggi', 'Carica il file') . '</label>';
    echo FileInput::widget([
        'name' => 'import-file',
        'pluginOptions' => [
            'showPreview' => false,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false
        ]
    ]);
    echo $form->field($model, 'sondaggi_domande_id')->hiddenInput()->label(FALSE);
//    echo $form->field($model, 'tipo_domanda')->hiddenInput()->label(FALSE);

    Modal::end();

ActiveForm::end();
