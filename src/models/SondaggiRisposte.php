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
use open20\amos\attachments\behaviors\FileBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class SondaggiRisposte
 * This is the model class for table "sondaggi_risposte".
 * @package open20\amos\sondaggi\models
 */
class SondaggiRisposte extends \open20\amos\sondaggi\models\base\SondaggiRisposte
{
    public $byBassRuleCwh = true;

    public $attachment;
    public $attachment_multiple;

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'risposta_libera'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        $i = 1;
        if($this->sondaggi_domande_id == 11){
            $i = 0;
        }
        return ArrayHelper::merge(parent::rules(), [
            //[['regola_pubblicazione', 'destinatari', 'validatori'], 'safe'],
            [['domanda_'.$this->sondaggi_domande_id], 'file', 'maxFiles' => $i],
        ]);
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
            [
                'fileBehavior' => [
                    'class' => FileBehavior::className()
                ],
            ]);
    }
}
