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

class CheckSum extends Validator {

    public $operator = '==';
    public $value = 100;
    public $message = 'La somma degli elementi deve essere uguale a {value}';
    public $ruleId;

    public function validateAttribute($model, $attribute) {

        if (empty($this->ruleId)) {
            return true;
        }
        $rule = \open20\amos\sondaggi\models\SondaggiDomandePagineRule::findOne($this->ruleId);
        $value = floatval($rule->value);
        $operator = $rule->logical_operator;
        $fields = json_decode($rule->fields);
        $compareValue = 0;
        $post = \Yii::$app->request->post();
        $pagId = 'Pagina_'.$rule->sondaggi_domande_pagine_id;
        foreach ($fields as $v) {
            $fieldName = 'domanda_'.$v;
            if (!empty($post[$pagId]) && !empty($post[$pagId][$fieldName])) {
                $compareValue = floatval($compareValue) + floatval($post[$pagId][$fieldName]);
            }
        }        
        $ok = false;
        switch ($operator) {
            case '==':
                $ok = ($compareValue == $value ? true : false);
                break;
            case '>=':
                $ok = ($compareValue >= $value ? true : false);
                break;
            case '<=':
                $ok = ($compareValue <= $value ? true : false);
                break;
            case '>':
                $ok = ($compareValue > $value ? true : false);
                break;
            case '<':
                $ok = ($compareValue < $value ? true : false);
                break;
        }
        if ($ok) {
            return true;
        }
        $this->addError($model, $attribute,
                AmosSondaggi::t('amossondaggi', (empty($rule->message) ? AmosSondaggi::t('amossondaggi', $this->message) : AmosSondaggi::t('amossondaggi', $rule->message)), [
                    'value' => $rule->value,
        ]));
        return false;
    }
}
