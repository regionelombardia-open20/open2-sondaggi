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

namespace open20\amos\sondaggi\models\base;

use Yii;

/**
 * This is the base-model class for table "sondaggi_comunication".
 *
 * @property integer $id
 * @property integer $sondaggi_id
 * @property string $name
 * @property string $subject
 * @property string $message
 * @property integer $target
 * @property integer $type
 * @property string $query
 * @property integer $count
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class SondaggiComunication extends \open20\amos\core\record\Record
{
    public $isSearch = false;

   
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sondaggi_communication';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sondaggi_id', 'name', 'subject', 'message', 'type', 'target'], 'required'],
            [['sondaggi_id', 'count', 'created_by', 'updated_by', 'deleted_by', 'target'], 'integer'],
            [['message', 'query'], 'string'],
            ['email_test', 'email'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amossondaggi', 'ID'),
            'sondaggi_id' => Yii::t('amossondaggi', 'Sondaggi ID'),
            'name' => Yii::t('amossondaggi', 'Name'),
            'subject' => Yii::t('amossondaggi', 'Subject'),
            'message' => Yii::t('amossondaggi', 'Message'),
            'target' => Yii::t('amossondaggi', 'Target'),
            'type' => Yii::t('amossondaggi', 'Filtro'),
            'query' => Yii::t('amossondaggi', 'Query'),
            'count' => Yii::t('amossondaggi', 'Count'),
            'created_at' => Yii::t('amossondaggi', 'Creato il'),
            'updated_at' => Yii::t('amossondaggi', 'Aggiornato il'),
            'deleted_at' => Yii::t('amossondaggi', 'Cancellato il'),
            'created_by' => Yii::t('amossondaggi', 'Creato da'),
            'updated_by' => Yii::t('amossondaggi', 'Aggiornato da'),
            'deleted_by' => Yii::t('amossondaggi', 'Cancellato da'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggi(){
        return $this->hasOne(\open20\amos\sondaggi\models\Sondaggi::className(), ['id' => 'sondaggi_id']);
    }

}