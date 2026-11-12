<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\migrations
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
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
