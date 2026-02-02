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
use open20\amos\sondaggi\rules\RemoveOwnSondaggiRule;
use open20\amos\sondaggi\rules\UpdateOwnSondaggiRule;
use yii\rbac\Permission;

/**
 * Class m240801_124424_creatore_sondaggi_permission
 */
class m240801_124424_creatore_sondaggi_permission extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'updateOwnSondaggi',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if user can update own sondaggi',
                'ruleName' => UpdateOwnSondaggiRule::class,
                'children' => [
                    'SONDAGGI_UPDATE',
                    'SONDAGGIDOMANDE_UPDATE',
                    'SONDAGGIDOMANDEPAGINE_UPDATE',
                    'SONDAGGIDOMANDETIPOLOGIE_UPDATE',
                    'SONDAGGIRISPOSTEPREDEFINITE_UPDATE',
                    'DASHBOARD_VIEW'
                ],
            ],
            [
                'name' => 'removeOwnSondaggi',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if user can remove own sondaggi',
                'ruleName' => RemoveOwnSondaggiRule::class,
                'children' => [
                    'SONDAGGI_DELETE',
                    'SONDAGGIDOMANDE_DELETE',
                    'SONDAGGIDOMANDEPAGINE_DELETE',
                    'SONDAGGIDOMANDETIPOLOGIE_DELETE',
                    'SONDAGGIRISPOSTEPREDEFINITE_DELETE',
                ]
            ],
            [
                'name' => 'CREATORE_SONDAGGI',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Creatore Sondaggi Role',
                'children' => [
                    'updateOwnSondaggi',
                    'removeOwnSondaggi',
                    'SONDAGGI_CREATE',
                    'SONDAGGIDOMANDE_CREATE',
                    'SONDAGGIDOMANDEPAGINE_CREATE',
                    'SONDAGGIDOMANDETIPOLOGIE_CREATE',
                    'SONDAGGIRISPOSTEPREDEFINITE_CREATE'
                ],
//                'parent' => ['AMMINISTRAZIONE_SONDAGGI'] // DISABLED BECAUSE THERE ARE CONFLICTS WHEN CHECKING ROLES
            ],
        ];
    }
}
