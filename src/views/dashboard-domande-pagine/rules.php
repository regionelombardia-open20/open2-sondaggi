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
use open20\amos\core\views\DataProviderView;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\models\SondaggiDomandePagine;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \open20\amos\sondaggi\models\search\SondaggiDomandePagineSearch $searchModel
 */
$this->title = AmosSondaggi::t('amossondaggi', 'Gestione regole');
if ($url) {
    $this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => '/' . $this->context->module->id . '/sondaggi/manage'];
} else {
    $this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => 'sondaggi/manage', 'route' => 'sondaggi/sondaggi/manage'];
    $this->params['breadcrumbs'][] = ['label' => $this->title];
}
if (!AmosSondaggi::instance()->enableBreadcrumbs)
    $this->params['breadcrumbs'] = [];

$this->params['titleButtons'][] = Html::a(
                AmosIcons::show('plus-circle') . '&nbsp;' . AmosSondaggi::t('amossondaggi', 'Nuova regola'),
                Yii::$app->urlManager->createUrl([
                    '/' . $this->context->module->id . '/dashboard-domande-pagine/create-rule',
                    'idSondaggio' => filter_input(INPUT_GET, 'idSondaggio'),
                    'url' => $url,
                ]),
                [
                    'title' => AmosSondaggi::t('amossondaggi', 'Nuova regola'),
                    'class' => 'btn btn-primary'
                ]
);


$contextMenuVisibile = true;
if (Yii::$app->user->can('CREATORE_SONDAGGI')) {
    $sondaggio = \open20\amos\sondaggi\models\Sondaggi::findOne(['id' => Yii::$app->request->get('idSondaggio')]);
    if(!empty($sondaggio)) {
        if($sondaggio->status !== \open20\amos\sondaggi\models\Sondaggi::WORKFLOW_STATUS_BOZZA) {
            $contextMenuVisibile = false;
            $this->registerCss(<<<CSS
.page-header .w-100 {
display: none !important;
}
CSS
            );
        }
    }
}

$contextMenuOptions = [];
if($contextMenuVisibile) {
    $contextMenuOptions[] = [
        'class' => 'open20\amos\core\views\grid\ActionColumn',
        'template' => '{update} {delete}',
        'buttons' => [
            'update' => function ($url, $model) use ($idSondaggio) {
                /** @var \open20\amos\sondaggi\models\search\SondaggiDomandePagineSearch $model */
                $url = \yii\helpers\Url::current();
                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGIDOMANDEPAGINE_UPDATE', ['model' => $model])) {
                    return Html::a(
                        AmosIcons::show('edit'),
                        Yii::$app->urlManager->createUrl([
                            '/' . $this->context->module->id . '/dashboard-domande-pagine/update-rule',
                            'idSondaggio' => $idSondaggio,
                            'id' => $model->id,
                            'url' => $url,
                        ]),
                        [
                            'title' => AmosSondaggi::t('amossondaggi', 'Modifica'),
                            'class' => 'btn btn-tool-secondary'
                        ]
                    );
                } else {
                    return '';
                }
            },
            'delete' => function ($url, $model) use ($idSondaggio) {
                /** @var \open20\amos\sondaggi\models\search\SondaggiDomandePagineSearch $model */
                $url = \yii\helpers\Url::current();
                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGIDOMANDEPAGINE_DELETE', ['model' => $model])) {
                    return Html::a(AmosIcons::show('delete'), Yii::$app->urlManager->createUrl([
                        '/' . $this->context->module->id . '/dashboard-domande-pagine/delete-rule',
                        'idSondaggio' => $idSondaggio,
                        'id' => $model->id,
                        'url' => $url,
                    ]), [
                        'title' => AmosSondaggi::t('amossondaggi', 'Cancella'),
                        'class' => 'btn btn-danger-inverse',
                        'data-confirm' => AmosSondaggi::t('amossondaggi', 'Sei sicuro di voler cancellare la regola?')
                    ]);
                } else {
                    return '';
                }
            },
        ]
    ];
}
?>

<div class="sondaggi-domande-pagine-dashboard rules">

    <?php
    echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => array_merge([
                'title',
                [
                    'attribute' => 'rule',
                    'value' => function ($model) {
                        return \open20\amos\sondaggi\utility\SondaggiUtility::RULES[$model->rule];
                    }
                ],
                [
                    'attribute' => 'fields',
                    'value' => function ($model) {
                        $allFields = [];
                        foreach (json_decode($model->fields, true) as $fld) {
                            $field = open20\amos\sondaggi\models\SondaggiDomande::findOne($fld);
                            if (!empty($field)) {
                                $allFields[] = $field->domanda;
                            }
                        }
                        return implode(', ', $allFields);
                    }
                ],
                [
                    'attribute' => 'logical_operator',
                    'value' => function ($model) {
                        return \open20\amos\sondaggi\utility\SondaggiUtility::LOGIC_OPERATOR[$model->logical_operator];
                    }
                ],
                'value:decimal',
                'message',
                'sondaggiDomandePagine.titolo',
            ], $contextMenuOptions),
        ],
    ]);
    ?>   
</div>