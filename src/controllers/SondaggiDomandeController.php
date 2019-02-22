<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\controllers
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi\controllers;

use lispa\amos\core\controllers\CrudController;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\sondaggi\AmosSondaggi;
use lispa\amos\sondaggi\models\search\SondaggiDomandeSearch;
use lispa\amos\sondaggi\models\SondaggiDomande;
use Yii;
use yii\helpers\Url;

/**
 * Class SondaggiDomandeController
 * SondaggiDomandeController implements the CRUD actions for SondaggiDomande model.
 *
 * @property \lispa\amos\sondaggi\models\SondaggiDomande $model
 *
 * @package lispa\amos\sondaggi\controllers
 */
class SondaggiDomandeController extends CrudController
{
    /**
     * @var string $layout
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setModelObj(new SondaggiDomande());
        $this->setModelSearch(new SondaggiDomandeSearch());

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt') . Html::tag('p', AmosSondaggi::tHtml('amossondaggi', 'Tabella')),
                'url' => '?currentView=grid'
            ]
        ]);

        parent::init();

        $this->setUpLayout();
    }

    /**
     * Lists all SondaggiDomande models.
     * @return mixed
     */
    public function actionIndex($idSondaggio = null, $idPagina = null, $url = null)
    {
        Url::remember();
        $this->setUrl($url);
        $this->setDataProvider($this->getModelSearch()->search(Yii::$app->request->getQueryParams()));
//        return parent::actionIndex($layout); // TODO sistemare questo punto cambiando totalmente la action in quanto non compatibile con gli standard di PHP 7
        return parent::actionIndex();
    }

    /**
     * Displays a single SondaggiDomande model.
     * @param integer $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->save()) {
            return $this->redirect(['view', 'id' => $this->model->id]);
        } else {
            return $this->render('view', ['model' => $this->model]);
        }
    }

    /**
     * Creates a new SondaggiDomande model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $idSondaggio
     * @param int|null $idPagina
     * @param string|null $url
     * @return string|\yii\web\Response
     */
    public function actionCreate($idSondaggio, $idPagina = null, $url = null)
    {
        $this->setUpLayout('form');
        $this->model = new SondaggiDomande();
        $this->model->sondaggi_id = $idSondaggio;
        if ($idPagina) {
            $this->model->sondaggi_domande_pagine_id = $idPagina;
        }
        if ($this->model->load(Yii::$app->request->post())) {
            $condizioneNecessaria = (isset($this->model->condizione_necessaria)) ? $this->model->condizione_necessaria : null;
            $ordinamento = Yii::$app->request->post()['SondaggiDomande']['ordine'];
            $ordinaDopo = 0;
            if (strlen($ordinamento) == 0) {
                $ordinamento = 'fine';
            }
            if ($ordinamento == 'dopo') {
                $ordinaDopo = Yii::$app->request->post()['SondaggiDomande']['ordina_dopo'];
            }
            $this->model->save();
            $this->model->setOrdinamento($ordinamento, $ordinaDopo, (isset($this->model->condizione_necessaria)) ? $this->model->condizione_necessaria : 0);
            if ($this->model->domanda_condizionata) {
                $condizione = new \lispa\amos\sondaggi\models\SondaggiDomandeCondizionate();
                $condizione->sondaggi_risposte_predefinite_id = $condizioneNecessaria;
                $condizione->sondaggi_domande_id = $this->model->id;
                $condizione->save();
            }
            if ($url) {
                $this->redirect($url);
            } else {
                return $this->redirect(['update',
                    'id' => $this->model->id,
                    'url' => ($url) ? $url : null,
                ]);
            }
        }

        return $this->render('create', [
            'model' => $this->model,
            'url' => ($url) ? $url : null,
        ]);
    }

    /**
     * Updates an existing SondaggiDomande model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param null $url
     * @return string|\yii\web\Response
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $url = null)
    {
        $this->setUpLayout('form');
        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            $ordinamento = Yii::$app->request->post()['SondaggiDomande']['ordine'];
            $ordinaDopo = 0;
            if (strlen($ordinamento) == 0) {
                $ordinamento = 'fine';
            }
            if ($ordinamento == 'dopo') {
                $ordinaDopo = Yii::$app->request->post()['SondaggiDomande']['ordina_dopo'];
            }
            $this->model->save();
            $this->model->setOrdinamento($ordinamento, $ordinaDopo, (isset($this->model->condizione_necessaria)) ? $this->model->condizione_necessaria : 0);
            if ($this->model->domanda_condizionata) {
                if ($this->model->getSondaggiRispostePredefinitesCondizionate()->count()) {
                    $idMm = $this->model->getSondaggiRispostePreCondMm()->one()['id'];
                    $condizione = \lispa\amos\sondaggi\models\SondaggiDomandeCondizionate::findOne(['id' => $idMm]);
                    $condizione->sondaggi_risposte_predefinite_id = $this->model->condizione_necessaria;
                    $condizione->save();
                } else {
                    $condizione = new \lispa\amos\sondaggi\models\SondaggiDomandeCondizionate();
                    $condizione->sondaggi_risposte_predefinite_id = $this->model->condizione_necessaria;
                    $condizione->sondaggi_domande_id = $this->model->id;
                    $condizione->save();
                }
            } else {
                if ($this->model->getSondaggiRispostePredefinitesCondizionate()->count()) {
                    $idMm = $this->model->getSondaggiRispostePreCondMm()->one()['id'];
                    $condizione = \lispa\amos\sondaggi\models\SondaggiDomandeCondizionate::findOne(['id' => $idMm]);
                    $condizione->delete();
                }
            }
            if ($url) {
                return $this->redirect($url);
            } else {
                return $this->redirect(['update', 'id' => $this->model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $this->model,
                'url' => ($url) ? $url : null,
            ]);
        }
    }

    /**
     * Deletes an existing SondaggiDomande model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @param int $idSondaggio
     * @param null $url
     * @return \yii\web\Response
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id, $idSondaggio, $url = null)
    {
        $this->model = $this->findModel($id);

        $risposte = $this->model->getSondaggiRispostePredefinites()->count();
        $pubbl = $this->model->getSondaggi()->one()['sondaggi_stato_id'];
        $pubblicato = \lispa\amos\sondaggi\models\SondaggiStato::findOne(['stato' => 'BOZZA'])->id;
        if ($risposte) {
            Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare la domanda in quanto sono presenti risposte predefinite collegate."));
        } else {
            if ($pubblicato != $pubbl) {
                Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare la domanda in quanto il sondaggio a cui è collegata non è in stato BOZZA."));
            } else {
                $this->model->delete();
                Yii::$app->getSession()->addFlash('success', AmosSondaggi::tHtml('amossondaggi', "Domanda cancellata correttamente."));
            }
        }
        if ($url) {
            return $this->redirect($url);
        } else {
            return $this->redirect(['index', 'idSondaggio' => $idSondaggio, 'url' => $url]);
        }
    }
}
