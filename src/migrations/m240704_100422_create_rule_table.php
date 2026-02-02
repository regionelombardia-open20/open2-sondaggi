<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240704_100422_create_rule_table
 */
class m240704_100422_create_rule_table extends Migration {

    const TABLE = '{{%sondaggi_domande_pagine_rule}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        if ($this->db->schema->getTableSchema(self::TABLE, true) === null) {
            $this->createTable(self::TABLE, [
                'id' => Schema::TYPE_PK,
                'title' => $this->string()->comment('Titolo'),
                'logical_operator' => $this->string()->comment('Operatore logico'),
                'rule' => $this->integer()->comment('Regola'),
                'value' => $this->decimal(11, 2)->defaultValue(100)->comment('Valore'),
                'message' => $this->string()->defaultValue(null)->comment('Messaggio di errore'),
                'sondaggi_domande_pagine_id' => $this->integer()->comment('Pagina'),
                'fields' => $this->text()->comment('Campi sottoposti al controllo'),
                'created_at' => Schema::TYPE_DATETIME . " NULL DEFAULT NULL COMMENT 'Creato il'",
                'updated_at' => Schema::TYPE_DATETIME . " NULL DEFAULT NULL COMMENT 'Aggiornato il'",
                'deleted_at' => Schema::TYPE_DATETIME . " NULL DEFAULT NULL COMMENT 'Cancellato il'",
                'created_by' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Creato da'",
                'updated_by' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Aggiornato da'",
                'deleted_by' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Cancellato da'",
                    ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
            $this->addForeignKey('fk_sondaggi_domande_pagine_rule_pagine1', 'sondaggi_domande_pagine_rule', 'sondaggi_domande_pagine_id', 'sondaggi_domande_pagine', 'id');
        } else {
            echo "Nessuna creazione eseguita in quanto la tabella esiste gia'";
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {

        if ($this->db->schema->getTableSchema(self::TABLE, true) !== null) {
            $this->dropTable(self::TABLE);
        } else {
            echo "Nessuna cancellazione eseguita in quanto la tabella non esiste";
        }

        return true;
    }
}
