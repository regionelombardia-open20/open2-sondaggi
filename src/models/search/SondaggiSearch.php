<?php

namespace lispa\amos\sondaggi\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use lispa\amos\sondaggi\models\Sondaggi;

/**
 * SondaggiSearch represents the model behind the search form about `lispa\amos\sondaggi\models\Sondaggi`.
 */
class SondaggiSearch extends Sondaggi {

    public function rules() {
        return [
            [['id', 'filemanager_mediafile_id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['titolo', 'descrizione', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    public function scenarios() {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Sondaggi::find();
        $query->orderBy('created_at DESC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'filemanager_mediafile_id' => $this->filemanager_mediafile_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'titolo', $this->titolo])
                ->andFilterWhere(['like', 'descrizione', $this->descrizione]);

        return $dataProvider;
    }

    public function searchDominio($params) {
        $query = Sondaggi::find();
        
//        $configurazioneAccessi = \backend\modules\peipoint\models\PeiAccessiServiziFacilitazioneConfigurazioneSondaggi::find();
//        $sondaggiAccessi = [];
//        if($configurazioneAccessi->count()){
//            foreach ($configurazioneAccessi->all() as $ConfAccesso){
//                $sondaggiAccessi[] = $ConfAccesso['sondaggi_id'];
//            }
//        }        
//        $query->andWhere(['NOT IN', 'id', $sondaggiAccessi]);
        $utente = \Yii::$app->getUser();
        $ruoli_utente = \Yii::$app->authManager->getRolesByUser($utente->getId());

        //ruolo pubblico sempre visibile
        $Ruoli = ['PUBBLICO'];
        foreach ($ruoli_utente as $Ruolo) {
            $Ruoli[] = $Ruolo->name;
        }

        $query->orderBy('sondaggi.created_at DESC');
        $query->andWhere(['sondaggi_stato_id' => \lispa\amos\sondaggi\models\SondaggiStato::findOne(['stato' => 'VALIDATO'])->id]);
        $query->innerJoinWith('sondaggiPubblicaziones');
        if (!$utente->can('AMMINISTRAZIONE_SONDAGGI')) {
            $query->andWhere(['IN', 'ruolo', $Ruoli]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'filemanager_mediafile_id' => $this->filemanager_mediafile_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'titolo', $this->titolo])
                ->andFilterWhere(['like', 'descrizione', $this->descrizione]);

        return $dataProvider;
    }

    public function searchPartecipato($params) {
        $query = Sondaggi::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'filemanager_mediafile_id' => $this->filemanager_mediafile_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'titolo', $this->titolo])
                ->andFilterWhere(['like', 'descrizione', $this->descrizione]);

        return $dataProvider;
    }


    /**
     * base query.
     *
     * @return mixed
     */
    private function searchBase(){
        return  Sondaggi::find();
    }


    /**
     * @param $params
     * @return mixed
     */
    public function searchSondaggiNonPartecipato($params){
       $query = $this->searchBase();

       $query->joinWith('sondaggiRisposteSessionis');
       $query->andWhere(['begin_date' => null]);
       return $query;

    }

}
