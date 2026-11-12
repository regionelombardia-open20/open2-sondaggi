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
use open20\amos\sondaggi\rules\CommunityManagerAdminSondaggiRule;
use open20\amos\sondaggi\rules\RemoveOwnSondaggiRule;
use open20\amos\sondaggi\rules\UpdateOwnSondaggiRule;
use yii\rbac\Permission;

/**
 * Class m240829_190424_community_manager_admin_sondaggi_rule
 */
class m240829_190424_community_manager_admin_sondaggi_rule extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'communityManagerAdminSondaggi',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if user is community manager for model sondaggi and can admin it',
                'ruleName' => CommunityManagerAdminSondaggiRule::class,
                'children' => [
                    'SONDAGGIDOMANDEPAGINERULE_CREATE',
                    'SONDAGGIDOMANDEPAGINERULE_DELETE',
                    'SONDAGGIDOMANDEPAGINERULE_READ',
                    'SONDAGGIDOMANDEPAGINERULE_UPDATE',
                    'SONDAGGIDOMANDEPAGINE_CREATE',
                    'SONDAGGIDOMANDEPAGINE_DELETE',
                    'SONDAGGIDOMANDEPAGINE_READ',
                    'SONDAGGIDOMANDEPAGINE_UPDATE',
                    'SONDAGGIDOMANDETIPOLOGIE_CREATE',
                    'SONDAGGIDOMANDETIPOLOGIE_DELETE',
                    'SONDAGGIDOMANDETIPOLOGIE_READ',
                    'SONDAGGIDOMANDETIPOLOGIE_UPDATE',
                    'SONDAGGIDOMANDE_CREATE',
                    'SONDAGGIDOMANDE_DELETE',
                    'SONDAGGIDOMANDE_READ',
                    'SONDAGGIDOMANDE_UPDATE',
                    'SONDAGGIRISPOSTEPREDEFINITE_CREATE',
                    'SONDAGGIRISPOSTEPREDEFINITE_DELETE',
                    'SONDAGGIRISPOSTEPREDEFINITE_READ',
                    'SONDAGGIRISPOSTEPREDEFINITE_UPDATE',
                    'SONDAGGIRISPOSTE_DELETE',
                    'SONDAGGIRISPOSTE_READ',
                    'SONDAGGIRISPOSTE_UPDATE',
                    'SONDAGGI_CREATE',
                    'SONDAGGI_DELETE',
                    'SONDAGGI_MANAGE',
                    'SONDAGGI_READ',
                    'SONDAGGI_UPDATE',
                    'SONDAGGI_VALIDATOR',
                    'SondaggiWorkflow/BOZZA',
                    'SondaggiWorkflow/DAVALIDARE',
                    'open20\amos\sondaggi\rules\SondaggiWorkflowPublishedRule',
                    'open20\amos\sondaggi\widgets\icons\WidgetIconSondaggi',
                    'open20\amos\sondaggi\widgets\icons\WidgetIconSondaggiAdministration',
                    'open20\amos\sondaggi\widgets\icons\WidgetIconSondaggiGeneral',
                    'open20\amos\sondaggi\widgets\icons\WidgetIconPubblicaSondaggi',
                    'open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi',
                    'SondaggiValidate',
                ]
            ],
            [
                'name' => 'COMMUNITYMANAGER_ADMIN_SONDAGGI',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Community Manage Admin Sondaggi Role',
                'children' => [
                    'communityManagerAdminSondaggi',
                    'COMPILATORE_SONDAGGI',
                ],
            ],
        ];
    }
}
