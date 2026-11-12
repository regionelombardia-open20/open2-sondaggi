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

namespace open20\amos\sondaggi\validators;

/*
 * Classe che estende la Validator base per aggiungere
 * altri validatori alle domande custom, praticamente è necessaria
 * una classe per ogni nuovo metodo di validazione
 */

use yii\validators\Validator;
use open20\amos\sondaggi\AmosSondaggi;

class Cardinality extends Validator
{
    public $min;
    public $max;
    public $messageMin = 'Il numero di elementi selezionati non deve essere inferiore a {min}';
    public $messageMax = 'Il numero di elementi selezionati non deve essere superiore a {max}';

    public function validateAttribute($model, $attribute)
    {

        if (!empty($this->min) && count($model->$attribute) < $this->min) {
            $this->addError($model, $attribute,
                AmosSondaggi::t('amossondaggi', $this->messageMin, ['min' => $this->min]));
            return false;
        }

        if (!empty($this->max) && count($model->$attribute) > $this->max) {
            $this->addError($model, $attribute,
                AmosSondaggi::t('amossondaggi', $this->messageMax, ['max' => $this->max]));
            return false;
        }

        return true;
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $error_msg_min = AmosSondaggi::t('amossondaggi', $this->messageMin, ['min' => $this->min]);
        $error_msg_max = AmosSondaggi::t('amossondaggi', $this->messageMax, ['max' => $this->max]);

        $min = empty($this->min) ? 0 : $this->min;
        $max = empty($this->max) ? 0 : $this->max;

        return <<<JS
        var max = $max;
        var min = $min;
        var count = 0;
	if(typeof min != 'undefined' || typeof max != 'undefined'){
            if(typeof value != 'undefined'){
                if(Array.isArray(value)){
                        count = value.length;
                } else {
                        count = 1;
                }
            }
            if($("#div-$attribute").is(":visible")){
                if(typeof max != 'undefined' && count > max){
                    messages.push("$error_msg_max");
                    return false;
                } else if(typeof min != 'undefined' && count < min){
                    messages.push("$error_msg_min");
                    return false;
                }
                return false;
            }
	}		
        return true;
JS;
    }
}