<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240730_140624_add_tipologia_domanda
 */
class m240730_140624_add_tipologia_domanda extends Migration
{
    const TABLE = '{{%sondaggi_domande_tipologie}}';
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('sondaggi_domande_tipologie', ['id' => 15, 'tipologia' => 'Provincia', 'descrizione' => "Sarà visualizzato una tendina con le province italiane", 'attivo' => 1, 'html_type' =>'province']);
        $this->insert('sondaggi_domande_tipologie', ['id' => 16, 'tipologia' => 'Comune', 'descrizione' => "Sarà visualizzata una tendina con i comuni italiani", 'attivo' => 1, 'html_type' =>'comuni']);
        $this->insert('sondaggi_domande_tipologie', ['id' => 17, 'tipologia' => 'Comune collegato al campo provincia', 'descrizione' => "Sarà visualizzata una tendina dei comuni collegata al campo provincia", 'attivo' => 1, 'html_type' =>'province_comuni']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('sondaggi_domande_tipologie', ['id' => 17]);
        $this->delete('sondaggi_domande_tipologie', ['id' => 16]);
        $this->delete('sondaggi_domande_tipologie', ['id' => 15]);
    }

}
