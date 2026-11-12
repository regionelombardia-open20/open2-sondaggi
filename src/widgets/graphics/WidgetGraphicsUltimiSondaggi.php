<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\widgets\graphics
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

namespace open20\amos\sondaggi\widgets\graphics;

use open20\amos\core\widget\WidgetGraphic;
use open20\amos\notificationmanager\base\NotifyWidgetDoNothing;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\models\search\SondaggiSearch;

/**
 * Class WidgetGraphicsUltimiSondaggi
 * @package open20\amos\sondaggi\widgets\graphics
 */
class WidgetGraphicsUltimiSondaggi extends WidgetGraphic
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->setCode('ULTIMI_SONDAGGI_GRAPHIC');
        $this->setLabel(AmosSondaggi::t('amossondaggi', 'Ultimi sondaggi'));
        $this->setDescription(AmosSondaggi::t('amossondaggi', '#widget_graphic_cms_last_surveys_description'));
    }
    
    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        $search = new SondaggiSearch();
        $search->setNotifier(new NotifyWidgetDoNothing());
        
        $listaSondaggi = $search->ultimiSondaggi($_GET, 3);
        
        if (isset(\Yii::$app->params['showWidgetEmptyContent']) && \Yii::$app->params['showWidgetEmptyContent'] == false) {
            if ($listaSondaggi->getTotalCount() == 0) {
                return false;
            }
        }
        
        return $this->render('ultimi_sondaggi_cms', [
            'lista' => $listaSondaggi,
            'widget' => $this,
            'toRefreshSectionId' => 'widgetGraphicSondaggi'
        ]);
    }
}
