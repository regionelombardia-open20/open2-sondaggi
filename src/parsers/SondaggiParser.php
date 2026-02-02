<?php
namespace open20\amos\sondaggi\parsers;

use open20\amos\admin\models\UserProfile;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiDomande;
use open20\amos\sondaggi\models\SondaggiDomandeCondizionate;
use open20\amos\sondaggi\models\SondaggiRispostePredefinite;
use Yii;
use yii\helpers\StringHelper;

class SondaggiParser {

    /**
     *
     * @param Sondaggi $item
     * @return array
     */
    public static function parseItem($item) {
        //The base class name
        $baseClassName = StringHelper::basename(Sondaggi::className());

        //Read permission name
        $readPremission = strtoupper($baseClassName . '_READ');

        //Edit permission name
        $editPremission = strtoupper($baseClassName . '_UPDATE');

        //Can user view element
        $canView = Yii::$app->user->can($readPremission, ['model' => $item]);

        if ($canView) {
            //Define temp item
            $newItem = [];

            //Need id column
            $newItem['id'] = $item->id;

            //Get the list of description fields
            //$newItem['representingColumn'] = $item->representingColumn();

            //Creator profile
            $owner = UserProfile::findOne(['id' => $item->created_by]);

            //Image
            $image = $item->file;

            //Fill fields from item usable in app
            $newItem['fields'] = [
                'title' => $item->titolo,
                'description' => $item->descrizione,
                'compilazioni_disponibili' => $item->compilazioni_disponibili,
                'data_apertura' => $item->publish_date,
                'data_scadenza' => $item->close_date,
//                'created_at' => $item->created_at,
//                'created_by' => $item->created_by,
//                'owner' => [
//                    'nome' => $owner->nome,
//                    'cognome' => $owner->cognome,
//                    'presentazione_breve' => $owner->presentazione_breve,
//                    'avatarUrl' => $owner->avatarWebUrl,
//                ],
                'sondaggioImageUrl' => $image ? Yii::$app->getUrlManager()->createAbsoluteUrl($image->getWebUrl()) : null,
            ];

            //Remove id as is not needed
            if(isset($newItem['fields']['id'])){
                unset($newItem['fields']['id']);
            }

            //Can edit
            //$newItem['canEdit'] = Yii::$app->user->can($editPremission, ['model' => $item]);

            return $newItem;
        }

        return [];
    }

    /**
     *
     * @param type $item
     * @return type
     */
    public static function parsePageItem($item) {

        $newItem = [];

        //Creator profile
        $owner = UserProfile::findOne(['id' => $item->created_by]);

        //Image
        $image = $item->file;

        //Fill fields from item usable in app
        $newItem = [
            'id' => $item->id,
            'title' => $item->titolo,
            'description' => $item->descrizione,
            //'ordinamento' => $item->ordinamento,
//            'created_at' => $item->created_at,
//            'created_by' => $item->created_by,
//            'owner' => [
//                'nome' => $owner->nome,
//                'cognome' => $owner->cognome,
//                'presentazione_breve' => $owner->presentazione_breve,
//                'avatarUrl' => $owner->avatarWebUrl,
//            ],
            'pageImageUrl' => $image ? Yii::$app->getUrlManager()->createAbsoluteUrl($image->getWebUrl()) : null,
        ];

        return $newItem;
    }

