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
