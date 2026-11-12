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

use open20\amos\core\record\ContentModel;
use open20\amos\notificationmanager\record\NotifyRecord;
use open20\amos\sondaggi\AmosSondaggi;

/**
 * Class Sondaggi
 *
 * This is the base-model class for table "sondaggi".
 *
 * @property integer $id
 * @property string $status
 * @property integer $sondaggio_chiuso_frontend
 * @property string $thank_you_page_sondaggio_chiuso
 * @property string $titolo
 * @property string $descrizione
 * @property integer $send_pdf_via_email
 * @property string $mail_subject_send_pdf_guest
 * @property string $mail_send_pdf_guest
 * @property integer $additional_emails
 * @property integer $compilazioni_disponibili
 * @property integer $sondaggi_stato_id
 * @property integer $filemanager_mediafile_id
 * @property integer $sondaggi_temi_id
 * @property integer $sondaggio_type
 * @property integer $community_id
 * @property integer $sondaggio_live_community_id
 * @property string $begin_date_hour_live
 * @property string $end_date_hour_live
 * @property string $graphics_live
 * @property string $how_show_live
 * @property string $publish_date
 * @property string $close_date
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $version
 *
 * @property \open20\amos\upload\models\FilemanagerMediafile $filemanagerMediafile
 * @property \open20\amos\sondaggi\models\SondaggiStato $sondaggiStato
 * @property \open20\amos\sondaggi\models\SondaggiTemi $sondaggiTemi
 * @property \open20\amos\sondaggi\models\SondaggiDomande[] $sondaggiDomandes
 * @property \open20\amos\sondaggi\models\SondaggiDomandePagine[] $sondaggiDomandePagines
 * @property \open20\amos\sondaggi\models\SondaggiRisposteSessioni[] $sondaggiRisposteSessionis
 * @property \open20\amos\sondaggi\models\SondaggiPubblicazione[] $sondaggiPubblicaziones
 *
 * @package open20\amos\sondaggi\models\base
 */
