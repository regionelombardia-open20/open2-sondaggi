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
 * Class SondaggiDomandeCondizionate
 * This is the model class for table "sondaggi_domande_condizionate".
 * @package lispa\amos\sondaggi\models
 */
class SondaggiDomandeCondizionate extends \lispa\amos\sondaggi\models\base\SondaggiDomandeCondizionate
{
    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'sondaggi_risposte_predefinite_id',
            'sondaggi_domande_id'
        ];
    }
}
