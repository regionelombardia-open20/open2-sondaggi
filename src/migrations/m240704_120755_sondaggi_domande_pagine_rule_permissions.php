<?php

use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m240704_120755_sondaggi_domande_pagine_rule_permissions
 */
class m240704_120755_sondaggi_domande_pagine_rule_permissions extends AmosMigrationPermissions {

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations() {
        $prefixStr = '';

        return [
            [
                'name' => 'SONDAGGIDOMANDEPAGINERULE_CREATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di CREATE sul model SondaggiDomandePagineRule',
                'ruleName' => null,
                'parent' => ['AMMINISTRAZIONE_SONDAGGI']
            ],
            [
                'name' => 'SONDAGGIDOMANDEPAGINERULE_READ',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di READ sul model SondaggiDomandePagineRule',
                'ruleName' => null,
                'parent' => ['AMMINISTRAZIONE_SONDAGGI', 'BASIC_USER']
            ],
            [
                'name' => 'SONDAGGIDOMANDEPAGINERULE_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di UPDATE sul model SondaggiDomandePagineRule',
                'ruleName' => null,
                'parent' => ['AMMINISTRAZIONE_SONDAGGI']
            ],
            [
                'name' => 'SONDAGGIDOMANDEPAGINERULE_DELETE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di DELETE sul model SondaggiDomandePagineRule',
                'ruleName' => null,
                'parent' => ['AMMINISTRAZIONE_SONDAGGI']
            ],
        ];
    }
}
