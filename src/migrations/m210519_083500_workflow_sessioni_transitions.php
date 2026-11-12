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

class m210519_083500_workflow_sessioni_transitions extends AmosMigrationWorkflow
{

    /**
     * @inheritdoc
     */
    protected function setWorkflow()
    {
        return ArrayHelper::merge(
                parent::setWorkflow(), $this->workflowConf(), $this->workflowStatusConf(),
                $this->workflowTransitionsConf(), $this->workflowMetadataConf()
        );
    }

    /**
     * In this method there are the new workflow configuration.
     * @return array
     */
    private function workflowConf()
    {
        return [
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW,
                'id' => SondaggiRisposteSessioni::WORKFLOW,
                'initial_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY
            ]
        ];
    }

    /**
     * In this method there are the new workflow statuses configurations.
     * @return array
     */
    private function workflowStatusConf()
    {
        return [
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_STATUS,
                'id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'label' => 'Bozza',
                'sort_order' => '0'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_STATUS,
                'id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'label' => 'Richiesta di invio',
                'sort_order' => '1'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_STATUS,
                'id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'label' => 'Inviato',
                'sort_order' => '2'
            ],
        ];
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
                'end_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_TRANSITION,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'start_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY,
                'end_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_TRANSITION,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'start_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY,
                'end_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_TRANSITION,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'start_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY,
                'end_status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY
            ],
        ];
    }

    /**
     * In this method there are the new workflow metadata configurations.
     * @return array
     */
    private function workflowMetadataConf()
    {
        return [
            // Bozza
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY,
                'key' => 'label',
                'value' => '#'.SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY.'_label'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY,
                'key' => 'buttonLabel',
                'value' => '#'.SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY.'_buttonLabel'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY,
                'key' => 'description',
                'value' => '#'.SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY.'_description'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_BOZZA_KEY,
                'key' => 'order',
                'value' => '1'
            ],
            // Richiesta di invio
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY,
                'key' => 'label',
                'value' => '#'.SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY.'_label'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY,
                'key' => 'buttonLabel',
                'value' => '#'.SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY.'_buttonLabel'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY,
                'key' => 'description',
                'value' => '#'.SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY.'_description'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_RICHIESTA_INVIO_KEY,
                'key' => 'order',
                'value' => '2'
            ],
            // Inviata
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY,
                'key' => 'label',
                'value' => '#'.SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY.'_label'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY,
                'key' => 'buttonLabel',
                'value' => '#'.SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY.'_buttonLabel'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY,
                'key' => 'description',
                'value' => '#'.SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY.'_description'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => SondaggiRisposteSessioni::WORKFLOW,
                'status_id' => SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO_KEY,
                'key' => 'order',
                'value' => '3'
            ],
        ];
    }
}