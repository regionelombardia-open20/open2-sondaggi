<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\news
 * @category   CategoryName
 */

namespace open20\amos\sondaggi\rules;

use open20\amos\core\record\Record;
use open20\amos\core\rules\DefaultOwnContentRule;
use open20\amos\cwh\models\CwhConfig;
use open20\amos\cwh\models\CwhConfigContents;
use open20\amos\cwh\models\CwhPubblicazioni;
use open20\amos\cwh\models\CwhPubblicazioniCwhNodiEditoriMm;
use open20\amos\cwh\models\CwhPubblicazioniCwhNodiValidatoriMm;
use open20\amos\cwh\models\CwhRegolePubblicazione;
use open20\amos\sondaggi\models\base\SondaggiDomande;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiDomandePagine;
use open20\amos\sondaggi\models\SondaggiDomandePagineRule;
use open20\amos\sondaggi\models\SondaggiRispostePredefinite;
use open20\amos\sondaggi\utility\SondaggiUtility;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class CommunityManagerAdminSondaggiRule extends DefaultOwnContentRule
{
    public $name = 'communityManagerAdminSondaggi';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {

        $moduleCwh = \Yii::$app->getModule('cwh');
        $moduleCommunity = \Yii::$app->getModule('community');

        if(empty($moduleCwh) || empty($moduleCommunity)) {
            return false;
        }

        $scope = $moduleCwh->getCwhScope();
        if(empty($scope)) {
            return false;
        }


        if (isset($params['model'])) {
            /** @var Record $model */
            $model = $params['model'];

            if (
                !($model instanceof Sondaggi) &&
                !($model instanceof SondaggiDomandePagine) &&
                !($model instanceof SondaggiDomande) &&
                !($model instanceof SondaggiRispostePredefinite) &&
                !($model instanceof SondaggiDomandePagineRule)
            ) {
                return false;
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

            return SondaggiUtility::isCommunityManagerOfContent($modelSondaggio);
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
