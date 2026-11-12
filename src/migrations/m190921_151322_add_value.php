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
 * Class m190921_151322_add_value
 */
class m190921_151322_add_value extends Migration
{
    const TABLE_MAP = '{{%sondaggi_map}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(self::TABLE_MAP,
            [
            'campo' => 'email',
            'tabella' => 'user',
            'descrizione' => 'Email dell\'utente, campo del profilo.',
            'obbligatorio' => 1,
        ]);

        $this->insert(self::TABLE_MAP,
            [
            'campo' => 'nome',
            'tabella' => 'user_profile',
            'descrizione' => 'Nome dell\'utente, campo del profilo.',
            'obbligatorio' => 1,
        ]);

        $this->insert(self::TABLE_MAP,
            [
            'campo' => 'cognome',
            'tabella' => 'user_profile',
            'descrizione' => 'Cognome dell\'utente, campo del profilo.',
            'obbligatorio' => 1,
        ]);               

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(self::TABLE_MAP);
        echo "Cancellazione dei contenuti avvenuta correttamente.";
        return true;
    }
}