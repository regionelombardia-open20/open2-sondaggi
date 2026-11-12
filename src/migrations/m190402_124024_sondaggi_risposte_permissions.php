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
use yii\rbac\Permission;

/**
 * Class m180911_081424_new_compila_sondaggi_widgets_permissions
 */
class m190402_124024_sondaggi_risposte_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        $prefixStr = 'Permissions for the dashboard for the widget ';
        return [
            [
                'name' => 'readOwnSondaggiRisposte',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if read sondaggi risposte',
                'ruleName' => \open20\amos\core\rules\DefaultOwnContentRule::className(),
                'parent' => ['COMPILATORE_SONDAGGI'],
                'children' => ['SONDAGGIRISPOSTE_READ']
            ],
            [
                'name' => 'updateOwnSondaggiRisposte',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if read sondaggi risposte',
                'ruleName' => \open20\amos\core\rules\DefaultOwnContentRule::className(),
                'parent' => ['COMPILATORE_SONDAGGI'],
                'children' => ['SONDAGGIRISPOSTE_UPDATE']
            ],
            [
                'name' => 'deleteOwnSondaggiRisposte',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if read sondaggi risposte',
                'ruleName' => \open20\amos\core\rules\DefaultOwnContentRule::className(),
                'parent' => ['COMPILATORE_SONDAGGI'],
                'children' => ['SONDAGGIRISPOSTE_DELETE']
            ],
           [
                'name' => 'SONDAGGIRISPOSTE_READ',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if read sondaggi risposte',
                'parent' => ['AMMINISTRAZIONE_SONDAGGI']
            ],
            [
                'name' => 'SONDAGGIRISPOSTE_DELETE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if delete sondaggi risposte',
                'parent' => ['AMMINISTRAZIONE_SONDAGGI']
            ],
            [
                'name' => 'SONDAGGIRISPOSTE_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if update sondaggi risposte',
                'parent' => ['AMMINISTRAZIONE_SONDAGGI']
            ],


        ];
    }
}
