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

use Yii;
use open20\amos\sondaggi\AmosSondaggi;

/**
 * This is the base-model class for table "sondaggi_risposte".
 *
 * @property integer $id
 * @property string $risposta_libera
 * @property integer $sondaggi_domande_id
 * @property integer $sondaggi_risposte_predefinite_id
 * @property integer $ordinamento
 * @property integer $sondaggi_accessi_servizi_id
 * @property integer $sondaggi_risposte_sessioni_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $version
 *
 * @property \open20\amos\sondaggi\models\PeiAccessiServiziFacilitazione $peiAccessiServiziFacilitazione
 * @property \open20\amos\sondaggi\models\SondaggiDomande $sondaggiDomande
 * @property \open20\amos\sondaggi\models\SondaggiRispostePredefinite $sondaggiRispostePredefinite
 * @property \open20\amos\sondaggi\models\SondaggiRisposteSessioni $sondaggiRisposteSessioni
 */
class SondaggiRisposte extends \open20\amos\core\record\Record
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sondaggi_risposte';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['risposta_libera'], 'string'],
            [['sondaggi_domande_id', 'sondaggi_risposte_sessioni_id'], 'required'],
            [['sondaggi_domande_id', 'sondaggi_risposte_predefinite_id', 'sondaggi_accessi_servizi_id', 'sondaggi_risposte_sessioni_id',
                'created_by', 'updated_by', 'deleted_by', 'version', 'ordinamento'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosSondaggi::t('amossondaggi', 'ID'),
            'risposta_libera' => AmosSondaggi::t('amossondaggi', 'Risposta libera'),
            'sondaggi_domande_id' => AmosSondaggi::t('amossondaggi', 'Domanda'),
            'sondaggi_risposte_predefinite_id' => AmosSondaggi::t('amossondaggi', 'Risposta predefinita'),
            'ordinamento' => AmosSondaggi::t('amossondaggi', 'Ordinamento'),
            'sondaggi_accessi_servizi_id' => AmosSondaggi::t('amossondaggi', 'Pei Accessi Servizi Facilitazione ID'),
            'sondaggi_risposte_sessioni_id' => AmosSondaggi::t('amossondaggi', 'Utente'),
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
    public function getPeiAccessiServiziFacilitazione()
    {
        return $this->hasOne(\open20\amos\sondaggi\models\PeiAccessiServiziFacilitazione::className(),
                ['id' => 'sondaggi_accessi_servizi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiDomande()
    {
        return $this->hasOne(\open20\amos\sondaggi\models\SondaggiDomande::className(),
                    ['id' => 'sondaggi_domande_id'])
                ->andWhere([\open20\amos\sondaggi\models\SondaggiDomande::tableName().'.deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiRispostePredefinite()
    {
        return $this->hasOne(\open20\amos\sondaggi\models\SondaggiRispostePredefinite::className(),
                    ['id' => 'sondaggi_risposte_predefinite_id'])
                ->andWhere([\open20\amos\sondaggi\models\SondaggiRispostePredefinite::tableName().'.deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiRisposteSessioni()
    {
        return $this->hasOne(\open20\amos\sondaggi\models\SondaggiRisposteSessioni::className(),
                ['id' => 'sondaggi_risposte_sessioni_id']);
    }
}