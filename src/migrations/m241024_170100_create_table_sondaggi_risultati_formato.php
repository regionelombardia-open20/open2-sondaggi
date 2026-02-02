<?php

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