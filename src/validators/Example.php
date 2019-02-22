<?php

namespace lispa\amos\sondaggi\validators;

/*
 * Classe che estende la Validator base per aggiungere
 * altri validatori alle domande custom, praticamente è necessaria
 * una classe per ogni nuovo metodo di validazione
 */

use yii\validators\Validator;
use lispa\amos\sondaggi\AmosSondaggi;

class Example extends Validator {

    public function validateAttribute($model, $attribute) {
        $errore = FALSE;
        if ($errore) {
            $this->addError($model, $attribute, AmosSondaggi::t('amossondaggi', 'La descrizione dell\'errore.'));
        }
    }

}

?>