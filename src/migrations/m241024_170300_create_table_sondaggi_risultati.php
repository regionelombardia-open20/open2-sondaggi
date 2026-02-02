<?php

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