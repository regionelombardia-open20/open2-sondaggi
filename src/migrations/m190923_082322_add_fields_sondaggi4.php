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

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m190923_082322_add_fields_sondaggi4
 */
class m190923_082322_add_fields_sondaggi4 extends Migration
{
    const TABLE         = '{{%sondaggi}}';
    const TABLE_DOMANDE = '{{%sondaggi_domande}}';
    const TABLE_MAP     = '{{%sondaggi_map}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {       
        $this->addColumn(self::TABLE, 'link_landing_page', $this->text()->defaultValue(null)->after('url_chiudi_sondaggio'));
        $this->addColumn(self::TABLE, 'testo_sondaggio_non_compilabile_front', $this->text()->defaultValue(null)->after('link_landing_page'));
        $this->addColumn(self::TABLE, 'titolo_fine_sondaggio_front', $this->text()->defaultValue(null)->after('testo_sondaggio_non_compilabile_front'));
        $this->addColumn(self::TABLE, 'testo_fine_sondaggio_front', $this->text()->defaultValue(null)->after('titolo_fine_sondaggio_front'));
        $this->addColumn(self::TABLE, 'mail_mittente_nuovo_utente', $this->text()->defaultValue(null)->after('testo_fine_sondaggio_front'));
        $this->addColumn(self::TABLE, 'mail_soggetto_nuovo_utente', $this->text()->defaultValue(null)->after('mail_mittente_nuovo_utente'));
        $this->addColumn(self::TABLE, 'mail_contenuto_nuovo_utente', $this->text()->defaultValue(null)->after('mail_soggetto_nuovo_utente'));
        $this->addColumn(self::TABLE, 'mail_mittente_utente_presente', $this->text()->defaultValue(null)->after('mail_contenuto_nuovo_utente'));
        $this->addColumn(self::TABLE, 'mail_soggetto_utente_presente', $this->text()->defaultValue(null)->after('mail_mittente_utente_presente'));
        $this->addColumn(self::TABLE, 'mail_contenuto_utente_presente', $this->text()->defaultValue(null)->after('mail_soggetto_utente_presente'));
        $this->addColumn(self::TABLE, 'mail_registrazione_custom', $this->integer()->defaultValue(0)->after('mail_contenuto_utente_presente'));
        $this->addColumn(self::TABLE, 'mail_registrazione_mittente', $this->text()->defaultValue(null)->after('mail_registrazione_custom'));
        $this->addColumn(self::TABLE, 'mail_registrazione_soggetto', $this->text()->defaultValue(null)->after('mail_registrazione_mittente'));
        $this->addColumn(self::TABLE, 'mail_registrazione_corpo', $this->text()->defaultValue(null)->after('mail_registrazione_soggetto'));
        $this->addColumn(self::TABLE, 'mail_conf_community', $this->integer()->defaultValue(0)->after('mail_registrazione_corpo'));
        $this->addColumn(self::TABLE, 'mail_conf_community_id', $this->integer()->defaultValue(null)->after('mail_conf_community'));
        $this->addColumn(self::TABLE, 'mail_conf_community_mittente', $this->text()->defaultValue(null)->after('mail_conf_community_id'));
        $this->addColumn(self::TABLE, 'mail_conf_community_soggetto', $this->text()->defaultValue(null)->after('mail_conf_community_mittente'));
        $this->addColumn(self::TABLE, 'mail_conf_community_corpo', $this->text()->defaultValue(null)->after('mail_conf_community_soggetto'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {        
        $this->dropColumn(self::TABLE, 'link_landing_page');
        $this->dropColumn(self::TABLE, 'testo_sondaggio_non_compilabile_front');
        $this->dropColumn(self::TABLE, 'titolo_fine_sondaggio_front');
        $this->dropColumn(self::TABLE, 'testo_fine_sondaggio_front');
        $this->dropColumn(self::TABLE, 'mail_mittente_nuovo_utente');
        $this->dropColumn(self::TABLE, 'mail_soggetto_nuovo_utente');
        $this->dropColumn(self::TABLE, 'mail_contenuto_nuovo_utente');
        $this->dropColumn(self::TABLE, 'mail_mittente_utente_presente');
        $this->dropColumn(self::TABLE, 'mail_soggetto_utente_presente');
        $this->dropColumn(self::TABLE, 'mail_contenuto_utente_presente');
        $this->dropColumn(self::TABLE, 'mail_registrazione_custom');
        $this->dropColumn(self::TABLE, 'mail_registrazione_mittente');
        $this->dropColumn(self::TABLE, 'mail_registrazione_soggetto');
        $this->dropColumn(self::TABLE, 'mail_registrazione_corpo');
        $this->dropColumn(self::TABLE, 'mail_conf_community');
        $this->dropColumn(self::TABLE, 'mail_conf_community_id');
        $this->dropColumn(self::TABLE, 'mail_conf_community_mittente');
        $this->dropColumn(self::TABLE, 'mail_conf_community_soggetto');
        $this->dropColumn(self::TABLE, 'mail_conf_community_corpo');
    }
}