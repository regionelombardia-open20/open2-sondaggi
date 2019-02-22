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
use lispa\amos\sondaggi\models\search\SondaggiRisposteSearch;
use lispa\amos\sondaggi\models\SondaggiRisposte;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Class SondaggiRisposteController
 * SondaggiRisposteController implements the CRUD actions for SondaggiRisposte model.
 *
 * @property \lispa\amos\sondaggi\models\SondaggiRisposte $model
 *
 * @package lispa\amos\sondaggi\controllers
 */
class SondaggiRisposteController extends CrudController
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
        $this->setModelObj(new SondaggiRisposte());
        $this->setModelSearch(new SondaggiRisposteSearch());

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
     * Lists all SondaggiRisposte models.
     * @param string|null $layout
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($layout = null)
    {
        Url::remember();
        $this->setDataProvider($this->getModelSearch()->search(Yii::$app->request->getQueryParams()));
        return parent::actionIndex($layout);
    }

    /**
     * Displays a single SondaggiRisposte model.
     * @param integer $id
     * @return string|\yii\web\Response
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
     * Creates a new SondaggiRisposte model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->setUpLayout('form');

        $this->model = new SondaggiRisposte();

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
                Yii::$app->getSession()->addFlash('success', AmosSondaggi::tHtml('amoscore', 'Element successfully created.'));
                return $this->redirect('index');
            } else {
                Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amoscore', 'Element not created, check the data entered.'));
            }
        }

        return $this->render('create', [
            'model' => $this->model,
        ]);
    }

    /**
     * Updates an existing SondaggiRisposte model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $this->setUpLayout('form');
        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
                Yii::$app->getSession()->addFlash('success', AmosSondaggi::tHtml('amoscore', 'Element successfully updated.'));
                return $this->redirect(['view', 'id' => $this->model->id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amoscore', 'Element not updated, check the data entered.'));
            }
        }

        return $this->render('update', [
            'model' => $this->model,
        ]);
    }

    /**
     * Deletes an existing SondaggiRisposte model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return \yii\web\Response
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->model = $this->findModel($id);
        if ($this->model) {
            $this->model->delete();
            if (!$this->model->hasErrors()) {
                Yii::$app->getSession()->addFlash('success', AmosSondaggi::t('amoscore', 'Element deleted successfully.'));
            } else {
                Yii::$app->getSession()->addFlash('danger', AmosSondaggi::t('amoscore', 'You are not authorized to delete this element.'));
            }
        } else {
            Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amoscore', 'Element not found.'));
        }
        return $this->redirect(['index']);
    }
}
