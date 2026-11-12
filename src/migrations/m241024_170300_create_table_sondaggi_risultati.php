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

use open20\amos\core\migration\AmosMigrationTableCreation;

/**
 * Class m241024_170300_create_table_sondaggi_risultati
 */
class m241024_170300_create_table_sondaggi_risultati extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%sondaggi_risultati}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->setAddCreatedUpdatedFields(true);
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'excel_rows' => $this->integer(11)->comment('Excel file number of rows'),
            'only_completed' => $this->boolean()->defaultValue(null),
            'sondaggi_risultati_formato_id' => $this->integer(11)->comment('FK to format'),
            'sondaggi_id' => $this->integer(11)->comment('FK to sondaggi'),
            'task_manager_job_id' => $this->integer(11)->comment('FK to task manager job'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey('risultati_to_risultati_formato_fk', $this->tableName, 'sondaggi_risultati_formato_id', 'sondaggi_risultati_formato', 'id');
        $this->addForeignKey('risultati_to_sondaggi_fk', $this->tableName, 'sondaggi_id', 'sondaggi', 'id');
    }
}