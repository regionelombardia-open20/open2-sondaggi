<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\models\base
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

use open20\amos\core\user\User;
use Yii;
use open20\amos\sondaggi\AmosSondaggi;

/**
 * This is the base-model class for table "sondaggi_risposte_sessioni".
 *
 * @property integer $id
 * @property string $session_id
 * @property string $unique_id
 * @property string $begin_date
 * @property string $end_date
 * @property string $session_tmp
 * @property integer $completato
 * @property integer $user_id
 * @property integer $organization_id
 * @property integer $sondaggi_id
 * @property integer $entita_id
 * @property integer $sondaggi_accessi_servizi_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $version
 *
 * @property \open20\amos\sondaggi\models\SondaggiRisposte[] $sondaggiRispostes
 * @property \open20\amos\sondaggi\models\Sondaggi $sondaggi
 * @property \common\models\User $user
 */
class SondaggiRisposteSessioni extends \open20\amos\core\record\Record {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'sondaggi_risposte_sessioni';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['begin_date', 'end_date', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['session_tmp'], 'string'],
            [['completato', 'user_id', 'organization_id', 'sondaggi_id', 'entita_id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['sondaggi_id'], 'required'],
            [['session_id', 'unique_id', 'field_extra', 'lang'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => AmosSondaggi::t('amossondaggi', 'ID'),
            'session_id' => AmosSondaggi::t('amossondaggi', 'Id Sessione'),
            'unique_id' => AmosSondaggi::t('amossondaggi', 'Id Unico'),
            'begin_date' => AmosSondaggi::t('amossondaggi', 'Inizio compilazione'),
            'end_date' => AmosSondaggi::t('amossondaggi', 'Fine compilazione'),
            'session_tmp' => AmosSondaggi::t('amossondaggi', 'Tmp Sessione'),
            'completato' => AmosSondaggi::t('amossondaggi', 'Completato'),
            'field_extra' => AmosSondaggi::t('amossondaggi', 'Campo extra'),
            'lang' => AmosSondaggi::t('amossondaggi', 'Lingua'),
            'user_id' => AmosSondaggi::t('amossondaggi', 'Utente'),
            'sondaggi_id' => AmosSondaggi::t('amossondaggi', 'Sondaggio'),
            'entita_id' => AmosSondaggi::t('amossondaggi', 'Attività'),
            'created_at' => AmosSondaggi::t('amossondaggi', 'Creato il'),
            'updated_at' => AmosSondaggi::t('amossondaggi', 'Aggiornato il'),
            'deleted_at' => AmosSondaggi::t('amossondaggi', 'Cancellato il'),
            'created_by' => AmosSondaggi::t('amossondaggi', 'Creato da'),
            'updated_by' => AmosSondaggi::t('amossondaggi', 'Aggiornato da'),
            'deleted_by' => AmosSondaggi::t('amossondaggi', 'Cancellato da'),
            'version' => AmosSondaggi::t('amossondaggi', 'Versione'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiRispostes() {
        return $this->hasMany(\open20\amos\sondaggi\models\SondaggiRisposte::className(), ['sondaggi_risposte_sessioni_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggi() {
        return $this->hasOne(\open20\amos\sondaggi\models\Sondaggi::className(), ['id' => 'sondaggi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization() {
        if (\open20\amos\sondaggi\AmosSondaggi::instance()->compilationToOrganization)
            return $this->hasOne(\open20\amos\organizzazioni\models\Profilo::className(), ['id' => 'organization_id']);
        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getPeiAccessiServiziFacilitazione() {
//        return $this->hasOne(\backend\modules\peipoint\models\AccessiServiziFacilitazione::className(), ['id' => 'sondaggi_accessi_servizi_id']);
//    }

}
