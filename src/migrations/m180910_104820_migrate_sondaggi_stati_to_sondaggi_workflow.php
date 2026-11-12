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

use open20\amos\core\migration\libs\common\MigrationCommon;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiStato;
use yii\db\ActiveQuery;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class m180910_104820_migrate_sondaggi_stati_to_sondaggi_workflow
 */
class m180910_104820_migrate_sondaggi_stati_to_sondaggi_workflow extends Migration
{
    /**
     * @var array $oldStatuses
     */
    private $oldStatuses = [];

    /**
     * @var array $statusMap
     */
    private $statusMap = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->oldStatuses = ArrayHelper::map(SondaggiStato::find()->all(), 'id', 'stato');
        $this->statusMap = [
            'BOZZA' => Sondaggi::WORKFLOW_STATUS_BOZZA,
            'DA_VALIDARE' => Sondaggi::WORKFLOW_STATUS_DAVALIDARE,
            'VALIDATO' => Sondaggi::WORKFLOW_STATUS_VALIDATO,
            'NON_VALIDATO' => Sondaggi::WORKFLOW_STATUS_BOZZA
        ];
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        /** @var ActiveQuery $query */
        $query = Sondaggi::find();
        $sondaggi = $query->all();

        $allOk = true;

        foreach ($sondaggi as $sondaggio) {
            /** @var Sondaggi $sondaggio */
            if ($sondaggio->status) {
                continue;
            }
            $newStatus = $this->mapOldToNewStatus($sondaggio->sondaggi_stato_id);
            if (!$newStatus) {
                MigrationCommon::printCheckStructureError($sondaggio->attributes, 'Stato non trovato per il sondaggio');
                $allOk = false;
                continue;
            }
            $sondaggio->status = $newStatus;
            $sondaggio->detachBehaviors();
            $ok = $sondaggio->save(false);
            if (!$ok) {
                MigrationCommon::printCheckStructureError($sondaggio->attributes, "Errore durante il salvataggio del sondaggio");
                $allOk = false;
                continue;
            }
        }

        if ($allOk) {
            MigrationCommon::printConsoleMessage('Aggiornamento stati dei sondaggi avvenuto correttamente');
        }

        return true;
    }

    /**
     * @param string $oldStatus
     * @return string
     */
    private function mapOldToNewStatus($oldStatusId)
    {
        $oldStatus = $this->oldStatuses[$oldStatusId];
        if ($oldStatus && $this->statusMap[$oldStatus]) {
            return $this->statusMap[$oldStatus];
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180910_104820_migrate_sondaggi_stati_to_sondaggi_workflow cannot be reverted.\n";
        return false;
    }
}
