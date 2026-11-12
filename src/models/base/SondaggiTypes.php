<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\models\base
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

namespace open20\amos\sondaggi\models\base;

use open20\amos\sondaggi\AmosSondaggi;

class SondaggiTypes extends \yii\base\BaseObject
{
    private $sondaggi_types = [];


    const SONDAGGI_TYPE_STANDARD = '1';
    const SONDAGGI_TYPE_LIVE = '2';

    public function init()
    {
        parent::init();
        $this->sondaggi_types = self::getLabels();

    }


    public function getTypes()
    {
        return $this->sondaggi_types;
    }

    /**
     *
     * @return string
     */
    public static function getStandardType()
    {
        return self::SONDAGGI_TYPE_STANDARD;
    }

    /**
     *
     * @return string
     */
    public static function getLiveType()
    {
        return self::SONDAGGI_TYPE_LIVE;
    }

    /**
     * @return array
     */
    public static function getLabels()
    {
        return [
            self::SONDAGGI_TYPE_STANDARD => AmosSondaggi::t('amossondaggi', 'Completo'),
            self::SONDAGGI_TYPE_LIVE => AmosSondaggi::t('amossondaggi', 'Live')
        ];
    }

    /**
     * @param $type
     * @return string
     */
    public static function getDescription($type)
    {
        if ($type == self::SONDAGGI_TYPE_STANDARD) {
            return AmosSondaggi::t('amossondaggi', 'sondaggio completo con tutte le tipologie di risposta');
        }
        if ($type == self::SONDAGGI_TYPE_LIVE) {
            return AmosSondaggi::t('amossondaggi', 'sondaggio live che prevede una sola domanda con risposte a insieme chiuso');
        }
        return '';
    }
}
