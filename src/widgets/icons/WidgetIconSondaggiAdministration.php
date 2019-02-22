<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\widgets\icons
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\sondaggi\AmosSondaggi;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconSondaggiAdministration
 * @package lispa\amos\sondaggi\widgets\icons
 */
class WidgetIconSondaggiAdministration extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosSondaggi::tHtml('amossondaggi', 'Amministra i Sondaggi'));
        $this->setDescription(AmosSondaggi::t('amossondaggi', 'Modulo di amministrazione dei Sondaggi'));
        $this->setIcon('quote-right');
        $this->setUrl(['sondaggi']);
        $this->setCode('AMM_SONDAGGI');
        $this->setModuleName('sondaggi');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }
}
