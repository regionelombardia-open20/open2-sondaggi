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

class m161209_084651_permissions_sondaggi_risposte_predefinite extends \yii\db\Migration
{

    const TABLE_PERMISSION = '{{%auth_item}}';

    public function safeUp()
    {
        $this->insert(self::TABLE_PERMISSION, [
            'name' => 'SONDAGGIRISPOSTEPREDEFINITE_CREATE',
            'type' => '2',
            'description' => 'Permesso di CREATE sul model SONDAGGIRISPOSTEPREDEFINITE'
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'name' => 'SONDAGGIRISPOSTEPREDEFINITE_DELETE',
            'type' => '2',
            'description' => 'Permesso di DELETE sul model SONDAGGIRISPOSTEPREDEFINITE'
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'name' => 'SONDAGGIRISPOSTEPREDEFINITE_READ',
            'type' => '2',
            'description' => 'Permesso di READ sul model SONDAGGIRISPOSTEPREDEFINITE'
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'name' => 'SONDAGGIRISPOSTEPREDEFINITE_UPDATE',
            'type' => '2',
            'description' => 'Permesso di UPDATE sul model SONDAGGIRISPOSTEPREDEFINITE'
        ]);                         
    }

    public function safeDown()
    {
        echo "Down() non previsto per il file m161209_084651_permissions_sondaggi_risposte_predefinite";
        return false;
    }

}