<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-sondaggi
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
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;

class m210519_083000_workflow_sessioni_permission extends AmosMigrationPermissions
{

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return \yii\helpers\ArrayHelper::merge(
                $this->setPluginRoles(), $this->setWorkflowStatusPermissions()
        );
    }

    /**
     * Plugin roles.
     *
     * @return array
     */
    private function setPluginRoles()
    {
        return [
        ];
    }

    /**
     * Workflow statuses permissions
     *
     * @return array
     */
    private function setWorkflowStatusPermissions()
    {
        return [
            [
                'name' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Compilazione del sondaggio',
                'parent' => ['COMPILA_SONDAGGIO']
            ],
            [
                'name' => SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Richiesta di invio compilazione',
                'parent' => ['SondaggiSessioniWorkflowGeneric']
            ],
            [
                'name' => SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Invio compilazione',
                'parent' => ['SondaggiSessioniWorkflowGeneric']
            ],
        ];
    }
}