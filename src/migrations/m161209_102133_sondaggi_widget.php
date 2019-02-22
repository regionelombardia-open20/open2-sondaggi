<?php

use lispa\amos\core\migration\AmosMigrationWidgets;
use lispa\amos\dashboard\models\AmosWidgets;

class m161209_102133_sondaggi_widget extends AmosMigrationWidgets {

    const MODULE_NAME = 'sondaggi';

    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs() {
        $this->widgets = [
                [
                'classname' => lispa\amos\sondaggi\widgets\icons\WidgetIconSondaggi::className(),
                'type' => AmosWidgets::TYPE_ICON,
                'module' => self::MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
            ],
                [
                'classname' => lispa\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi::className(),
                'type' => AmosWidgets::TYPE_ICON,
                'module' => self::MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
            ],
                [
                'classname' => lispa\amos\sondaggi\widgets\icons\WidgetIconPubblicaSondaggi::className(),
                'type' => AmosWidgets::TYPE_ICON,
                'module' => self::MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
            ]
        ];
    }

}
