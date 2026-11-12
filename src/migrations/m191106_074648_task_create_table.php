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

class m191106_074648_task_create_table extends Migration {

    private $tabella = null;

    public function safeUp() {
        $this->execute("SET FOREIGN_KEY_CHECKS=0;");
        $this->execute("     
CREATE TABLE IF NOT EXISTS `task_sondaggi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `command` VARCHAR(255) NULL DEFAULT NULL,
  `status` INT(11) NULL DEFAULT NULL,
  `filename` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Creato il',
  `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Aggiornato il',
  `deleted_at` DATETIME NULL DEFAULT NULL COMMENT 'Cancellato il',
  `created_by` INT(11) NULL DEFAULT NULL COMMENT 'Creato da',
  `updated_by` INT(11) NULL DEFAULT NULL COMMENT 'Aggiornato da',
  `deleted_by` INT(11) NULL DEFAULT NULL COMMENT 'Cancellato da',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;");

        $this->execute("SET FOREIGN_KEY_CHECKS=1;");

        return true;
    }

    /**
     *
     * @return boolean
     */
    public function safeDown() {

        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->dropTable("task_sondaggi");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");

        return true;
    }

}
