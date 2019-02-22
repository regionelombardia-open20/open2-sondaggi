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
use lispa\amos\sondaggi\models\search\SondaggiDomandePagineSearch;
use lispa\amos\sondaggi\models\SondaggiDomandePagine;
use lispa\amos\upload\models\FilemanagerMediafile;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class SondaggiDomandePagineController
 * SondaggiDomandePagineController implements the CRUD actions for SondaggiDomandePagine model.
 *
 * @property \lispa\amos\sondaggi\models\SondaggiDomandePagine $model
 *
 * @package lispa\amos\sondaggi\controllers
 */
class SondaggiDomandePagineController extends CrudController
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
        $this->setModelObj(new SondaggiDomandePagine());
        $this->setModelSearch(new SondaggiDomandePagineSearch());

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
     * Lists all SondaggiDomandePagine models.
     * @return mixed
     */
    public function actionIndex($idSondaggio = null, $url = null)
    {
        Url::remember();
        $this->setUrl($url);
        $this->setDataProvider($this->getModelSearch()->search(Yii::$app->request->getQueryParams()));
//        return parent::actionIndex($layout); // TODO sistemare questo punto cambiando totalmente la action in quanto non compatibile con gli standard di PHP 7
        return parent::actionIndex();
    }

    /**
     * Displays a single SondaggiDomandePagine model.
     * @param integer $id
     * @return mixed
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
     * Finds the SondaggiDomandePagine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SondaggiDomandePagine the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($this->model = SondaggiDomandePagine::findOne($id)) !== null) {
            return $this->model;
        } else {
            throw new NotFoundHttpException(AmosSondaggi::t('amossondaggi', 'La pagina richiesta non è disponibile'));
        }
    }

    /**
     * Creates a new SondaggiDomandePagine model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $idSondaggio
     * @param string|null $url
     * @return string|\yii\web\Response
     */
    public function actionCreate($idSondaggio, $url = null)
    {
        $this->setUpLayout('form');
        $this->model = new SondaggiDomandePagine();
        $this->model->sondaggi_id = $idSondaggio;

        if ($this->model->load(Yii::$app->request->post())) {
            //inizio upload immagine
            $avatar_id = null;
            $modelFile = new FilemanagerMediafile();
            $modelFile->load(Yii::$app->request->post());
            $file = UploadedFile::getInstance($modelFile, 'file');
            if ($file) {
                $routes = Yii::$app->getModule('upload')->routes;
                $modelFile->saveUploadedFile($routes, true);
                if ($modelFile->id) {
                    $avatar_id = $modelFile->id;
                    $this->model->filemanager_mediafile_id = $avatar_id;
                }
            }
            //fine upload immagine
            $this->model->save();
            /* $domanda = new \lispa\amos\sondaggi\models\SondaggiDomande();
              $domanda->sondaggi_id = $this->model->sondaggi_id;
              $domanda->sondaggi_domande_pagine_id = $this->model->id; */
            if ($url) {
                return $this->redirect($url);
            } else {
                return $this->redirect('index');
            }
        } else {
            return $this->render('create', [
                'model' => $this->model,
                'url' => $url
            ]);
        }
    }

    /**
     * Updates an existing SondaggiDomandePagine model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string|null $url
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id, $url = null)
    {
        $this->setUpLayout('form');
        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post())) {
            //inizio upload immagine
            $avatar_id = null;
            $modelFile = new FilemanagerMediafile();
            $modelFile->load(Yii::$app->request->post());
            $file = UploadedFile::getInstance($modelFile, 'file');
            if ($file) {
                $routes = Yii::$app->getModule('upload')->routes;
                $modelFile->saveUploadedFile($routes, true);
                if ($modelFile->id) {
                    $avatar_id = $modelFile->id;
                    $this->model->filemanager_mediafile_id = $avatar_id;
                }
            }
            //fine upload immagine
            $this->model->save();
            if ($url) {
                return $this->redirect($url);
            } else {
                return $this->redirect('index');
            }
        } else {
            return $this->render('update', [
                'model' => $this->model,
                'url' => ($url) ? $url : null
            ]);
        }
    }

    /**
     * Deletes an existing SondaggiDomandePagine model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @param int $idSondaggio
     * @param string|null $url
     * @return \yii\web\Response
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id, $idSondaggio, $url = null)
    {
        $this->model = $this->findModel($id);
        $domande = $this->model->getSondaggiDomandes()->count();
        $pubbl = $this->model->getSondaggi()->one()['sondaggi_stato_id'];
        $pubblicato = \lispa\amos\sondaggi\models\SondaggiStato::findOne(['stato' => 'BOZZA'])->id;
        if ($domande) {
            Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare la pagina per la presenza di domande."));
        } else {
            if ($pubblicato != $pubbl) {
                Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare la risposta in quanto il sondaggio a cui è collegata non è in stato BOZZA."));
            } else {
                $this->model->delete();
                Yii::$app->getSession()->addFlash('success', AmosSondaggi::tHtml('amossondaggi', "Pagina cancellata correttamente."));
            }
        }
        if ($url) {
            return $this->redirect($url);
        } else {
            return $this->redirect(['index', 'idSondaggio' => $idSondaggio, 'url' => $url]);
        }
    }
}
