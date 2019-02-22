<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi;

use lispa\amos\core\module\AmosModule;
use lispa\amos\core\module\ModuleInterface;
use lispa\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi;
use lispa\amos\sondaggi\widgets\icons\WidgetIconPubblicaSondaggi;
use lispa\amos\sondaggi\widgets\icons\WidgetIconSondaggi;
use lispa\amos\sondaggi\widgets\icons\WidgetIconAmministraSondaggi;
use Yii;
use yii\db\Connection;

/**
 * Class AmosSondaggi
 * @package lispa\amos\sondaggi
 */
class AmosSondaggi extends AmosModule implements ModuleInterface
{
    public static $CONFIG_FOLDER = 'config';
    public $controllerNamespace = 'lispa\amos\sondaggi\controllers';
    public $newFileMode = 0666;
    public $newDirMode = 0777;

    /**
     * In the case of a private poll for role, it is possible to send the notification to the users who can fill out the survey.
     * @var boolean
     */
    public $enableNotificationEmailByRoles = false;

    /**
     * Default email for the sender
     * @var string
     */
    public $defaultEmailSender;

    /**
     * It allows to show in the first page of the results the geoChart based on the province of domicile.
     * @var boolean
     */
    public $enableGeoChart = false;

    /**
     * It allows to show in the first page of the results a partecipant report if available.
     * @var boolean
     */
    public $enablePartecipantsReport = false;

    /**
     * The fields that will be displayed in the participant's //TO-DO
     * @var array
     */
    public $fieldsByPartecipants = [];

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';
    public $name = 'Sondaggi';

    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     */
    public $db = 'db';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->db = Yii::$app->db;

        \Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/controllers/', __DIR__ . '/controllers/');
        // initialize the module with the configuration loaded from config.php
        Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . self::$CONFIG_FOLDER . DIRECTORY_SEPARATOR . 'config.php'));
    }

    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return "sondaggi";
    }

    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [
            WidgetIconSondaggi::className(),
            WidgetIconCompilaSondaggi::className(),
            WidgetIconPubblicaSondaggi::className(),
            WidgetIconAmministraSondaggi::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultModels()
    {
        return [];
    }
}
