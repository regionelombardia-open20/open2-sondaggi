<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\migrations
 * @category   CategoryName
 */

use open20\amos\core\migration\AmosMigrationPermissions;
use open20\amos\sondaggi\models\Sondaggi;

/**
 * Class m240802_132328_update_sondaggi_workflow_permissions_for_updateOwnSondaggi
 */
class m240802_132328_update_sondaggi_workflow_permissions_for_updateOwnSondaggi extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => Sondaggi::WORKFLOW_STATUS_BOZZA,
                'update' => true,
                'newValues' => [
                    'addParents' => ['updateOwnSondaggi']
                ]
            ],
            [
                'name' => Sondaggi::WORKFLOW_STATUS_DAVALIDARE,
                'update' => true,
                'newValues' => [
                    'addParents' => ['updateOwnSondaggi']
                ]
            ]
        ];
    }
}
