<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationWidgets;
use lispa\amos\dashboard\models\AmosWidgets;

/**
 * Class m180411_093139_change_sondaggi_administration_widget
 */
//class m180411_093139_change_sondaggi_administration_widget extends Migration
class m180411_093139_change_sondaggi_administration_widget extends AmosMigrationWidgets
{
    const MODULE_NAME = 'sondaggi';

    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\sondaggi\widgets\icons\WidgetIconSondaggiAdministration::className(),
                'type' => AmosWidgets::TYPE_ICON,
                'module' => self::MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
                'default_order' => 100,
                'dashboard_visible' => 1
            ],
            [
                'classname' => 'lispa\amos\sondaggi\widgets\icons\WidgetIconSondaggiAdministration',
                'update' => true,
                'status' => AmosWidgets::STATUS_DISABLED
            ]
        ];
    }
}
