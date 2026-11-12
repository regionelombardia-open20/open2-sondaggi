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
 * Class m200306_075522_insert_rules
 */
class m200306_075522_insert_rules extends Migration
{
    const TABLE         = '{{%sondaggi_domande_rule_mm}}';
    const TABLE_TIPO    = '{{%sondaggi_domande_rule}}';
    const TABLE_DOMANDE = '{{%sondaggi_domande}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_TIPO, 'namespace', $this->text()->defaultValue(null)->after('descrizione'));
        $this->addColumn(self::TABLE_TIPO, 'standard', $this->text()->defaultValue(null)->after('namespace'));

        $this->insert(self::TABLE_TIPO,
            [
            'nome' => 'Email',
            'descrizione' => 'Verifica che sia stata inserita una mail corretta',
            'standard' => 'email',
            'custom' => 0,
            'namespace' => null,
            'codice_custom' => null,
        ]);

        $this->insert(self::TABLE_TIPO,
            [
            'nome' => 'Url',
            'descrizione' => 'Verifica che sia stato inserito un indirizzo web corretto (è necessario aggiungere anche il prefisso http o https).',
            'standard' => 'url',
            'custom' => 0,
            'namespace' => null,
            'codice_custom' => null,
        ]);

        $this->insert(self::TABLE_TIPO,
            [
            'nome' => 'Codice fiscale italiano (persona fisica)',
            'descrizione' => 'Verifica che sia stato inserito un codice fiscale italiano corretto per una persona fisica',
            'standard' => null,
            'custom' => 1,
            'namespace' => \open20\amos\core\validators\CFValidator::className(),
            'codice_custom' => null,
        ]);

        $this->insert(self::TABLE_TIPO,
            [
            'nome' => 'Codice fiscale/Partita iva (formati italiani)',
            'descrizione' => 'Verifica che sia stato inserito un codice fiscale o una partita iva corretta',
            'standard' => null,
            'custom' => 1,
            'namespace' => \open20\amos\core\validators\CfPivaValidator::className(),
            'codice_custom' => null,
        ]);

        $this->insert(self::TABLE_TIPO,
            [
            'nome' => 'Partita iva',
            'descrizione' => 'Verifica che sia stata inserita una partita iva corretta',
            'standard' => null,
            'custom' => 1,
            'namespace' => \open20\amos\core\validators\PIVAValidator::className(),
            'codice_custom' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}