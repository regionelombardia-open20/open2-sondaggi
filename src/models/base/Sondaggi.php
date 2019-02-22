<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\models\base
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi\models\base;

use lispa\amos\sondaggi\AmosSondaggi;

/**
 * Class Sondaggi
 *
 * This is the base-model class for table "sondaggi".
 *
 * @property integer $id
 * @property string $titolo
 * @property string $descrizione
 * @property integer $compilazioni_disponibili
 * @property integer $sondaggi_stato_id
 * @property integer $filemanager_mediafile_id
 * @property integer $sondaggi_temi_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $version
 *
 * @property \lispa\amos\upload\models\FilemanagerMediafile $filemanagerMediafile
 * @property \lispa\amos\sondaggi\models\SondaggiStato $sondaggiStato
 * @property \lispa\amos\sondaggi\models\SondaggiTemi $sondaggiTemi
 * @property \lispa\amos\sondaggi\models\SondaggiDomande[] $sondaggiDomandes
 * @property \lispa\amos\sondaggi\models\SondaggiDomandePagine[] $sondaggiDomandePagines
 * @property \lispa\amos\sondaggi\models\SondaggiRisposteSessioni[] $sondaggiRisposteSessionis
 * @property \lispa\amos\sondaggi\models\SondaggiPubblicazione[] $sondaggiPubblicaziones
 *
 * @package lispa\amos\sondaggi\models\base
 */
class Sondaggi extends \lispa\amos\core\record\Record
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sondaggi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['titolo'], 'required'],
            [['titolo', 'descrizione'], 'string'],
            [['compilazioni_disponibili', 'sondaggi_stato_id', 'filemanager_mediafile_id', 'sondaggi_temi_id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosSondaggi::t('amossondaggi', 'ID'),
            'titolo' => AmosSondaggi::t('amossondaggi', 'Titolo'),
            'descrizione' => AmosSondaggi::t('amossondaggi', 'Descrizione'),
            'sondaggi_stato_id' => AmosSondaggi::t('amossondaggi', 'Stato'),
            'filemanager_mediafile_id' => AmosSondaggi::t('amossondaggi', 'Immagine'),
            'sondaggi_temi_id' => AmosSondaggi::t('amossondaggi', 'Tema'),
            'created_at' => AmosSondaggi::t('amossondaggi', 'Creato il'),
            'updated_at' => AmosSondaggi::t('amossondaggi', 'Aggiornato il'),
            'deleted_at' => AmosSondaggi::t('amossondaggi', 'Cancellato il'),
            'created_by' => AmosSondaggi::t('amossondaggi', 'Creato da'),
            'updated_by' => AmosSondaggi::t('amossondaggi', 'Aggiornato da'),
            'deleted_by' => AmosSondaggi::t('amossondaggi', 'Cancellato da'),
            'version' => AmosSondaggi::t('amossondaggi', 'Versione'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilemanagerMediafile()
    {
        return $this->hasOne(\lispa\amos\upload\models\FilemanagerMediafile::className(), ['id' => 'filemanager_mediafile_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiStato()
    {
        return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiStato::className(), ['id' => 'sondaggi_stato_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiTemi()
    {
        return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiTemi::className(), ['id' => 'sondaggi_temi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiDomandes()
    {
        return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiDomande::className(), ['sondaggi_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiDomandePagines()
    {
        return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiDomandePagine::className(), ['sondaggi_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiRisposteSessionis()
    {
        return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiRisposteSessioni::className(), ['sondaggi_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiPubblicaziones()
    {
        return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiPubblicazione::className(), ['sondaggi_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeiAttivitaFormatives()
    {
        return $this->hasMany(\backend\modules\attivitaformative\models\PeiAttivitaFormative::className(), ['id' => 'entita_id'])->via('sondaggiPubblicaziones');
    }

}
