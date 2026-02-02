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
