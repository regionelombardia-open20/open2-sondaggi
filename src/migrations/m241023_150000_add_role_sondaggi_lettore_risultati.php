<?php

use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m241023_150000_add_role_sondaggi_lettore_risultati
 */
class m241023_150000_add_role_sondaggi_lettore_risultati extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
            [
                'name' =>  'SONDAGGI_LETTORE_RISULTATI',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Ruolo che permette di vedere solo l\'andamento e i risultati dei sondaggi',
                'ruleName' => null,
                'children' => ['TASKMANAGER_CREATE']
            ]
        ];
    }
}