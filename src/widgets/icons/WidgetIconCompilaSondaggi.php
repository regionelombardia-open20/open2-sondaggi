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
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconCompilaSondaggi
 * @package lispa\amos\sondaggi\widgets\icons
 */
class WidgetIconCompilaSondaggi extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $options = parent::getOptions();

        //aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
        return ArrayHelper::merge($options, ["children" => []]);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosSondaggi::tHtml('amossondaggi', 'Compila sondaggi'));
        $this->setDescription(AmosSondaggi::t('amossondaggi', 'Permette di compilare e completare i sondaggi'));

        $this->setIcon('quote-right');

        $this->setUrl(Yii::$app->urlManager->createUrl(['/sondaggi/pubblicazione/index']));
        $this->setCode('COMP_SONDAGGI');
        $this->setModuleName('sondaggi');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }
}
