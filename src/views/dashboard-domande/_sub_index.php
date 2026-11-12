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

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\sondaggi\models\search\SondaggiDomandeSearch $searchModel
 */

echo DataProviderView::widget([
    'dataProvider' => $dataProvider,
    'currentView' => $currentView,
    'gridView' => [
        'striped' => false,
        'showPageSummary' => false,
        'showHeader'=> false,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            //'id',
            //'domanda:ntext',
            [
                'attribute' => 'domanda',
                'format' => 'raw',
                'value' => function ($model) {
                    if (preg_match('/<p>(.*?)<\/p>/', $model->domanda, $matches)) {
                        return $matches[1]; // Restituisce il contenuto del primo paragrafo
                    } else {
                        return Yii::$app->formatter->asNtext($model->domanda);
                    }
                }
            ],
            [
                'class' => 'open20\amos\core\views\grid\ActionColumn',
                'template' => '{update} {clone} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        $url = \yii\helpers\Url::current();
                        if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGIDOMANDE_UPDATE', ['model' => $model])) {
                            return Html::a(AmosIcons::show('edit'), Yii::$app->urlManager->createUrl([
                                '/' . $this->context->module->id . '/dashboard-domande/update',
                                'id' => $model->id,
                                'url' => $url,
                            ]), [
                                'title' => AmosSondaggi::t('amossondaggi', 'Modifica'),
                                'class' => 'btn btn-tool-secondary'
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'delete' => function ($url, $model) {
                        /** @var \open20\amos\sondaggi\models\search\SondaggiDomandeSearch $model */
                        $url = \yii\helpers\Url::current();
                        if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGIDOMANDE_DELETE', ['model' => $model])) {
                            return Html::a(AmosIcons::show('delete'), Yii::$app->urlManager->createUrl([
                                '/' . $this->context->module->id . '/sondaggi-domande/delete',
                                'id' => $model->id,
                                'idSondaggio' => $model->sondaggi_id,
                                'url' => $url,
                            ]), [
                                'title' => AmosSondaggi::t('amossondaggi', 'Cancella'),
                                'class' => 'btn btn-danger-inverse'
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'clone' => function ($url, $model) {
                        /** @var \open20\amos\sondaggi\models\search\SondaggiDomandeSearch $model */
                        $url = \yii\helpers\Url::current();
                        if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGIDOMANDE_CREATE', ['model' => $model])) {
                            return Html::a(AmosIcons::show('copy'), Yii::$app->urlManager->createUrl([
                                '/' . $this->context->module->id . '/dashboard-domande/clone',
                                'id' => $model->id,
                                'idSondaggio' => $model->sondaggi_id,
                                'url' => $url
                            ]), [
                                'title' => AmosSondaggi::t('amossondaggi', '#clone'),
                                'class' => 'btn btn-tool-secondary'
                            ]);
                        } else {
                            return '';
                        }
                    },
                ]
            ],
        ],
    ],
]);
?>
