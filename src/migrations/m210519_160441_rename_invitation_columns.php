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
 * Class m210519_160441_rename_invitation_columns
 */
class m210519_160441_rename_invitation_columns extends Migration
{
    const TABLE_INVITATION = '{{%sondaggi_invitation_mm}}';
    const TABLE_USERS = '{{%sondaggi_users_invitation_mm}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn(self::TABLE_INVITATION, 'invitation_id', 'to_id');
        $this->addColumn(self::TABLE_INVITATION, 'invitation_id', $this->integer()->notNull()->after('sondaggi_id'));
        $this->renameColumn(self::TABLE_USERS, 'invitation_id', 'to_id');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn(self::TABLE_USERS, 'to_id', 'invitation_id');
        $this->dropColumn(self::TABLE_INVITATION, 'invitation_id');
        $this->renameColumn(self::TABLE_INVITATION, 'to_id', 'invitation_id');
        return true;
    }
}