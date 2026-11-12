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
 * Class m200615_181321_add_fields_sondaggi_domande
 */
class m200615_181321_add_fields_sondaggi_domande extends Migration {

    const TABLE_DOMANDE = '{{%sondaggi_domande}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn(self::TABLE_DOMANDE, 'modello_risposte_id',
                $this->integer()->defaultValue(null)->after('nome_classe_validazione'));

        $this->addForeignKey('fk_modelli_predef_sondaggi_domande', self::TABLE_DOMANDE, 'modello_risposte_id', 'sondaggi_modelli_predefiniti', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn(self::TABLE_DOMANDE, 'modello_risposte_id');

        $this->dropForeignKey('fk_modelli_predef_sondaggi_domande', self::TABLE_DOMANDE);
    }

}
