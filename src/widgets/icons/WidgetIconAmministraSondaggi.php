<?php

namespace lispa\amos\sondaggi\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\sondaggi\AmosSondaggi;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconAmministraSondaggi
 * @deprecated since version 1.3.4 for wrong namespace
 * @package lispa\amos\sondaggi\widgets
 */
class WidgetIconAmministraSondaggi extends WidgetIcon
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
        $this->setUrl(Yii::$app->urlManager->createUrl(['sondaggi']));
        $this->setCode('AMM_SONDAGGI');
        $this->setModuleName('sondaggi');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $options = parent::getOptions();

        //aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
        return ArrayHelper::merge($options, ["children" => $this->getWidgetsIcon()]);
    }

    /* TEMPORANEA */

    public function getWidgetsIcon()
    {
        $widgets = [];

        $WidgetIconSondaggi = new icons\WidgetIconSondaggi();
        if ($WidgetIconSondaggi->isVisible()) {
            $widgets[] = $WidgetIconSondaggi->getOptions();
        }

        $WidgetIconCompilaSondaggi = new icons\WidgetIconCompilaSondaggi();
        if ($WidgetIconCompilaSondaggi->isVisible()) {
            $widgets[] = $WidgetIconCompilaSondaggi->getOptions();
        }

        $WidgetIconPubblicaSondaggi = new icons\WidgetIconPubblicaSondaggi();
        if ($WidgetIconPubblicaSondaggi->isVisible()) {
            $widgets[] = $WidgetIconPubblicaSondaggi->getOptions();
        }

        return $widgets;
    }
}
