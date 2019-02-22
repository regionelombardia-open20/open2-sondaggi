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
 * Class SondaggiDomandeTipologie
 * This is the model class for table "sondaggi_domande_tipologie".
 * @package lispa\amos\sondaggi\models
 */
class SondaggiDomandeTipologie extends \lispa\amos\sondaggi\models\base\SondaggiDomandeTipologie
{
    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'tipologia',
        ];
    }
}
