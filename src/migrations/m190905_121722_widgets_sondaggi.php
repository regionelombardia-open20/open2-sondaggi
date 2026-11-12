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
 * Class m190227_143522_create_sondaggi_model_content
 */
class m190905_121722_widgets_sondaggi extends Migration
{
    const TABLE = '{{%sondaggi}}';
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('amos_widgets', ['child_of' => 'open20\amos\sondaggi\widgets\icons\WidgetIconSondaggiGeneral'],
            [
                'classname' => 'open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggiAll',
                'module' => 'sondaggi'
            ]);
        $this->update('amos_widgets', ['child_of' => 'open20\amos\sondaggi\widgets\icons\WidgetIconSondaggiGeneral'],
            [
                'classname' => 'open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggiOwnInterest',
                'module' => 'sondaggi'
            ]);
        $this->update('amos_widgets', ['child_of' => null],
            [
                'classname' => 'open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi',
                'module' => 'sondaggi'
            ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->update('amos_widgets', ['child_of' => 'open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi'],
            [
                'classname' => 'open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggiAll',
                'module' => 'sondaggi'
            ]);
        $this->update('amos_widgets', ['child_of' => 'open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi'],
            [
                'classname' => 'open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggiOwnInterest',
                'module' => 'sondaggi'
            ]);
        $this->update('amos_widgets', ['child_of' => 'open20\amos\sondaggi\widgets\icons\WidgetIconSondaggiGeneral'],
            [
                'classname' => 'open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi',
                'module' => 'sondaggi'
            ]);

    }

}
