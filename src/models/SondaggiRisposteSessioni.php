<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\models
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi\models;

/**
 * Class SondaggiRisposteSessioni
 * This is the model class for table "sondaggi_risposte_sessioni".
 * @package lispa\amos\sondaggi\models
 */
class SondaggiRisposteSessioni extends \lispa\amos\sondaggi\models\base\SondaggiRisposteSessioni
{
    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            //inserire il campo o i campi rappresentativi del modulo
        ];
    }
}
