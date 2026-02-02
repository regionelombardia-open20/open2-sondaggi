<?php

namespace open20\amos\sondaggi\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sondaggi_domande_pagine_rule".
 */
class SondaggiDomandePagineRule extends \open20\amos\sondaggi\models\base\SondaggiDomandePagineRule {

    public function representingColumn() {
        return [
//inserire il campo o i campi rappresentativi del modulo
        ];
    }

    public function attributeHints() {
        return [
        ];
    }

    /**
     * Returns the text hint for the specified attribute.
     * @param string $attribute the attribute name
     * @return string the attribute hint
     */
    public function getAttributeHint($attribute) {
        $hints = $this->attributeHints();
        return isset($hints[$attribute]) ? $hints[$attribute] : null;
    }

    public function rules() {
        return ArrayHelper::merge(parent::rules(), [
        ]);
    }

    public function attributeLabels() {
        return
                ArrayHelper::merge(
                        parent::attributeLabels(),
                        [
        ]);
    }

    public function getEditFields() {
        $labels = $this->attributeLabels();

        return [
            [
                'slug' => 'title',
                'label' => $labels['title'],
                'type' => 'string'
            ],
            [
                'slug' => 'logical_operator',
                'label' => $labels['logical_operator'],
                'type' => 'string'
            ],
            [
                'slug' => 'rule',
                'label' => $labels['rule'],
                'type' => 'integer'
            ],
            [
                'slug' => 'value',
                'label' => $labels['value'],
                'type' => 'decimal'
            ],
            [
                'slug' => 'message',
                'label' => $labels['message'],
                'type' => 'string'
            ],
            [
                'slug' => 'sondaggi_domande_pagine_id',
                'label' => $labels['sondaggi_domande_pagine_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'fields',
                'label' => $labels['fields'],
                'type' => 'text'
            ],
        ];
    }

    /**
     * @return string marker path
     */
    public function getIconMarker() {
        return null; //TODO
    }

    /**
     * If events are more than one, set 'array' => true in the calendarView in the index.
     * @return array events
     */
    public function getEvents() {
        return NULL; //TODO
    }

    /**
     * @return url event (calendar of activities)
     */
    public function getUrlEvent() {
        return NULL; //TODO e.g. Yii::$app->urlManager->createUrl([]);
    }

    /**
     * @return color event 
     */
    public function getColorEvent() {
        return NULL; //TODO
    }

    /**
     * @return title event
     */
    public function getTitleEvent() {
        return NULL; //TODO
    }
}
