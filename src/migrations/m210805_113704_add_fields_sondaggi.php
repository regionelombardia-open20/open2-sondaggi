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
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;

/**
 * Class m210805_113704_add_fields_sondaggi
 */
class m210805_113704_add_fields_sondaggi extends Migration
{
    private $tableName;
    private $tableName2;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->tableName = Sondaggi::tableName();
        $this->tableName2 = SondaggiRisposteSessioni::tableName();
    }

    /**, 'code'
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'use_get_language', $this->integer()->defaultValue(0)->after('forza_lingua'));
        $this->addColumn($this->tableName, 'field_extra', $this->integer()->defaultValue(0)->after('use_get_language'));
        $this->addColumn($this->tableName2, 'lang', $this->string()->null()->after('user_id'));
        $this->addColumn($this->tableName2, 'field_extra', $this->string()->null()->after('lang'));
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'use_get_language');
        $this->dropColumn($this->tableName, 'field_extra');
        $this->dropColumn($this->tableName2, 'lang');
        $this->dropColumn($this->tableName2, 'field_extra');
        return true;
    }
}
