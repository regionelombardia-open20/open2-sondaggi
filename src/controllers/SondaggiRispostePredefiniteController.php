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
use lispa\amos\sondaggi\models\search\SondaggiRispostePredefiniteSearch;
use lispa\amos\sondaggi\models\SondaggiRispostePredefinite;
use Yii;
use yii\helpers\Url;

/**
 * Class SondaggiRispostePredefiniteController
 * SondaggiRispostePredefiniteController implements the CRUD actions for SondaggiRispostePredefinite model.
 *
 * @property \lispa\amos\sondaggi\models\SondaggiRispostePredefinite $model
 *
 * @package lispa\amos\sondaggi\controllers
 */
class SondaggiRispostePredefiniteController extends CrudController
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
        $this->setModelObj(new SondaggiRispostePredefinite());
        $this->setModelSearch(new SondaggiRispostePredefiniteSearch());

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
     * Lists all SondaggiRispostePredefinite models.
     * @return mixed
     */
    public function actionIndex($url = null)
    {
        $idDomanda = Yii::$app->request->get('idDomanda');
        Url::remember();
        $this->setUrl($url);
        $this->setParametro($idDomanda);
        $this->setDataProvider($this->getModelSearch()->search(Yii::$app->request->getQueryParams()));
//        return parent::actionIndex($layout); // TODO sistemare questo punto cambiando totalmente la action in quanto non compatibile con gli standard di PHP 7
        return parent::actionIndex();
    }

    /**
     * Displays a single SondaggiRispostePredefinite model.
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
     * Creates a new SondaggiRispostePredefinite model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $idDomanda
     * @param string|null $url
     * @return string|\yii\web\Response
     */
    public function actionCreate($idDomanda, $url = null)
    {
        $this->setUpLayout('form');
        $this->model = new SondaggiRispostePredefinite();
        $this->model->sondaggi_domande_id = $idDomanda;

        if ($this->model->load(Yii::$app->request->post())) {
            $ordinamento = Yii::$app->request->post()['SondaggiRispostePredefinite']['ordine'];
            $ordinaDopo = 0;
            if (strlen($ordinamento) == 0) {
                $ordinamento = 'fine';
            }
            if ($ordinamento == 'dopo') {
                $ordinaDopo = Yii::$app->request->post()['SondaggiRispostePredefinite']['ordina_dopo'];
            }
            $this->model->save();
            $this->model->setOrdinamento($ordinamento, $ordinaDopo);
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
     * Updates an existing SondaggiRispostePredefinite model.
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
            $ordinamento = Yii::$app->request->post()['SondaggiRispostePredefinite']['ordine'];
            $ordinaDopo = 0;
            if (strlen($ordinamento) == 0) {
                $ordinamento = 'fine';
            }
            if ($ordinamento == 'dopo') {
                $ordinaDopo = Yii::$app->request->post()['SondaggiRispostePredefinite']['ordina_dopo'];
            }
            $this->model->save();
            $this->model->setOrdinamento($ordinamento, $ordinaDopo);
            if ($url) {
                return $this->redirect($url);
            }
            return $this->redirect(['update', 'id' => $this->model->id]);
        } else {
            return $this->render('update', [
                'model' => $this->model,
                'url' => ($url) ? $url : null,
            ]);
        }
    }

    /**
     * Deletes an existing SondaggiRispostePredefinite model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string|null $url
     * @return \yii\web\Response
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id, $url = null)
    {
        $this->model = $this->findModel($id);
        $risposte = $this->model->getSondaggiDomandeCondizionates()->count();
        $domanda = \lispa\amos\sondaggi\models\SondaggiDomande::findOne(['id' => $this->model->sondaggi_domande_id]);
        $pubbl = $domanda->getSondaggi()->one()['sondaggi_stato_id'];
        $pubblicato = \lispa\amos\sondaggi\models\SondaggiStato::findOne(['stato' => 'BOZZA'])->id;
        if ($risposte) {
            Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare la risposta in quanto sono presenti domande condizionate da questa risposta."));
        } else {
            if ($pubblicato != $pubbl) {
                Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare la risposta in quanto il sondaggio a cui è collegata non è in stato BOZZA."));
            } else {
                $this->model->delete();
                Yii::$app->getSession()->addFlash('success', AmosSondaggi::tHtml('amossondaggi', "Risposta cancellata correttamente."));
            }
        }
        if ($url) {
            return $this->redirect($url);
        } else {
            return $this->redirect(['index', 'idDomanda' => $domanda->id, 'url' => $url]);
        }
    }
}
