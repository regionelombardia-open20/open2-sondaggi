<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\i18n\grammar
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

namespace open20\amos\sondaggi\i18n\grammar;

use open20\amos\core\interfaces\ModelGrammarInterface;
use open20\amos\sondaggi\AmosSondaggi;

/**
 * Class SondaggiGrammar
 * @package open20\amos\sondaggi\i18n\grammar
 */
class SondaggiGrammar implements ModelGrammarInterface
{
    /**
     * @inheritdoc
     */
    public function getModelSingularLabel()
    {
        return AmosSondaggi::t('amossondaggi', '#sondaggi_singular');
    }

    /**
     * @inheritdoc
     */
    public function getModelLabel()
    {
        return AmosSondaggi::t('amossondaggi', '#sondaggi_plural');
    }

    /**
     * @inheritdoc
     */
    public function getArticleSingular()
    {
        return AmosSondaggi::t('amossondaggi', '#sondaggi_article_singular');
    }

    /**
     * @inheritdoc
     */
    public function getArticlePlural()
    {
        return AmosSondaggi::t('amossondaggi', '#sondaggi_article_plural');
    }

    /**
     * @inheritdoc
     */
    public function getIndefiniteArticle()
    {
        return AmosSondaggi::t('amossondaggi', '#sondaggi_indefinite_article');
    }
}
