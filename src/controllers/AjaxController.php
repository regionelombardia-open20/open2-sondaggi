<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\controllers
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

namespace open20\amos\sondaggi\controllers;

use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\models\SondaggiDomande;
use open20\amos\sondaggi\models\SondaggiDomandePagine;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * Class AjaxController
 * @package open20\amos\sondaggi\controllers
 */
class AjaxController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(),
                [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'domande-by-pagine',
                                'pagine',
                                'pagine-by-sondaggio',
                                'change-status-session'
                            ],
                            'roles' => ['@']
                        ]
                    ]
                ]
        ]);
        return $behaviors;
    }

    public function actionPagineBySondaggio()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $id          = end($_POST['depdrop_parents']);
            $id_selected = end($_POST['depdrop_params']);
            $pagine      = SondaggiDomandePagine::find()->andWhere(['sondaggi_id' => $id])->asArray()->all();
            $selected    = null;
            if ($id != null && count($pagine) > 0) {
                $selected = '';
                foreach ($pagine as $i => $pagina) {

                    $out[] = ['id' => $pagina['id'], 'name' => $pagina['titolo']];

                    if ($id_selected) {
                        $selected = $id_selected;
                    } elseif ($i == 0) {
                        $selected = $pagina['id'];
                    }
                }
                // Shows how you can preselect a value
                return Json::encode(['output' => $out, 'selected' => $selected]);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionChangeStatusSession($id, $new_state)
    {
        if (\Yii::$app->request->isAjax) {
            $model = \open20\amos\sondaggi\models\SondaggiRisposteSessioni::findOne($id);

            if (\Yii::$app->user->can($new_state, ['model' => $model])) {
                $model->status = $new_state;

                if ($model->save(false)) {
                    \Yii::$app->getSession()->addFlash('success',
                        AmosSondaggi::t('amossondaggi', 'Stato cambiato correttamente.'));
                    return true;
                }
            }
        }

        return false;
    }

    public function actionDomandeByPagine($currentDomandaId = null)
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $id          = end($_POST['depdrop_parents']);
            $id_selected = end($_POST['depdrop_params']);
            $pagine      = SondaggiDomande::find()
                ->andWhere(['sondaggi_domande_pagine_id' => $id])
                ->andWhere(['parent_id' => null])
                ->andFilterWhere(['!=', 'id', $currentDomandaId])
                ->orderBy('ordinamento ASC')
                ->asArray()
                ->all();
            $selected    = null;
            if ($id != null && count($pagine) > 0) {
                $selected = '';
                foreach ($pagine as $i => $pagina) {

                    $out[] = ['id' => $pagina['id'], 'name' => html_entity_decode(strip_tags($pagina['domanda']))];

                    if ($id_selected) {
                        $selected = $id_selected;
                    } elseif ($i == 0) {
                        $selected = $pagina['id'];
                    }
                }
                // Shows how you can preselect a value
                return Json::encode(['output' => $out, 'selected' => $selected]);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionPagine($search = null, $id = null)
    {
        $out = ['more' => false];
        if (!is_null($search)) {
            $query          = new Query();
            $query->select('id, titolo AS text')
                ->from('sondaggi_domande_pagine')
                ->where('titolo LIKE "%'.$search.'%"');
            //->limit(20);
            $command        = $query->createCommand();
            $data           = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => SondaggiDomandePagine::findOne($id)->titolo];
        } else {
            $out['results'] = ['id' => 0, 'text' => AmosSondaggi::t('amossondaggi', 'Nessun risultato trovato')];
        }
        return Json::encode($out);
    }
}