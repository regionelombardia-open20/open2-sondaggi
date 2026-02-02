<?php


namespace open20\amos\sondaggi\modules\v1;

use open20\amos\core\module\AmosModule;
use yii\helpers\ArrayHelper;

/**
 * Class Module
 * @package open20\amos\mobile\bridge
 */
class V1 extends AmosModule
{
    public $newFileMode = 0666;
    public $newDirMode = 0777;
    public static $CONFIG_FOLDER = 'config';

    /**
     * @inheritdoc
     */
    static $name = 'v1';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'open20\amos\sondaggi\modules\v1\controllers';


    public function init()
    {
        parent::init();

        // Custom initialization code goes here
        // Ensure the user component is stateless for this module's requests
        \Yii::$app->user->enableSession = false;

        // Optionally, configure the loginUrl to return 401 instead of redirecting
        \Yii::$app->user->loginUrl = null;
    }
   

    /**
     * Module name
     * @return string
     */
    public static function getModuleName()
    {
        return self::$name;
    }

    public function getWidgetIcons()
    {
        return [
        ];
    }

    public function getWidgetGraphics()
    {
        return [
        ];
    }

    protected function getDefaultModels()
    {
        return [
        ];
    }
}
