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

class m170524_094650_alter_sondaggi extends \yii\db\Migration {

    const TABLE_PERMISSION = '{{%sondaggi_pubblicazione}}';

    public function safeUp() {
       $this->execute('SET FOREIGN_KEY_CHECKS=0;');
       $this->execute("
           
            ALTER TABLE `sondaggi_pubblicazione` ADD `mail_subject` VARCHAR(255) NULL DEFAULT NULL AFTER `entita_id`;
            ALTER TABLE `sondaggi_pubblicazione` ADD `mail_message` TEXT NULL DEFAULT NULL AFTER `mail_subject`;
            ALTER TABLE `sondaggi_pubblicazione` ADD `text_not_compilable` TEXT NULL DEFAULT NULL AFTER `mail_message`;
            ALTER TABLE `sondaggi_pubblicazione` ADD `text_end` TEXT NULL DEFAULT NULL AFTER `text_not_compilable`;
            ALTER TABLE `sondaggi_pubblicazione` ADD `text_end_title` VARCHAR(255) NULL DEFAULT NULL AFTER `text_end`;
            ALTER TABLE `sondaggi_pubblicazione` ADD `text_end_html` INTEGER DEFAULT 0 AFTER `text_end_title`;
            ALTER TABLE `sondaggi_pubblicazione` ADD `text_not_compilable_html` INTEGER DEFAULT 0 AFTER `text_end_html`;

               ");
       $this->execute('SET FOREIGN_KEY_CHECKS=0;');
    }

    public function safeDown() {
        echo "Down() non previsto per il file m170524_094650_alter_sondaggi";
        return false;
    }

}
