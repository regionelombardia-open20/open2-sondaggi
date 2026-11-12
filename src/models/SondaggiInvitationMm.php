<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\models
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

namespace open20\amos\sondaggi\models;

use yii\helpers\ArrayHelper;
use open20\amos\sondaggi\AmosSondaggi;

/**
 * Class SondaggiDomandePagine
 * This is the model class for table "sondaggi_domande_pagine".
 * @package open20\amos\sondaggi\models
 */
class SondaggiInvitationMm extends \open20\amos\sondaggi\models\base\SondaggiInvitationMm
{
    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'id'
        ];
    }

    public function getTo() {
        if (AmosSondaggi::instance()->compilationToOrganization) {
            return $this->hasOne(\open20\amos\organizzazioni\models\Profilo::className(), ['id' => 'to_id']);
        } else {
            return $this->hasOne(\open20\amos\core\user\User::className(), ['id' => 'to_id']);
        }
    }

    public function getSessions() {
        if (AmosSondaggi::instance()->compilationToOrganization) {
            return $this->hasMany(\open20\amos\sondaggi\models\SondaggiRisposteSessioni::className(), ['organization_id' => 'to_id', 'sondaggi_id' => 'sondaggi_id']);
        } else {
            return $this->hasMany(\open20\amos\sondaggi\models\SondaggiRisposteSessioni::className(), ['user_id' => 'to_id', 'sondaggi_id' => 'sondaggi_id']);
        }
    }

    public function getLastSession() {
        if (AmosSondaggi::instance()->compilationToOrganization) {
            return $this->hasOne(\open20\amos\sondaggi\models\SondaggiRisposteSessioni::className(), ['organization_id' => 'to_id', 'sondaggi_id' => 'sondaggi_id'])->orderBy(['end_date' => SORT_DESC])->limit([0, 1]);
        } else {
            return $this->hasOne(\open20\amos\sondaggi\models\SondaggiRisposteSessioni::className(), ['user_id' => 'to_id', 'sondaggi_id' => 'sondaggi_id'])->orderBy(['end_date' => SORT_DESC])->limit([0, 1]);
        }
    }
}
