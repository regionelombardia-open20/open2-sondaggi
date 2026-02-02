<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m190227_143522_create_sondaggi_model_content
 */
class m240206_160422_create_sondaggi_context extends Migration
{
    const TABLE = '{{%sondaggi_context}}';
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) === null)
        {
            $this->createTable(self::TABLE, [
                'id' => Schema::TYPE_PK,
                'sondaggi_id' => $this->integer()->defaultValue(null),
                'context_model_id' => $this->integer()->defaultValue(null),
                'context_id' => $this->integer()->defaultValue(null)->comment('context id'),
                'context_url' => $this->string()->defaultValue(null)->comment('Url'),
                'label' => $this->string()->defaultValue(null)->comment('Label'),
                'created_at' => Schema::TYPE_DATETIME . " NULL DEFAULT NULL COMMENT 'Creato il'",
                'updated_at' => Schema::TYPE_DATETIME . " NULL DEFAULT NULL COMMENT 'Aggiornato il'",
                'deleted_at' => Schema::TYPE_DATETIME . " NULL DEFAULT NULL COMMENT 'Cancellato il'",
                'created_by' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Creato da'",
                'updated_by' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Aggiornato da'",
                'deleted_by' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Cancellato da'",
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
            $this->addForeignKey('fk_sondaggi_context_sondaggi1','sondaggi_context', 'sondaggi_id', 'sondaggi', 'id');
            $this->addForeignKey('fk_sondaggi_context_model_classn1','sondaggi_context', 'context_model_id', 'models_classname', 'id');
        }
        else
        {
            echo "Nessuna creazione eseguita in quanto la tabella esiste gia'";
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        if ($this->db->schema->getTableSchema(self::TABLE, true) !== null)
        {
            $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
            $this->dropTable(self::TABLE);
            $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
        }
        else
        {
            echo "Nessuna cancellazione eseguita in quanto la tabella non esiste";
        }
        
        return true;
    }

}
