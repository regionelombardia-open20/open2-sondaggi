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
 * Class m241024_170100_create_table_sondaggi_risultati_formato
 */
class m241024_170100_create_table_sondaggi_risultati_formato extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%sondaggi_risultati_formato}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->setAddCreatedUpdatedFields(true);
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'label' => $this->string(255)->comment('Format label'),
            'code' => $this->string(255)->comment('Format code'),
        ];
    }

    protected function afterTableCreation()
    {
        $this->insert($this->tableName,['id' => 1, 'label' => 'Excel: file Excel contenente tutti i dati delle compilazioni degli utenti', 'code' => 'excel']);
        $this->insert($this->tableName,['id' => 2, 'label' => 'PDF: file Zip contente le singole compilazioni in formato PDF', 'code' => 'pdf']);
        parent::afterTableCreation();
    }
}