abstract class Sondaggi extends ContentModel {

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'sondaggi';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['titolo', 'sondaggio_type', 'descrizione', 'publish_date', 'close_date'], 'required'],
            [['descrizione', 'additional_emails', 'thank_you_page', 'url_sondaggio_non_compilabile',
            'link_landing_page', 'testo_sondaggio_non_compilabile_front', 'titolo_fine_sondaggio_front',
            'testo_fine_sondaggio_front', 'mail_mittente_nuovo_utente', 'mail_soggetto_nuovo_utente',
            'mail_contenuto_nuovo_utente', 'mail_mittente_utente_presente', 'mail_soggetto_utente_presente',
            'mail_contenuto_utente_presente', 'mail_registrazione_mittente', 'mail_registrazione_soggetto',
            'mail_registrazione_corpo', 'mail_conf_community_mittente', 'mail_conf_community_soggetto',
            'mail_conf_community_corpo', 'forza_lingua', 'thank_you_page_sondaggio_chiuso', 'mail_subject_send_pdf_guest'], 'string'],
            ['mail_send_pdf_guest', \open20\amos\core\validators\StringHtmlValidator::className()],
            [['url_chiudi_sondaggio'], 'url', 'defaultScheme' => 'http'],
            [['status', 'titolo', 'sottotitolo'], 'string', 'max' => 255],
            [['publish_date', 'close_date'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['publish_date', 'compare', 'compareValue' => date('Y-m-d H:i:00'), 'operator' => '>=', 'on' => self::SCENARIO_CREATE],
            ['close_date', 'compare', 'compareAttribute' => 'publish_date', 'operator' => '>'],
            [['compilazioni_disponibili', 'send_pdf_via_email', 'send_pdf_via_email_closed', 'send_pdf_to_compiler', 'sondaggi_stato_id', 'filemanager_mediafile_id', 'sondaggi_temi_id', 'created_by',
            'updated_by', 'deleted_by', 'version', 'send_pdf_via_email', 'frontend', 'abilita_registrazione', 'mail_registrazione_custom', 'mail_conf_community', 'mail_conf_community_id', 'abilita_criteri_valutazione', 'n_max_valutatori', 'sondaggio_chiuso_frontend', 'anonimous_poll', 'visualizza_solo_titolo', 'sondaggio_type', 'sondaggio_live_community_id', 'use_get_language', 'field_extra', 'community_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at', 'begin_date_hour_live', 'end_date_hour_live', 'graphics_live', 'how_show_live', 'customTags', 'customTagsDefault'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => AmosSondaggi::t('amossondaggi', 'ID'),
            'status' => AmosSondaggi::t('amossondaggi', 'Status'),
            'sondaggio_chiuso_frontend' => AmosSondaggi::t('amossondaggi', 'Sondaggio chiuso'),
            'thank_you_page_sondaggio_chiuso' => AmosSondaggi::t('amossondaggi', 'ThankYouPage del sondaggio chiuso'),
            'titolo' => AmosSondaggi::t('amossondaggi', 'Titolo'),
            'sottotitolo' => AmosSondaggi::t('amossondaggi', 'Sottotitolo'),
            'descrizione' => AmosSondaggi::t('amossondaggi', 'Descrizione'),
            'forza_lingua' => AmosSondaggi::t('amossondaggi', 'Forza la lingua'),
            'field_extra' => AmosSondaggi::t('amossondaggi', 'Abilita info aggiuntive da memorizzare (aggiungere in get field_extra come parametro)'),
            'use_get_language' => AmosSondaggi::t('amossondaggi', 'Forza la lingua da parametro in get per il frontend (il parametro è language, un esempio: URL?language=it-IT)'),
            'thank_you_page' => AmosSondaggi::t('amossondaggi', 'Url della "Thank you page"'),
            'url_chiudi_sondaggio' => AmosSondaggi::t('amossondaggi', 'Url a cui redirigere al click su "Chiudi"'),
            'visualizza_solo_titolo' => AmosSondaggi::t('amossondaggi', 'Nella compilazione visualizzare solo il titolo'),
            'anonimous_poll' => AmosSondaggi::t('amossondaggi', 'Crea un sondaggio anonimo'),
            'url_sondaggio_non_compilabile' => AmosSondaggi::t('amossondaggi',
                    'Url della pagina di avviso di "Sondaggio non compilabile"'),
            'abilita_registrazione' => AmosSondaggi::t('amossondaggi',
                    'Abilita registrazione automatica al termine del sondaggio'),
            'compilazioni_disponibili' => AmosSondaggi::t('amossondaggi', 'Compilazioni disponibili'),
            'additional_emails' => AmosSondaggi::t('amossondaggi', 'Altre email'),
            'send_pdf_via_email' => AmosSondaggi::t('amossondaggi',
                    '#on_poll_compiled_send_email'),
            'send_pdf_to_compiler' => AmosSondaggi::t('amossondaggi',
                    '#on_poll_compiled_send_email_compiler') . ' ' . AmosSondaggi::t('amossondaggi', 'con la seguente mail:'),
            'send_pdf_via_email_closed' => AmosSondaggi::t('amossondaggi',
                    '#on_poll_closed_send_email'),
            'publish_date' => AmosSondaggi::t('amossondaggi', 'Data di pubblicazione sondaggio'),
            'close_date' => AmosSondaggi::t('amossondaggi', 'Data di chiusura sondaggio'),
            'sondaggi_stato_id' => AmosSondaggi::t('amossondaggi', 'Stato'),
            'filemanager_mediafile_id' => AmosSondaggi::t('amossondaggi', 'Immagine'),
            'sondaggi_temi_id' => AmosSondaggi::t('amossondaggi', 'Tema'),
            'frontend' => AmosSondaggi::t('amossondaggi', 'Sondaggio compilabile da utente Guest'),
            'link_landing_page' => AmosSondaggi::t('amossondaggi', 'Url della Landing Page'),
            'testo_sondaggio_non_compilabile_front' => AmosSondaggi::t('amossondaggi', 'Testo in caso di sondaggio non compilabile'),
            'titolo_fine_sondaggio_front' => AmosSondaggi::t('amossondaggi', 'H1 della "thank you page"'),
            'testo_fine_sondaggio_front' => AmosSondaggi::t('amossondaggi', 'Messaggio della "thank you page"'),
            'mail_mittente_nuovo_utente' => AmosSondaggi::t('amossondaggi', 'Mittente (il nome visualizzato sarà il testo dopo il primo spazio)'),
            'mail_soggetto_nuovo_utente' => AmosSondaggi::t('amossondaggi', 'Soggetto'),
            'mail_contenuto_nuovo_utente' => AmosSondaggi::t('amossondaggi', 'Testo'),
            'mail_mittente_utente_presente' => AmosSondaggi::t('amossondaggi', 'Mittente'),
            'mail_soggetto_utente_presente' => AmosSondaggi::t('amossondaggi', 'Soggetto'),
            'mail_contenuto_utente_presente' => AmosSondaggi::t('amossondaggi', 'Testo'),
            'mail_registrazione_custom' => AmosSondaggi::t('amossondaggi', 'Abilita invio e-mail personalizzata per le nuove registrazioni'),
            'mail_registrazione_mittente' => AmosSondaggi::t('amossondaggi', 'Mittente'),
            'mail_registrazione_soggetto' => AmosSondaggi::t('amossondaggi', 'Soggetto'),
            'mail_registrazione_corpo' => AmosSondaggi::t('amossondaggi', 'Testo'),
            'mail_conf_community' => AmosSondaggi::t('amossondaggi', 'Abilita invio mail conferma registrazione alla community (al primo accesso in piattaforma)'),
            'mail_conf_community_id' => AmosSondaggi::t('amossondaggi', 'Id della Community'),
            'mail_conf_community_mittente' => AmosSondaggi::t('amossondaggi', 'Mittente'),
            'mail_conf_community_soggetto' => AmosSondaggi::t('amossondaggi', 'Soggetto'),
            'mail_conf_community_corpo' => AmosSondaggi::t('amossondaggi', 'Testo'),
            'community_id' => AmosSondaggi::t('amossondaggi', 'Community'),
            'created_at' => AmosSondaggi::t('amossondaggi', 'Creato il'),
            'updated_at' => AmosSondaggi::t('amossondaggi', 'Aggiornato il'),
            'deleted_at' => AmosSondaggi::t('amossondaggi', 'Cancellato il'),
            'created_by' => AmosSondaggi::t('amossondaggi', 'Creato da'),
            'updated_by' => AmosSondaggi::t('amossondaggi', 'Aggiornato da'),
            'deleted_by' => AmosSondaggi::t('amossondaggi', 'Cancellato da'),
            'version' => AmosSondaggi::t('amossondaggi', 'Versione'),
            'abilita_criteri_valutazione' => AmosSondaggi::t('amossondaggi', 'Abilita criteri di valutazione'),
            'n_max_valutatori' => AmosSondaggi::t('amossondaggi', 'N. max di valutatori (0 = senza limiti)'),
            'begin_date_hour_live' => AmosSondaggi::t('amossondaggi', 'Data inizio live'),
            'end_date_hour_live' => AmosSondaggi::t('amossondaggi', 'Data fine live'),
            'mail_subject_send_pdf_guest' => AmosSondaggi::t('amossondaggi', 'Soggetto'),
            'mail_send_pdf_guest' => AmosSondaggi::t('amossondaggi', 'Messaggio della mail (variabili disponibili: {{{nome}}} - {{{cognome}}})'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilemanagerMediafile() {
        return $this->hasOne(\open20\amos\upload\models\FilemanagerMediafile::className(),
                        ['id' => 'filemanager_mediafile_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiStato() {
        return $this->hasOne(\open20\amos\sondaggi\models\SondaggiStato::className(), ['id' => 'sondaggi_stato_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiTemi() {
        return null;
//        return $this->hasOne(\open20\amos\sondaggi\models\SondaggiTemi::className(), ['id' => 'sondaggi_temi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiDomandes() {
        return $this->hasMany(\open20\amos\sondaggi\models\SondaggiDomande::className(), ['sondaggi_id' => 'id'])->andWhere([
                    \open20\amos\sondaggi\models\SondaggiDomande::tableName() . '.deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiDomandesWithFiles() {
        return $this->hasMany(\open20\amos\sondaggi\models\SondaggiDomande::className(), ['sondaggi_id' => 'id'])->andWhere([
                    'OR', ['sondaggi_domande_tipologie_id' => 10], ['sondaggi_domande_tipologie_id' => 11]]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiDomandePagines() {
        return $this->hasMany(\open20\amos\sondaggi\models\SondaggiDomandePagine::className(),
                                ['sondaggi_id' => 'id'])
                        ->andWhere([\open20\amos\sondaggi\models\SondaggiDomandePagine::tableName() . '.deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiRisposteSessionis() {
        return $this->hasMany(\open20\amos\sondaggi\models\SondaggiRisposteSessioni::className(),
                        ['sondaggi_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiPubblicaziones() {
        return $this->hasMany(\open20\amos\sondaggi\models\SondaggiPubblicazione::className(),
                        ['sondaggi_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeiAttivitaFormatives() {
        return $this->hasMany(\frontend\modules\attivitaformative\models\PeiAttivitaFormative::className(),
                        ['id' => 'entita_id'])->via('sondaggiPubblicaziones');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiContext() {
        return $this->hasOne(\open20\amos\sondaggi\models\SondaggiContext::className(), ['sondaggi_id' => 'id']);
    }
}
