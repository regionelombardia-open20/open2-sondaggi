<?php

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