    /**
     *
     * @param SondaggiDomande $item
     * @return array
     */
    public static function parseQuestionItem($item) {

        //Define temp item
        //$newItem = [];

        //Get the list of description fields
        //$newItem['representingColumn'] = $item->representingColumn();

        //Creator profile
        //$owner = UserProfile::find()->andWhere(['user_id' => $item->created_by])->one();

        // Struttura con domande condizionate e relative risposte predefinite
        $conditions = static::getConditions($item);

        // Gestione allegati
        $file = [];
        if($item->file) {
            foreach($item->file as $attachment) {
                $data = [];
                $data['name'] = $attachment->name;
                $data['type'] = $attachment->type;
                $data['url'] = Yii::$app->getUrlManager()->createAbsoluteUrl($attachment->getWebUrl());

                $file[] = $data;
            }
        }

        // Padre della domanda corrente
        $parent = $item->parent;
        if($parent) {
            $parent = $parent->id;
        }

        // Calcolo quali sono i figli della domanda se questa è un contenitore
        $children = array_map('intval', $item->getChildren()->select('id')->column());
        if(empty($children)) {
            $children = null;
        }
        
        //Fill fields from item usable in app
        $newItem = [
            'id' => $item->id,
            'label' => $item->domanda,
            'parent' => $parent,
            'columns' => ($item->multi_columns == 'null') ? null : $item->multi_columns,
            'container_children' => $children,
            'domanda_condizionata' => $item->domanda_condizionata,
            'condizioni' => $conditions,
            'obbligatoria' => $item->obbligatoria,
            'min_risposte' => $item->min_int_multipla,
            'max_risposte' => $item->max_int_multipla,
            'descrizione' => $item->introduzione,
            'descrizione_condizionata' => $item->introduzione_condizionata,
            'tooltip' => $item->tooltip,
            'attachments' => $file,
            //'ordinamento' => $item->ordinamento,
            //'min_int_multipla' => $item->min_int_multipla,
            //'max_int_multipla' => $item->max_int_multipla,
            'type' => $item->sondaggiDomandeTipologie->id,
//            'created_at' => $item->created_at,
//            'created_by' => $item->created_by,
//            'owner' => [
//                'nome' => $owner->nome,
//                'cognome' => $owner->cognome,
//                'presentazione_breve' => $owner->presentazione_breve,
//                'avatarUrl' => $owner->avatarWebUrl,
//            ],
        ];

        return $newItem;
    }

    /**
     * @param SondaggiDomande $item
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getConditions($item) {
        $conditions = SondaggiDomandeCondizionate::find()
            ->select([
                SondaggiDomande::tableName() . '.sondaggi_domande_pagine_id',
                SondaggiRispostePredefinite::tableName() . '.sondaggi_domande_id',
                'sondaggi_risposte_predefinite_id'
            ])
            ->leftJoin(SondaggiRispostePredefinite::tableName(), SondaggiRispostePredefinite::tableName() . '.id = ' . SondaggiDomandeCondizionate::tableName() . '.sondaggi_risposte_predefinite_id')
            ->leftJoin(SondaggiDomande::tableName(), SondaggiDomande::tableName() . '.id = ' . SondaggiRispostePredefinite::tableName() . '.sondaggi_domande_id')
            ->andWhere([SondaggiDomandeCondizionate::tableName() . '.sondaggi_domande_id' => $item->id])
            ->asArray()
            ->all();

        // Trasformo gli id in interi. & utilizzato per modificare l'array di origine sennò non viene applicata la modifica
        foreach($conditions as &$condition) {
            $condition['sondaggi_domande_pagine_id'] = intval($condition['sondaggi_domande_pagine_id']);
            $condition['sondaggi_domande_id'] = intval($condition['sondaggi_domande_id']);
            $condition['sondaggi_risposte_predefinite_id'] = intval($condition['sondaggi_risposte_predefinite_id']);
        }

        // Aggiungo la condizione sulla domanda libera condizionata se esistente
        if(!is_null($item->domanda_condizionata_testo_libero)) {
            $conditions[] = [
                'sondaggi_domande_pagine_id' => $item->sondaggi_domande_pagine_id,
                'sondaggi_domande_id' => $item->domanda_condizionata_testo_libero,
                'sondaggi_risposte_predefinite_id' => null
            ];
        }

        return $conditions;
    }

    /**
     *
     * @param type $item
     * @return type
     */
    public static function parseDefaultResponseItem($item) {

        //Define temp item
        $newItem = [];

        //Get the list of description fields
        //$newItem['representingColumn'] = $item->representingColumn();

        //Creator profile
        $owner = UserProfile::findOne(['id' => $item->created_by]);

        //Fill fields from item usable in app
        $newItem = [
            'id' => $item->id,
            'label' => $item->risposta,
            //'ordinamento' => $item->ordinamento,
//            'created_at' => $item->created_at,
//            'created_by' => $item->created_by,
//            'owner' => [
//                'nome' => $owner->nome,
//                'cognome' => $owner->cognome,
//                'presentazione_breve' => $owner->presentazione_breve,
//                'avatarUrl' => $owner->avatarWebUrl,
//            ],
        ];

        return $newItem;
    }
}