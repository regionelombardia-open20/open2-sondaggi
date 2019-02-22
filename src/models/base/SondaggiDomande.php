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
 * This is the base-model class for table "sondaggi_domande".
 *
 * @property integer $id    
 * @property integer $domanda_condizionata
 * @property string $domanda
 * @property integer $obbligatoria
 * @property integer $inline
 * @property integer $sondaggi_id
 * @property integer $ordinamento
 * @property integer $min_int_multipla
 * @property integer $max_int_multipla
 * @property string $nome_classe_validazione
 * @property integer $sondaggi_domande_pagine_id
 * @property integer $sondaggi_domande_tipologie_id 
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $version
 *
 * @property \lispa\amos\sondaggi\models\Sondaggi $sondaggi
 * @property \lispa\amos\sondaggi\models\SondaggiDomandePagine $sondaggiDomandePagine
 * @property \lispa\amos\sondaggi\models\SondaggiDomandeTipologie $sondaggiDomandeTipologie 
 * @property \lispa\amos\sondaggi\models\SondaggiDomandeCondizionate[] $sondaggiDomandeCondizionates
 * @property \lispa\amos\sondaggi\models\SondaggiRispostePredefinite[] $sondaggiRispostePredefinitesCondizionate
 * @property \lispa\amos\sondaggi\models\SondaggiRisposte[] $sondaggiRispostes
 * @property \lispa\amos\sondaggi\models\SondaggiRispostePredefinite[] $sondaggiRispostePredefinites
 */
class SondaggiDomande extends \lispa\amos\core\record\Record {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'sondaggi_domande';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['domanda_condizionata', 'obbligatoria', 'inline', 'sondaggi_id', 'ordinamento', 'min_int_multipla', 'max_int_multipla', 'sondaggi_domande_pagine_id', 'sondaggi_domande_tipologie_id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['domanda'], 'string'],
            [['nome_classe_validazione'], 'string', 'max' => 255],
            [['domanda', 'sondaggi_id', 'sondaggi_domande_pagine_id', 'sondaggi_domande_tipologie_id'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => AmosSondaggi::t('amossondaggi', 'ID'),
            'domanda_condizionata' => AmosSondaggi::t('amossondaggi', 'Domanda condizionata'),
            'domanda' => AmosSondaggi::t('amossondaggi', 'Domanda'),
            'obbligatoria' => AmosSondaggi::t('amossondaggi', 'Obbligatoria'),
            'inline' => AmosSondaggi::t('amossondaggi', 'Visualizzazione risposte'),
            'sondaggi_id' => AmosSondaggi::t('amossondaggi', 'Sondaggio'),
            'ordinamento' => AmosSondaggi::t('amossondaggi', 'Ordinamento'),
            'min_int_multipla' => AmosSondaggi::t('amossondaggi', 'Selezioni minime'),
            'max_int_multipla' => AmosSondaggi::t('amossondaggi', 'Selezioni massime'),
            'nome_classe_validazione' => AmosSondaggi::t('amossondaggi', 'Nome della classe Validatrice'),
            'sondaggi_domande_pagine_id' => AmosSondaggi::t('amossondaggi', 'Pagina'),
            'sondaggi_domande_tipologie_id' => AmosSondaggi::t('amossondaggi', 'Tipo risposta'),
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
    public function getSondaggi() {
        return $this->hasOne(\lispa\amos\sondaggi\models\Sondaggi::className(), ['id' => 'sondaggi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiDomandePagine() {
        return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiDomandePagine::className(), ['id' => 'sondaggi_domande_pagine_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiDomandeTipologie() {
        return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiDomandeTipologie::className(), ['id' => 'sondaggi_domande_tipologie_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiDomandeCondizionates() {
        return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiDomandeCondizionate::className(), ['sondaggi_domande_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiRispostePredefinitesCondizionate() {        
        return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiRispostePredefinite::className(), ['id' => 'sondaggi_risposte_predefinite_id'])->viaTable('sondaggi_domande_condizionate', ['sondaggi_domande_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiRispostes() {
        return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiRisposte::className(), ['sondaggi_domande_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSondaggiRispostePredefinites() {
        return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiRispostePredefinite::className(), ['sondaggi_domande_id' => 'id']);
    }

}
