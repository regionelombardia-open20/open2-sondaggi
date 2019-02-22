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
 * Class SondaggiRisposte
 * This is the model class for table "sondaggi_risposte".
 * @package lispa\amos\sondaggi\models
 */
class SondaggiRisposte extends \lispa\amos\sondaggi\models\base\SondaggiRisposte
{
    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'risposta_libera'
        ];
    }
}
