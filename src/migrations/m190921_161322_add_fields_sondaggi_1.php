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
 * Class m190921_161322_add_fields_sondaggi_1
 */
class m190921_161322_add_fields_sondaggi_1 extends Migration
{
    const TABLE         = '{{%sondaggi}}';
    const TABLE_DOMANDE = '{{%sondaggi_domande}}';
    const TABLE_MAP     = '{{%sondaggi_map}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE, 'frontend', $this->integer()->defaultValue(0)->after('sondaggi_temi_id'));
        $this->addColumn(self::TABLE, 'thank_you_page', $this->text()->defaultValue(null)->after('frontend'));
        $this->addColumn(self::TABLE, 'abilita_registrazione',
            $this->integer()->defaultValue(0)->after('thank_you_page'));
        $this->addColumn(self::TABLE_DOMANDE, 'introduzione', $this->text()->defaultValue(null)->after('id'));
        $this->addColumn(self::TABLE_DOMANDE, 'sondaggi_map_id', $this->integer()->defaultValue(null)->after('id'));

        $this->addForeignKey(
            'fk-sondaggi_domande-map_id', self::TABLE_DOMANDE, 'sondaggi_map_id', self::TABLE_MAP, 'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-sondaggi_domande-map_id', self::TABLE_DOMANDE);
        $this->dropColumn(self::TABLE_DOMANDE, 'sondaggi_map_id');
        $this->dropColumn(self::TABLE_DOMANDE, 'introduzione');
        $this->dropColumn(self::TABLE, 'abilita_registrazione');
        $this->dropColumn(self::TABLE, 'thank_you_page');
        $this->dropColumn(self::TABLE, 'frontend');
    }
}