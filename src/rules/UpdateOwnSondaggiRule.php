<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\news
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

namespace open20\amos\sondaggi\rules;

use open20\amos\core\rules\DefaultOwnContentRule;
use open20\amos\sondaggi\models\base\SondaggiDomande;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiDomandePagine;
use open20\amos\sondaggi\models\SondaggiDomandePagineRule;
use open20\amos\sondaggi\models\SondaggiRispostePredefinite;
use Yii;

class UpdateOwnSondaggiRule extends DefaultOwnContentRule
{
    public $name = 'updateOwnSondaggi';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $bypassStatusCheck = false;

        if (isset($params['model'])) {
            /** @var Record $model */
            $model = $params['model'];

            if(!empty($params['bypassStatusCheck'])) {
                $bypassStatusCheck = $params['bypassStatusCheck'];
            }

            if (!$model->id) {
                $post = Yii::$app->getRequest()->post();
                $get = Yii::$app->getRequest()->get();
                if (isset($get['id'])) {
                    $model = $this->instanceModel($model, $get['id']);
                } elseif (isset($post['id'])) {
                    $model = $this->instanceModel($model, $post['id']);
                } elseif (isset($get['idDomanda'])) {
                    $model = SondaggiDomande::findOne(['id' => $get['idDomanda']]);
                } elseif (isset($post['idDomanda'])) {
                    $model = SondaggiDomande::findOne(['id' => $post['idDomanda']]);
                } elseif (isset($get['idSondaggio'])) {
                    $model = Sondaggi::findOne(['id' => $get['idSondaggio']]);
                } elseif (isset($post['idSondaggio'])) {
                    $model = Sondaggi::findOne(['id' => $post['idSondaggio']]);
                }
            }

            $modelSondaggio = $model;
            if (!($model instanceof Sondaggi)) {
                if(
                    $model instanceof SondaggiDomandePagine ||
                    $model instanceof SondaggiDomande
                ) {
                    $modelSondaggio = Sondaggi::findOne(['id' => $model->sondaggi_id]);
                }
                if (
                    $model instanceof SondaggiRispostePredefinite
                ) {
                    $modelSondaggiDomande = SondaggiDomande::findOne(['id' => $model->sondaggi_domande_id]);
                    $modelSondaggio = Sondaggi::findOne(['id' => $modelSondaggiDomande->sondaggi_id]);
                }
                if (
                    $model instanceof SondaggiDomandePagineRule
                ) {
                    $modelSondaggio = Sondaggi::findOne(['id' => $model->sondaggiDomandePagine->sondaggi_id]);
                }
            }
            if(!$modelSondaggio->hasMethod('getWorkflowStatus')){
                return true;
            }

            if (!empty($modelSondaggio->getWorkflowStatus())) {
                if (($modelSondaggio->getWorkflowStatus()->getId() == Sondaggi::WORKFLOW_STATUS_BOZZA || $modelSondaggio->getWorkflowStatus()->getId() == Sondaggi::WORKFLOW_STATUS_VALIDATO || Yii::$app->getUser()->can('SondaggioValidate', ['model' => $modelSondaggio]) || $bypassStatusCheck) && $modelSondaggio->created_by == $user) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * @param Record $model
     * @param int $modelId
     * @return mixed
     */
    protected function instanceModel($model, $modelId)
    {
        $modelClass = $model->className();
        /** @var Record $modelClass */
        $instancedModel = $modelClass::findOne($modelId);
        if (!is_null($instancedModel)) {
            $model = $instancedModel;
        }
        return $model;
    }
}
