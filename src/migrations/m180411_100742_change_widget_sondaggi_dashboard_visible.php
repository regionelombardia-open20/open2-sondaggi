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

/**
 * Class m180411_100742_change_widget_sondaggi_dashboard_visible
 */
class m180411_100742_change_widget_sondaggi_dashboard_visible extends AmosMigrationWidgets
{
    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi::className(),
                'update' => true,
                'default_order' => 110,
                'dashboard_visible' => 1
            ],
            [
                'classname' => \lispa\amos\sondaggi\widgets\icons\WidgetIconPubblicaSondaggi::className(),
                'update' => true,
                'default_order' => 120,
                'dashboard_visible' => 1
            ],
            [
                'classname' => \lispa\amos\sondaggi\widgets\icons\WidgetIconSondaggi::className(),
                'update' => true,
                'default_order' => 130,
                'dashboard_visible' => 1
            ]
        ];
    }
}
