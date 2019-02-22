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
use lispa\amos\sondaggi\models\search\SondaggiSearch;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconSondaggi
 * @package lispa\amos\sondaggi\widgets\icons
 */
class WidgetIconSondaggi extends WidgetIcon
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

        $this->setLabel(AmosSondaggi::tHtml('amossondaggi', 'Gestione sondaggi'));
        $this->setDescription(AmosSondaggi::t('amossondaggi', 'Gestisce i sondaggi'));

        $this->setIcon('quote-right');

        $this->setUrl(Yii::$app->urlManager->createUrl(['/sondaggi/sondaggi']));
        $this->setCode('SONDAGGI');
        $this->setModuleName('sondaggi');
        $this->setNamespace(__CLASS__);
        $search = new SondaggiSearch();
        $this->setBulletCount($search->searchSondaggiNonPartecipato(null)->count());
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }
}
