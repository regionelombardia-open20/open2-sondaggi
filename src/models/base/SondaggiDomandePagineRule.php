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
 * This is the base-model class for table "sondaggi_domande_pagine_rule".
 *
 * @property integer $id
 * @property string $title
 * @property string $logical_operator
 * @property integer $rule
 * @property string $value
 * @property string $message
 * @property integer $sondaggi_domande_pagine_id
 * @property string $fields
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\sondaggi\models\SondaggiDomandePagine $sondaggiDomandePagine
 */
class SondaggiDomandePagineRule extends \open20\amos\core\record\Record {

    public $isSearch = false;

    public $fields2;
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'sondaggi_domande_pagine_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['rule', 'sondaggi_domande_pagine_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['value'], 'number'],
            [['title', 'logical_operator', 'fields2', 'message', 'rule', 'value'], 'required'],
            [['fields'], 'string'],
            [['created_at', 'updated_at', 'deleted_at', 'fields2'], 'safe'],
            [['title', 'logical_operator', 'message'], 'string', 'max' => 255],
            [['sondaggi_domande_pagine_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\sondaggi\models\SondaggiDomandePagine::className(), 'targetAttribute' => ['sondaggi_domande_pagine_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('amossondaggi', 'ID'),
            'title' => Yii::t('amossondaggi', 'Titolo'),
            'logical_operator' => Yii::t('amossondaggi', 'Operatore logico'),
            'rule' => Yii::t('amossondaggi', 'Regola'),
            'value' => Yii::t('amossondaggi', 'Valore'),
            'message' => Yii::t('amossondaggi', 'Messaggio di errore'),
            'sondaggi_domande_pagine_id' => Yii::t('amossondaggi', 'Pagina'),
            'fields' => Yii::t('amossondaggi', 'Campi sottoposti al controllo'),
            'fields2' => Yii::t('amossondaggi', 'Campi sottoposti al controllo'),
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
    public function getSondaggiDomandePagine() {
        return $this->hasOne(\open20\amos\sondaggi\models\SondaggiDomandePagine::className(), ['id' => 'sondaggi_domande_pagine_id']);
    }
}
