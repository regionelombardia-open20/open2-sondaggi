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
use yii\helpers\ArrayHelper;
use open20\amos\core\migration\AmosMigrationWorkflow;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;

class m210610_161458_workflow_sessioni_transitions extends AmosMigrationWorkflow
{

    /**
     * @inheritdoc
     */
    protected function setWorkflow()
    {
        return ArrayHelper::merge(
                parent::setWorkflow(),
                $this->workflowTransitionsConf()
        );
    }

    /**
     * In this method there are the new workflow status transitions configurations.
     * @return array
     */
    private function workflowTransitionsConf()
    {
        return [
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_TRANSITION,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'start_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY,
                'end_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY
            ]
        ];
    }
}