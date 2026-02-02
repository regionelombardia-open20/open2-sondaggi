<?php

namespace open20\amos\sondaggi\models\search;

use open20\amos\sondaggi\models\SondaggiRisultati;
use yii\data\ActiveDataProvider;

class SondaggiRisultatiSearch extends SondaggiRisultati
{
    public function search($params)
    {
        $query = SondaggiRisultati::find();
        $query->andWhere(['sondaggi_id' => $this->sondaggi_id]);
        $query->orderBy('created_at DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 10;

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        return $dataProvider;
    }
}