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

use open20\amos\core\rules\DefaultOwnContentRule;
use open20\amos\sondaggi\models\Sondaggi;
use Yii;

class RemoveOwnSondaggiRule extends DefaultOwnContentRule
{
    public $name = 'removeOwnSondaggi';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if (isset($params['model'])) {
            /** @var Record $model */
            $model = $params['model'];
            if (!$model->id) {
                $post = Yii::$app->getRequest()->post();
                $get = Yii::$app->getRequest()->get();
                if (isset($get['id'])) {
                    $model = $this->instanceModel($model, $get['id']);
                } elseif (isset($post['id'])) {
                    $model = $this->instanceModel($model, $post['id']);
                }
            }

            return ($model->created_by == $user &&
                !($model instanceof Sondaggi && $model->status != Sondaggi::WORKFLOW_STATUS_BOZZA));
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
