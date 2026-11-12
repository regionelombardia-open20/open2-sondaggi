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
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use open20\amos\core\views\AmosGridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\sondaggi\models\search\SondaggiDomandeSearch $searchModel
 */

$this->title = AmosSondaggi::t('amossondaggi', "#compilations");
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;


$this->params['titleButtons'][] = Html::a(AmosIcons::show('block').'&nbsp;'.AmosSondaggi::t('amossondaggi', '#revoke_all'),
    Yii::$app->urlManager->createUrl([
        '/'.$this->context->module->id.'/dashboard/delete-compilations',
        'idSondaggio' => $model->id,
    ]),
    [
    'class' => 'btn pull-right btn-danger-inverse',
    'data' => [
        'confirm' => AmosSondaggi::t('amossondaggi', '#modalRevokeAll')
        ]
]);


if (!AmosSondaggi::instance()->enableBreadcrumbs) $this->params['breadcrumbs'] = [];
?>
<div class="sondaggi-compilations-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
        <?php /* echo         Html::a(AmosSondaggi::t('amossondaggi', 'Nuovo {modelClass}', [
          'modelClass' => 'Sondaggi Domande Pagine',
          ])        , ['create'], ['class' => 'btn btn-success']) */ ?>
    </p>

    <?php
    echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label' => AmosSondaggi::t('amossondaggi', AmosSondaggi::instance()->compilationToOrganization ? '#organization' : '#user'),
                    'attribute' => 'compilationSubject',
                    'value' => function ($model) {
                        if (AmosSondaggi::instance()->compilationToOrganization)
                            return $model->to->name;
                        return $model->to->profile->nome;
                    },
                ],
                [
                    'label' => AmosSondaggi::t('amossondaggi', '#compilazioniStatus'),
                    'attribute' => 'status',
                    'value' => function ($model) {
                        if (empty($model->lastSession->status)) return AmosSondaggi::t('amossondaggi', '#not_compiled');
                        return AmosSondaggi::t('amossondaggi', $model->lastSession->status);
                    },
                ],
                'lastSession.completato:statosino',
                [
                    'attribute' => 'lastSession.end_date',
                    'value' => function($model) {
                        return \Yii::$app->formatter->asDateTime($model->lastSession->end_date, 'humanalwaysdatetime');
                    }
                ],
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{cancella}',
                    'buttons' => [
                        'cancella' => function ($url, $model) {
                            if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
                                $url = \yii\helpers\Url::current();
                                return Html::a(AmosIcons::show('block'),
                                      Yii::$app->urlManager->createUrl([
                                          '/'.$this->context->module->id.'/dashboard/delete-compilations',
                                          'id' => $model->id,
                                          'idSondaggio' => $model->sondaggi_id,
                                          'url' => $url
                                      ]),
                                      [
                                      'class' => 'btn btn-danger-inverse',
                                      'title' => AmosSondaggi::t('amossondaggi', '#revoke_compilation'),
                                      'data' => [
                                        'confirm' => AmosSondaggi::t('amossondaggi', '#modalRevokeCompilation')
                                        ]
                                      ]);
                            } else {
                                return '';
                            }
                        },
                    ]
                ]
            ],
        ]
    ]);
    ?>
    <p>
        <?php
//        if (isset($url)) :
//            echo Html::a(AmosSondaggi::t('amossondaggi', 'Aggiungi domanda'), ['create', 'idSondaggio' => filter_input(INPUT_GET, 'idSondaggio'), 'idPagina' => filter_input(INPUT_GET, 'idPagina'), 'url' => yii\helpers\Url::current()], ['class' => 'btn btn-success']);
//        endif;
        ?>
    </p>
</div>
