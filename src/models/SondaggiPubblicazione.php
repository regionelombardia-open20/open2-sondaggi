<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\models
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

namespace open20\amos\sondaggi\models;

/**
 * Class SondaggiPubblicazione
 * This is the model class for table "sondaggi_pubblicazione".
 * @package open20\amos\sondaggi\models
 */
class SondaggiPubblicazione extends \open20\amos\sondaggi\models\base\SondaggiPubblicazione
{
    public $byBassRuleCwh = true;

    public $attivita;

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'ruolo'
        ];
    }

    /**
     * @param array $values
     */
    public function setOtherAttribute($values)
    {
        if (isset($values['Sondaggi']['mail_subject'])) {
            $this->mail_subject = $values['Sondaggi']['mail_subject'];
        }
        if (isset($values['Sondaggi']['mail_message'])) {
            $this->mail_message = $values['Sondaggi']['mail_message'];
        }
        if (isset($values['Sondaggi']['text_end'])) {
            $this->text_end = $values['Sondaggi']['text_end'];
        }
        if (isset($values['Sondaggi']['text_end_html'])) {
            $this->text_end_html = $values['Sondaggi']['text_end_html'];
        }
        if (isset($values['Sondaggi']['text_end_title'])) {
            $this->text_end_title = $values['Sondaggi']['text_end_title'];
        }
        if (isset($values['Sondaggi']['text_not_compilable'])) {
            $this->text_not_compilable = $values['Sondaggi']['text_not_compilable'];
        }
        if (isset($values['Sondaggi']['text_not_compilable_html'])) {
            $this->text_not_compilable_html = $values['Sondaggi']['text_not_compilable_html'];
        }
    }
}
