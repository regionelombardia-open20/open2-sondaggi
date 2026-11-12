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
use open20\amos\sondaggi\models\base\Sondaggi;

/**
 * Class m210507_102732_add_fields_sondaggi
 */
class m210507_102732_add_fields_sondaggi extends Migration
{
    private $tableName;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->tableName = Sondaggi::tableName();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'sottotitolo', $this->text()->null()->defaultValue(null)->after('titolo'));
        $this->addColumn($this->tableName, 'publish_date', $this->date());
        $this->addColumn($this->tableName, 'close_date', $this->date()->after('publish_date'));
        $this->addColumn($this->tableName, 'send_pdf_to_compiler', $this->boolean()->defaultValue(0)->after('send_pdf_via_email'));
        $this->addColumn($this->tableName, 'send_pdf_via_email_closed', $this->boolean()->defaultValue(0)->after('send_pdf_to_compiler'));
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'sottotitolo');
        return true;
    }
}