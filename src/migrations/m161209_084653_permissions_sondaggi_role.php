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

class m161209_084653_permissions_sondaggi_role extends \yii\db\Migration {

    const TABLE_PERMISSION = '{{%auth_item_child}}';

    public function safeUp() {
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGI_CREATE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGI_DELETE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGI_READ',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGI_UPDATE',
        ]);

        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDE_CREATE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDE_DELETE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDE_READ',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDE_UPDATE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDEPAGINE_CREATE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDEPAGINE_DELETE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDEPAGINE_READ',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDEPAGINE_UPDATE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDETIPOLOGIE_CREATE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDETIPOLOGIE_DELETE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDETIPOLOGIE_READ',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIDOMANDETIPOLOGIE_UPDATE',
        ]);

        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIRISPOSTEPREDEFINITE_CREATE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIRISPOSTEPREDEFINITE_DELETE',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIRISPOSTEPREDEFINITE_READ',
        ]);
        $this->insert(self::TABLE_PERMISSION, [
            'parent' => 'AMMINISTRAZIONE_SONDAGGI',
            'child' => 'SONDAGGIRISPOSTEPREDEFINITE_UPDATE',
        ]);
    }

    public function safeDown() {
        echo "Down() non previsto per il file m161209_084653_permissions_sondaggi_role";
        return false;
    }

}
