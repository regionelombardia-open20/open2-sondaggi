<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-sondaggi
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\sondaggi\widgets\icons;

use open20\amos\core\widget\WidgetIcon;
use open20\amos\sondaggi\AmosSondaggi;
use Yii;
use yii\helpers\ArrayHelper;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\widget\WidgetAbstract;

/**
 * Class WidgetIconAmministraSondaggi
 * @deprecated since version 1.3.4 for wrong namespace
 * @package open20\amos\sondaggi\widgets
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

        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('sondaggi');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('quote-right');
        }

        $this->setUrl(Yii::$app->urlManager->createUrl(['sondaggi']));
        $this->setCode('AMM_SONDAGGI');
        $this->setModuleName('sondaggi');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                [
                    'bk-backgroundIcon',
                    'color-primary'
                ]
            )
        );
    }

    /**
     * Aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
     * 
     * @inheritdoc
     */
    public function getOptions()
    {
        return ArrayHelper::merge(
            parent::getOptions(),
            ['children' => $this->getWidgetsIcon()]
        );
    }

    /**
     * TEMPORANEA
     * @return type
     */
    public function getWidgetsIcon()
    {
        $widgets = [];

        $WidgetIconSondaggi = new WidgetIconSondaggi();
        if ($WidgetIconSondaggi->isVisible()) {
            $widgets[] = $WidgetIconSondaggi->getOptions();
        }

        $WidgetIconCompilaSondaggi = new WidgetIconCompilaSondaggi();
        if ($WidgetIconCompilaSondaggi->isVisible()) {
            $widgets[] = $WidgetIconCompilaSondaggi->getOptions();
        }

        $WidgetIconPubblicaSondaggi = new WidgetIconPubblicaSondaggi();
        if ($WidgetIconPubblicaSondaggi->isVisible()) {
            $widgets[] = $WidgetIconPubblicaSondaggi->getOptions();
        }

        return $widgets;
    }

}
