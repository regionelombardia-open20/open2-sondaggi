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
 * Class SondaggiStato
 * This is the model class for table "sondaggi_stato".
 * @package lispa\amos\sondaggi\models
 */
class SondaggiStato extends \lispa\amos\sondaggi\models\base\SondaggiStato
{
    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'descrizione'
        ];
    }
}
