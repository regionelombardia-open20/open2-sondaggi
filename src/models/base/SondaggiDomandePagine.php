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

use Yii;
use lispa\amos\sondaggi\AmosSondaggi;

/**
* This is the base-model class for table "sondaggi_domande_pagine".
*
    * @property integer $id
    * @property integer $sondaggi_id
    * @property string $titolo
    * @property string $descrizione
    * @property integer $ordinamento 
    * @property integer $filemanager_mediafile_id
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property integer $version
    *
            * @property \lispa\amos\sondaggi\models\SondaggiDomande[] $sondaggiDomandes
            * @property \lispa\amos\sondaggi\models\FilemanagerMediafile $filemanagerMediafile
            * @property \lispa\amos\sondaggi\models\Sondaggi $sondaggi
    */
class SondaggiDomandePagine extends \lispa\amos\core\record\Record
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'sondaggi_domande_pagine';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['sondaggi_id'], 'required'],
            [['sondaggi_id', 'ordinamento', 'filemanager_mediafile_id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['descrizione'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['titolo'], 'string', 'max' => 255]
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => AmosSondaggi::t('amossondaggi', 'ID'),
    'sondaggi_id' => AmosSondaggi::t('amossondaggi', 'Sondaggio'),
    'titolo' => AmosSondaggi::t('amossondaggi', 'Titolo'),
    'descrizione' => AmosSondaggi::t('amossondaggi', 'Descrizione'),
    'ordinamento' => AmosSondaggi::t('amossondaggi', 'Ordinamento'),
    'filemanager_mediafile_id' => AmosSondaggi::t('amossondaggi', 'Immagine'),
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
    public function getSondaggiDomandes()
    {
    return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiDomande::className(), ['sondaggi_domande_pagine_id' => 'id']);
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
    public function getSondaggi()
    {
    return $this->hasOne(\lispa\amos\sondaggi\models\Sondaggi::className(), ['id' => 'sondaggi_id']);
    }
}
