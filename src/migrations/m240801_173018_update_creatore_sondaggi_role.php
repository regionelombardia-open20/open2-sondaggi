<?php

use open20\amos\core\migration\AmosMigrationPermissions;

/**
 * Class m240801_173018_update_creatore_sondaggi_role
 */
class m240801_173018_update_creatore_sondaggi_role extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'CREATORE_SONDAGGI',
                'update' => true,
                'newValues' => [
                    'addChildren' => ['SONDAGGIDOMANDEPAGINERULE_CREATE']
                ]
            ],
        ];
    }
}
