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

namespace open20\amos\sondaggi\modules\v1\controllers;

use open20\amos\attachments\FileModule;
use open20\amos\comuni\models\IstatComuni;
use open20\amos\comuni\models\IstatProvince;
use open20\amos\core\helpers\StringHelper;
use open20\amos\sondaggi\parsers\SondaggiParser;
use open20\amos\sondaggi\models\search\SondaggiSearch;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiDomande;
use open20\amos\sondaggi\models\SondaggiDomandePagine;
use open20\amos\sondaggi\models\SondaggiDomandeTipologie;
use open20\amos\sondaggi\models\SondaggiRisposte;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;
use Exception;
use http\Exception\InvalidArgumentException;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\log\Logger;
use yii\rest\Controller;
use yii\validators\Validator;

class ApiController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'sondaggi-list',
                            'sondaggi-detail',
                            'add-sondaggio',
                            'get-page',
                            'get-province',
                            'get-municipality',
                        ],
                        'roles' => ['@']
                    ]
                ]
            ],
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    'bearerAuth' => [
                        'class' => HttpBearerAuth::className(),
                    ]
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'sondaggi-list' => ['get'],
                    'sondaggi-detail' => ['get'],
                    'add-sondaggio' => ['post'],
                    'get-page' => ['get'],
                    'get-province' => ['get'],
                    'get-municipality' => ['get'],
                ],
            ],
        ]);
    }

    public function init()
    {
        parent::init();
        // Set the default timezone
        date_default_timezone_set('Europe/Rome');
    }

    /**
     *
     * @return yii\base\Module
     * @throws Exception
     */
    private function loadCwh() {
        $cwh = Yii::$app->getModule('cwh');

        if (is_null($cwh)) {
            throw new Exception('CWH not present');
        }

        return $cwh;
    }

    /**
     *
     * @return array
     */
    public function actionSondaggiList($page = null, $page_size = null, $from_date = null, $to_date = null) {
        $list = [];
        try {
            $params = [];
            $search = new SondaggiSearch();
            if (!is_null($page)) {
                $params['offset'] = $page;
            }
            if (!is_null($page_size)) {
                $params['limit'] = $page_size;
            }

            $cwh = $this->loadCwh();
            $cwh->resetCwhScopeInSession();
            $dataProvider = $search->searchOwnInterest($params);
            //$query = $dataProvider->query;
            //$query->joinWith('sondaggiRisposteSessionis')->andwhere([SondaggiRisposteSessioni::tableName().'.completato' => null]);
            //$dataProvider->query = $query;
            $dataProvider->query->andWhere([
                '<=', new Expression('DATE_FORMAT(publish_date, "%Y-%m-%d")'),
                Yii::$app->getFormatter()->asDate('now', 'php:Y-m-d')
            ]);
            $dataProvider->query->andWhere([
                '>=', new Expression('DATE_FORMAT(close_date, "%Y-%m-%d")'),
                Yii::$app->getFormatter()->asDate('now', 'php:Y-m-d')
            ]);
            //return $dataProvider->query->createCommand()->rawSql;
            $listModel = $dataProvider->getModels();
            foreach ($listModel as $model) {
                $detail = SondaggiParser::parseItem($model);
                $detail['fields']['status'] = explode('/', $model->status)[1];
                $list[] = $detail;
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $list;
//        return [
//            'totalRowCount' => $search->searchOwnInterest()->query->count(),
//            'pageSize' => count($list),
//            'pageNumber' => 1,
//            'list' => $list
//        ];
    }

    /**
     *
     * @param integer $event_id
     * @return array
     */
    public function actionSondaggiDetail($sondaggi_id) {
        $detail = [];

        try {
            $model = Sondaggi::findOne(['id' => $sondaggi_id]);
            $detail = SondaggiParser::parseItem($model);
            $detail['fields']['status'] = explode('/', $model->status)[1];
            $pages = $model->sondaggiDomandePagines;

            $detail['pages'] = [];
            foreach ($pages as $page) {
//                $thePage = SondaggiParser::parsePageItem($page);
//                $thePage['questions'] = [];
//                $questions = $page->sondaggiDomandes;
//                foreach ($questions as $question) {
//                    $theQuestion = SondaggiParser::parseQuestionItem($question);
//                    $reponses = $question->sondaggiRispostePredefinites;
//                    $theQuestion['options'] = [];
//                    foreach ($reponses as $response) {
//                        $theResponse = SondaggiParser::parseDefaultResponseItem($response);
//                        $theQuestion['options'][] = $theResponse;
//                    }
//                    $thePage['questions'][] = $theQuestion;
//                }

                $detail['pages'][] = $page->id;
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $detail;
    }

    /**
     *
     * @return array|null
     */
    public function actionAddSondaggio() {

        try {
            //Request params
            $bodyParams = \Yii::$app->getRequest()->getBodyParams();
            $mappedData = [];

            //Record interested
            if (!empty($bodyParams['id'])) {
                $bodyParams['id'] = intval($bodyParams['id']);
                $record = Sondaggi::findOne(['id' => $bodyParams['id']]);

                $q = SondaggiRisposteSessioni::find();
                $q->andWhere([SondaggiRisposteSessioni::tableName() . '.sondaggi_id' => $record->id])
                    ->andWhere([SondaggiRisposteSessioni::tableName() . '.user_id' => \Yii::$app->user->id])
                    ->andWhere([SondaggiRisposteSessioni::tableName() . '.completato' => 0]);
                $session = $q->one();
                if (is_null($session)) {
                    $session = new SondaggiRisposteSessioni();
                    $session->begin_date = date('Y-m-d H:i:s');
                    $session->end_date = null;
                    $session->sondaggi_id = $record->id;
                    $session->user_id = \Yii::$app->user->id;
                    if (!$session->save()) {
                        return $this->echoError($session);
                    }
                }
                $lastPage = $this->lastPageId($record);

                // Id tipologie che prevedono una risposta con opzioni di scelta
                $domandeTipologieWithOptions = SondaggiDomandeTipologie::find()
                    ->select('id')
                    ->andWhere(['in', 'html_type', ['checkbox', 'select-multiple', 'select', 'radio']])
                    ->andWhere(['attivo' => 1])
                    ->column();

                // Id tipologie che prevedono una risposta con allegati
                $domandeTipologieWithAttachments = SondaggiDomandeTipologie::find()
                    ->select('id')
                    ->andWhere(['in', 'html_type', ['file', 'file-multiple']])
                    ->andWhere(['attivo' => 1])
                    ->column();

                if (isset($bodyParams['pages']) && is_array($bodyParams['pages'])) {
                    $totalErrors = 0;
                    foreach ($bodyParams['pages'] as $pageKey => &$page) {
                        // Conversione in intero dell'id
                        $page['id'] = intval($page['id']);

                        if (isset($page['questions'])) {
                            foreach ($page['questions'] as $questionKey => &$question) {
                                // Conversione in intero dei dati numerici
                                $question['id'] = intval($question['id']);
                                $question['type'] = intval($question['type']);

                                $domanda = SondaggiDomande::findOne($question['id']);
                                $response = null;
                                // Condizioni per far apparire la domanda
                                $conditions = SondaggiParser::getConditions($domanda);
                                $skipQuestion = $this->checkConditions($conditions, $bodyParams['pages'], $mappedData);
                                $isContainer = !empty($question['container_children']); // Array contenente elenco dei figli
                                if($skipQuestion || $isContainer) { // Salto la domanda perchè condizionata e invisibile oppure contenitrice
                                    $response = '';
                                } elseif(in_array($question['type'], $domandeTipologieWithOptions)) {
                                    $checkedValuesCount = $this->getCheckedCount($question['options']);
                                    if($domanda->min_int_multipla > $checkedValuesCount) {
                                        $response = "Risposte minime richieste: $domanda->min_int_multipla";
                                    } elseif($domanda->max_int_multipla != 0 && $domanda->max_int_multipla < $checkedValuesCount) {
                                        $response = "Risposte massime richieste: $domanda->max_int_multipla";
                                    } elseif($checkedValuesCount == 0 && $domanda->obbligatoria) {
                                        $response = "La domanda è obbligatoria";
                                    } else {
                                        if(!empty($question['values'])) {
                                            foreach($question['values'] as $column => $value) {
                                                $response = $this->addSondaggiRisposte($question, $session, $question['value'], $value, $column);
                                            }
                                        } else {
                                            foreach ($question['options'] as &$option) {
                                                if ($option['checked'] === "true") {
                                                    $response = $this->addSondaggiRisposte($question, $session, $question['value'], $option['id']);
                                                }
                                            }
                                        }
                                    }
                                } elseif(in_array($question['type'], $domandeTipologieWithAttachments)) {
                                    $response = $this->addSondaggiRisposte($question, $session, null, null, null, $pageKey, $questionKey);
                                } else {
                                    if(!empty($question['values'])) {
                                        foreach($question['values'] as $column => $value) {
                                            $response = $this->addSondaggiRisposte($question, $session, $value, null, $column);
                                        }
                                    } else {
                                        $response = $this->addSondaggiRisposte($question, $session, $question['value']);
                                    }
                                }
                                if(is_object($response)) {
                                    if ($response->hasErrors()) {
                                        $question['errors'] = $this->echoError($response);
                                        $totalErrors++;
                                    } else {
                                        $question['errors'] = [];
                                    }
                                } else {
                                    if(!empty($response)) {
                                        $question['errors'] = [$response];
                                        $totalErrors++;
                                    } else {
                                        $question['errors'] = [];
                                    }
                                }
                            }
                        }
                        if ($page['id'] == $lastPage && $totalErrors == 0) {
                            $session->end_date = date('Y-m-d H:i:s');
                            $session->completato = 1;
                            $session->save();
                        }
                    }
                } else { // C'è un problema con i dati, il parsing fallirebbe quindi torno errore
                    return ['error' => true, 'message' => 'Formato dati errato', 'code' => 400];
                }
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            return ['error' => true, 'message' => $ex->getMessage(), 'code' => 400];
        }

        return ($totalErrors > 0) ? $bodyParams : null;
    }

    /**
     * @param integer $pageKey
     * @param integer $questionKey
     * @param SondaggiDomande $domanda
     * @param SondaggiRisposte $model
     * @return string|void
     */
    public function checkAttachment($pageKey, $questionKey, $domanda, $model) {
        // Check if a file is uploaded and there are no errors
        if (isset($_FILES['pages'])) {// && $_FILES['fileUpload']['error'] === 0) {
            $file = $_FILES['pages'];

            // File properties
            $filesName = $file['name'][$pageKey]['questions'][$questionKey]['selectedFiles'];  // Original file name
            $filesName = empty($filesName)? []: $filesName;
            $filesTmpPath = $file['tmp_name'][$pageKey]['questions'][$questionKey]['selectedFiles'];  // Temporary file path
            $uploadDir = 'uploads/';  // Directory to store the uploaded file

            if($domanda->obbligatoria && count($filesName) == 0) {
                return 'La domanda è obbligatoria';
            }

            for($i = 0; $i < count($filesName); $i++) {
                $fileName = basename($filesName[$i]);
                $fileTmpPath = $filesTmpPath[$i];
                $destination = $uploadDir . $fileName;  // Destination file path

                // Move the uploaded file to the destination directory
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $saved = \Yii::$app->getModule('attachments')->attachFile($destination, $model, 'domanda_' . $domanda->id, true);
                    if(!$saved) {
                        return 'Non è stato possibile salvare l\'immagine';
                    }
                } else {
                    return 'Non è stato possibile salvare l\'immagine';
                }
            }
        } else {
            if($domanda->obbligatoria) {
                return 'Nessun file inviato';
            }
        }
    }

    public function getCheckedCount($options) {
        $count = 0;
        foreach($options as $option) {
            if(isset($option['checked']) && $option['checked'] === "true") {
                $count++;
            }
        }
        return $count;
    }

    public function checkConditions($conditions, $data, &$mappedData) {
        if(count($conditions) == 0) { // Domanda non condizionata, è sicuramente visibile
            return false;
        }
        if(count($mappedData) == 0) { // Effettuo mappatura per accedere rapidamente nei controlli
            $mappedData = $this->mapData($data);
        }

        $continue = true;
        $isQuestionSkippable = true;
        $i = 0;
        while($i < count($conditions) && $continue) {
            $pageId = $conditions[$i]['sondaggi_domande_pagine_id'];
            $questionId = $conditions[$i]['sondaggi_domande_id'];
            $optionId = $conditions[$i]['sondaggi_risposte_predefinite_id'];

            $mappedPage = $mappedData[$pageId]['page'];
            $mappedQuestion = $mappedData[$pageId]['questions'][$questionId]['question'];

            if(is_null($optionId)) { // Risposte libere
                $value = $data[$mappedPage]['questions'][$mappedQuestion]['value'];
                $isValidCondition = !empty($value);
            } else { // Con risposte predefinite
                $mappedOption = $mappedData[$pageId]['questions'][$questionId]['options'][$optionId];
                $value = $data[$mappedPage]['questions'][$mappedQuestion]['options'][$mappedOption]['checked'];
                $isValidCondition = ($value === "true");
            }

            if($isValidCondition) {
                $continue = false;
                $isQuestionSkippable = false;
            }
            $i++;
        }
        return $isQuestionSkippable;
    }

    public function mapData($data) {
        $mappedData = [];
        foreach($data as $key => $page) {
            $mappedData[$page['id']] = [
                'page' => $key,
                'questions' => []
            ];
            foreach ($page['questions'] as $key => $question) {
                $mappedData[$page['id']]['questions'][$question['id']] = [
                    'question' => $key,
                    'options' => []
                ];
                foreach($question['options'] as $key => $option) {
                    $mappedData[$page['id']]['questions'][$question['id']]['options'][$option['id']] = $key;
                }
            }
        }
        return $mappedData;
    }

    /**
     *
     * @return array
     */
    private function echoError($record) {
        $errors = $record->getErrors();
        $firstError = reset($errors);

        return [
            'error' => true,
            'message' => reset($firstError),
            'more' => $errors
        ];
    }

    /**
     * @param $question
     * @param $session
     * @param $value_text
     * @param $value_predefinite
     * @param null $index_column
     * @param $page_key
     * @param $question_key
     * @return SondaggiRisposte|string|null
     */
    private function addSondaggiRisposte($question, $session, $value_text, $value_predefinite = null, $index_column = null, $page_key = null, $question_key = null) {
        $response = new SondaggiRisposte();
        $response->sondaggi_domande_id = $question['id'];
        $response->sondaggi_risposte_sessioni_id = $session->id;
        $response->risposta_libera = $value_text;
        $response->sondaggi_risposte_predefinite_id = $value_predefinite;
        $response->column = $index_column;

        $domanda = SondaggiDomande::findOne($question['id']);
        $rule = $domanda->sondaggiDomandeRuleMms[0]->sondaggiDomandeRule;

        // 3 tipologie di validazione in base a quale dei 3 attributi non vale NULL
        $error = false;
        $isValidationExecuted = false;
        if($rule) {
            if (!is_null($rule->standard)) {
                if (!empty($response->risposta_libera)) {
                    $validator = Validator::createValidator($rule->standard, $response, 'risposta_libera');
                    $error = $validator->validate($response->risposta_libera) ? false : $rule->descrizione;
                    $isValidationExecuted = true;
                }
            } elseif (!is_null($rule->namespace)) {
                if (!empty($response->risposta_libera)) {
                    $validator = new $rule->namespace;
                    $error = $validator->validateAttribute($response, 'risposta_libera') ? false : $rule->descrizione;
                    $isValidationExecuted = true;
                }
            } elseif (!is_null($rule->codice_custom)) {
                if (!empty($response->risposta_libera)) {
                    $params = explode(',', str_replace(' ', '', html_entity_decode($rule->codice_custom)));
                    $classname = str_replace('::className()', '', $params[0]);
                    unset($params[0]);
                    $validatorParams = [];
                    foreach ($params as $param) {
                        $explodedParam = explode('=>', $param);
                        if ($explodedParam[0] != '') {
                            $key = str_replace("'", '', $explodedParam[0]);
                            $value = $explodedParam[1];
                            $validatorParams[$key] = $value;
                        }
                    }
                    $validator = new $classname;
                    $error = $validator->validateAttribute($response, 'risposta_libera') ? false : $rule->descrizione;
                    $isValidationExecuted = true;
                }
            }
        }

        /*
         * L'errore nasce solo per le risposte libere, in quanto per quelle predefinite la validazione è a monte.
         * Quindi mi aspetto che $value_predefinite sia null, che non sia già stata fatta validazione (l'errore si)
         * prende da li), che sia obbligatoria, senza risposta e per finire che non sia un attachment (non devono essere definite le chiavi
         * di pagina e di domanda).
        */
        if(is_null($value_predefinite) && !$isValidationExecuted && $domanda->obbligatoria && empty($value_text) && is_null($page_key) && is_null($question_key)) {
            $error = "La domanda è obbligatoria";
        }

        if($error) {
            return $error;
        }

        $response->save();

        // Se la domanda è di tipo attachment allora creo gli attachments
        if(!is_null($page_key) && !is_null($question_key)) {
            $response = $this->checkAttachment($page_key, $question_key, $domanda, $response);
        }

        return $response;
    }

    /**
     *
     * @param type $sondaggio
     * @return type
     */
    private function lastPageId($sondaggio) {
        return $sondaggio->getSondaggiDomandePagines()->max('id');
    }

    public function actionGetPage($sondaggi_id, $pagina_id) {
        $pagina = SondaggiDomandePagine::find()
            ->andWhere(['sondaggi_id' => $sondaggi_id])
            ->andWhere(['id' => $pagina_id])
            ->one();

        $thePage = SondaggiParser::parsePageItem($pagina);
        $thePage['questions'] = [];

        // Solo domande non figlie
        $questions = $pagina->getSondaggiDomandes()
            ->andWhere(['parent_id' => null])
            ->orderBy(['ordinamento' => SORT_ASC])
            ->all();
        foreach ($questions as $question) {
            $newQuestion = $this->addQuestionToGetPage($question);
            if (in_array($newQuestion['type'], [10,11])) {
                $module = FileModule::instance();
                $newQuestion['file_size'] = $module->maxFileSize;
            }

            $thePage['questions'][] = $newQuestion;

            // Aggiungo all'elenco anche le domande figlie
            $children = $question->getChildren()->all();
            foreach ($children as $child) {
                $newChildQuestion = $this->addQuestionToGetPage($child);
                $newChildQuestion['options'] = $newQuestion['options'];
                $newChildQuestion['obbligatoria'] = $newQuestion['obbligatoria'];
                $thePage['questions'][] = $newChildQuestion;
            }
        }
        return $thePage;
    }

    public function addQuestionToGetPage($question) {
        $theQuestion = SondaggiParser::parseQuestionItem($question);
        $responses = $question->sondaggiRispostePredefinites;
        $theQuestion['options'] = [];
        foreach ($responses as $response) {
            $theResponse = SondaggiParser::parseDefaultResponseItem($response);
            $theQuestion['options'][] = $theResponse;
        }
        return $theQuestion;
    }

    /**
     *
     * @param integer $id
     * @return void
     */
    public function actionGetProvince($id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];

        // All data
        if (is_null($id)) {
            $all = IstatProvince::find()->orderBy('nome')->asArray()->all();

            $toret['status'] = 'ok';
            $toret['data'] = $all;
            return $toret;
        }

        $province = IstatProvince::findOne(['id' => $id]);

        if(empty($province)){
            $toret['status'] = 'error';
            $toret['messages'][] = 'Provincia non trovata';
            return $toret;
        }

        $toret['status'] = 'ok';
        $toret['data'] = $province->toArray();
        return $toret;
    }

    /**
     * @param $provinceId
     * @param $id
     * @return null[]
     */
    public function actionGetMunicipality($provinceId = null, $id = null, $search = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $toRet = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];

        try {

            // vince il parametro id
            if (!is_null($id)) {
                $comune = IstatComuni::findOne(['id' => $id]);

                if(empty($comune)){
                    $toRet['status'] = 'error';
                    $toRet['messages'][] = 'Comune non trovato';
                    return $toRet;
                }

                $toRet['status'] = 'ok';
                $toRet['data'] = $comune->toArray();
                return $toRet;
            }

            $searchOk = (!is_null($search) && strlen($search) >= 3);

            if ($searchOk || !is_null($provinceId)) {
                $query = IstatComuni::find();

                if ($searchOk) {
                    $query->andWhere('nome LIKE "%' . $search . '%"');
                }

                if (!is_null($provinceId)) {
                    $query->andWhere(['istat_province_id' => $provinceId]);
                }

                $result = $query->orderBy('nome')->asArray()->all();
                $toRet['status'] = 'ok';
                $toRet['data'] = $result;
                return $toRet;
            }

            $toRet['status'] = 'ok';
            $toRet['data'] = [];
            return $toRet;
        } catch (Exception $ex) {
            $toRet['status'] = 'error';
            $toRet['messages'][] = $ex->getMessage();
            return $toRet;
        }
    }

}
