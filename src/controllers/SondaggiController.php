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
use lispa\amos\sondaggi\models\Risposte;
use lispa\amos\sondaggi\models\search\SondaggiSearch;
use lispa\amos\sondaggi\models\Sondaggi;
use lispa\amos\sondaggi\models\SondaggiDomande;
use lispa\amos\sondaggi\models\SondaggiDomandeCondizionate;
use lispa\amos\sondaggi\models\SondaggiDomandePagine;
use lispa\amos\sondaggi\models\SondaggiRispostePredefinite;
use lispa\amos\sondaggi\models\SondaggiStato;
use lispa\amos\upload\models\FilemanagerMediafile;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * Class SondaggiController
 * SondaggiController implements the CRUD actions for Sondaggi model.
 *
 * @property \lispa\amos\sondaggi\models\Sondaggi $model
 *
 * @package lispa\amos\sondaggi\controllers
 */
class SondaggiController extends CrudController
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
        $this->setModelObj(new Sondaggi());
        $this->setModelSearch(new SondaggiSearch());

        $this->setAvailableViews([
            'list' => [
                'name' => 'list',
                'label' => AmosIcons::show('view-list').Html::tag('p', AmosSondaggi::tHtml('amossondaggi', 'Lista')),
                'url' => '?currentView=list'
            ],
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt').Html::tag('p',
                    AmosSondaggi::tHtml('amossondaggi', 'Tabella')),
                'url' => '?currentView=grid'
            ]
        ]);

        parent::init();

        $this->setUpLayout();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(),
                [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'risultati',
                            ],
                            'roles' => ['AMMINISTRAZIONE_SONDAGGI', 'COMPILATORE_SONDAGGI']
                        ]
                    ]
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'risultati' => ['post', 'get']
                    ]
                ]
        ]);
        return $behaviors;
    }

    /**
     * Lists all Sondaggi models.
     * @param string|null $layout
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex($layout = null)
    {
        Url::remember();
        $this->setDataProvider($this->getModelSearch()->search(Yii::$app->request->getQueryParams()));
        return parent::actionIndex($layout);
    }

    /**
     * Displays a single Sondaggi model.
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
     * @param integer $id
     * @param int $idPagina
     * @return string
     */
    public function actionRisultati($id, $idPagina = -2)
    {
        $url      = 'index';
        $risposte = new Risposte();
        if (!empty($_GET['filter']) && (!empty($_GET['filter']['data_inizio']) || !empty($_GET['filter']['data_fine']) || !empty($_GET['filter']['area_formativa'])
            || !empty($_GET['filter']['attivita']))) {
            $rispLoad['Risposte'] = $_GET['filter'];
            $risposte->load($rispLoad);
        }
        $data        = [];
        $this->model = Sondaggi::findOne(['id' => $id]);
        /* $pagine = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC'); */
        $pagine      = $this->model->getSondaggiDomandePagines()
            ->joinWith('sondaggiDomandes')
            ->andWhere(['IN', 'sondaggi_domande.sondaggi_domande_tipologie_id', [1, 2, 3, 4]])
            ->groupBy('sondaggi_domande_pagine.id')
            ->orderBy('sondaggi_domande_pagine.id ASC');
        /* ->innerJoin('sondaggi_domande', 'sondaggi_domande_pagine.id = sondaggi_domande.sondaggi_domande_pagine_id')
          ->andWhere(['IN', 'sondaggi_domande.sondaggi_domande_tipologie_id', [1,2,3,4]])
          ->orderBy('sondaggi_domande_pagine.id ASC'); */

        $primaPagina    = $pagine->all()[0]['id'];
        $ultimaPagina   = $pagine->all()[$pagine->count() - 1]['id'];
        $prossimaPagina = null;
        $arrayPag       = [];
        foreach ($pagine->all() as $Pag) {
            $arrayPag[] = $Pag['id'];
        }

        if (count($this->model)) {
            if ($idPagina > -2) {

                if (\Yii::$app->request->isPost) {
                    $data = \Yii::$app->request->post();
                    $risposte->load($data);
                }

                if ($idPagina == 0) {
                    $prossimaPagina   = 0;
                    $paginaPrecedente = $ultimaPagina;
                } else if ($idPagina == -1) {
                    $prossimaPagina   = $primaPagina;
                    $paginaPrecedente = -1;
                } else {
                    $IndicePag        = array_search($idPagina, $arrayPag);
                    $prossimaPagina   = (count($arrayPag) > 1) ? (($idPagina == $ultimaPagina) ? 0 : $arrayPag[$IndicePag
                        + 1]) : 0;
                    $paginaPrecedente = (count($arrayPag) > 1) ? (($idPagina == $primaPagina) ? -1 : $arrayPag[$IndicePag
                        - 1]) : -1;
                }

                return $this->render('risultati',
                        [
                        'model' => $this->model,
                        'primaPagina' => $primaPagina,
                        'prossimaPagina' => $prossimaPagina,
                        'paginaPrecedente' => $paginaPrecedente,
                        'ultimaPagina' => $ultimaPagina,
                        'idPagina' => $idPagina,
                        'risposte' => $risposte->getDati($id, $idPagina),
                        //  'report' => $risposte->getReport($id, $idPagina),
                        'domande' => $risposte->getDomandeStatistiche($id, $idPagina),
                        'tipo' => $risposte->getTipologia($id),
                        'filter' => $risposte,
                ]);
            } else {

                if (\Yii::$app->request->isPost) {
                    $data = \Yii::$app->request->post();
                    $risposte->load($data);
                }

                $idPagina         = -1;
                $prossimaPagina   = $primaPagina;
                $paginaPrecedente = -1;

                return $this->render('risultati',
                        [
                        'model' => $this->model,
                        'primaPagina' => $primaPagina,
                        'prossimaPagina' => $prossimaPagina,
                        'paginaPrecedente' => $paginaPrecedente,
                        'ultimaPagina' => $ultimaPagina,
                        'idPagina' => $idPagina,
                        'risposte' => $risposte->getDati($id, $idPagina),
                        // 'report' => $risposte->getReport($id, $idPagina),
                        'domande' => $risposte->getDomandeStatistiche($id, $idPagina),
                        'tipo' => $risposte->getTipologia($id),
                        'filter' => $risposte,
                ]);
            }
        } else {
            return $this->render($url);
        }
    }

    /**
     * Creates a new Sondaggi model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @param string|null $url
     * @return string
     */
    public function actionCreate($url = null)
    {
        $this->setUpLayout('form');
        $this->model = new Sondaggi();
        $pagine      = new SondaggiDomandePagine();
        $domanda     = new SondaggiDomande();
        $risposta    = new SondaggiRispostePredefinite();
        $urlSondaggi = [['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi'), 'url' => ['index']]];
        $navP        = isset($_POST['pagina']) ? 1 : 0;
        $navD        = isset($_POST['domanda']) ? 1 : 0;
        if ($url) {
            if ($this->model->load(Yii::$app->request->post())) {
                $avatar_id = null;
                $modelFile = new FilemanagerMediafile();
                $modelFile->load(Yii::$app->request->post());
                $file      = UploadedFile::getInstance($modelFile, 'file');
                if ($file) {
                    $routes = Yii::$app->getModule('upload')->routes;
                    $modelFile->saveUploadedFile($routes, true);
                    if ($modelFile->id) {
                        $avatar_id = $modelFile->id;
                    }
                }
                $this->model->filemanager_mediafile_id = $avatar_id;
                $this->model->pubblico                 = \Yii::$app->request->post()['Sondaggi']['pubblico'];
                $this->model->tipologie_entita         = \Yii::$app->request->post()['Sondaggi']['tipologie_entita'];
                if ($this->model->pubblico == 0) {
                    if (\Yii::$app->request->post()['Sondaggi']['destinatari_pubblicazione'] != '') {
                        $this->model->save();
                        \lispa\amos\sondaggi\models\SondaggiPubblicazione::deleteAll(['sondaggi_id' => $this->model->id]);
                        foreach (\Yii::$app->request->post()['Sondaggi']['destinatari_pubblicazione'] as $Destinatario) {
                            if (count($this->model->tipologie_entita) > 0 && is_array($this->model->tipologie_entita)) {
                                foreach ($this->model->tipologie_entita as $TipologAtt) {
                                    $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                                    $destPubb->setOtherAttribute(\Yii::$app->request->post());
                                    $destPubb->sondaggi_id      = $this->model->id;
                                    $destPubb->ruolo            = $Destinatario;
                                    $destPubb->tipologie_entita = $TipologAtt;
                                    $destPubb->save();
                                }
                            } else {
                                $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                                $destPubb->setOtherAttribute(\Yii::$app->request->post());
                                $destPubb->sondaggi_id      = $this->model->id;
                                $destPubb->ruolo            = $Destinatario;
                                $destPubb->tipologie_entita = $TipologAtt;
                                $destPubb->save();
                            }
                        }
                        $pagine->sondaggi_id = $this->model->id;
                        return $this->redirect('index');
                    } else {
                        $this->model->addError('destinatari_pubblicazione',
                            AmosSondaggi::t('amossondaggi', 'Destinatari Pubblicazione non può essere vuoto'));
                        return $this->render('create',
                                [
                                'model' => $this->model,
                                'url' => $url,
                                'public' => "false"
                        ]);
                    }
                } else {
                    //$attivita = \Yii::$app->request->post()['Sondaggi']['attivita_formativa'];
                    if (count($this->model->tipologie_entita) > 0 && is_array($this->model->tipologie_entita)) {
                        $this->model->save();
                        \lispa\amos\sondaggi\models\SondaggiPubblicazione::deleteAll(['sondaggi_id' => $this->model->id]);
                        foreach ($this->model->tipologie_entita as $TipologAtt) {
                            $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                            $destPubb->setOtherAttribute(\Yii::$app->request->post());
                            $destPubb->sondaggi_id      = $this->model->id;
                            //$destPubb->entita_id = $attivita;
                            $destPubb->ruolo            = 'PUBBLICO';
                            $destPubb->tipologie_entita = $TipologAtt;
                            $destPubb->save(FALSE);
                        }
                        $pagine->sondaggi_id = $this->model->id;
                        return $this->redirect('index');
                    } else {
                        $this->model->save();
                        \lispa\amos\sondaggi\models\SondaggiPubblicazione::deleteAll(['sondaggi_id' => $this->model->id]);
                        $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                        $destPubb->setOtherAttribute(\Yii::$app->request->post());
                        $destPubb->sondaggi_id      = $this->model->id;
                        //$destPubb->entita_id = $attivita;
                        $destPubb->ruolo            = 'PUBBLICO';
                        $destPubb->tipologie_entita = 0;
                        $destPubb->save(FALSE);
                        $pagine->sondaggi_id        = $this->model->id;
                        return $this->redirect('index');
                    }
                }
            } else {
                return $this->render('create',
                        [
                        'model' => $this->model,
                        'url' => $url
                ]);
            }
        } else {
            if ($this->model->load(Yii::$app->request->post())) {
                $avatar_id = null;
                $modelFile = new FilemanagerMediafile();
                $modelFile->load(Yii::$app->request->post());
                $file      = UploadedFile::getInstance($modelFile, 'file');
                if ($file) {
                    $routes = Yii::$app->getModule('upload')->routes;
                    $modelFile->saveUploadedFile($routes, true);
                    if ($modelFile->id) {
                        $avatar_id = $modelFile->id;
                    }
                }
                $this->model->filemanager_mediafile_id = $avatar_id;
                $this->model->pubblico                 = \Yii::$app->request->post()['Sondaggi']['pubblico'];
                $this->model->tipologie_entita         = \Yii::$app->request->post()['Sondaggi']['tipologie_entita'];
                if ($this->model->pubblico == 0) {
                    if (\Yii::$app->request->post()['Sondaggi']['destinatari_pubblicazione'] != '') {
                        $this->model->save();
                        \lispa\amos\sondaggi\models\SondaggiPubblicazione::deleteAll(['sondaggi_id' => $this->model->id]);
                        if (count($this->model->tipologie_entita) > 0 && is_array($this->model->tipologie_entita)) {
                            foreach ($this->model->tipologie_entita as $TipologAtt) {
                                foreach (\Yii::$app->request->post()['Sondaggi']['destinatari_pubblicazione'] as $Destinatario) {
                                    $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                                    $destPubb->setOtherAttribute(\Yii::$app->request->post());
                                    $destPubb->sondaggi_id      = $this->model->id;
                                    $destPubb->ruolo            = $Destinatario;
                                    $destPubb->tipologie_entita = $TipologAtt;
                                    $destPubb->save();
                                }
                            }
                        } else {
                            foreach (\Yii::$app->request->post()['Sondaggi']['destinatari_pubblicazione'] as $Destinatario) {
                                $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                                $destPubb->setOtherAttribute(\Yii::$app->request->post());
                                $destPubb->sondaggi_id      = $this->model->id;
                                $destPubb->ruolo            = $Destinatario;
                                $destPubb->tipologie_entita = 0;
                                $destPubb->save();
                            }
                        }
                        $pagine->sondaggi_id = $this->model->id;
                        return $this->render('/sondaggi-domande-pagine/create',
                                [
                                'model' => $pagine,
                        ]);
                    } else {
                        $this->model->addError('destinatari_pubblicazione',
                            AmosSondaggi::t('amossondaggi', 'Destinatari Pubblicazione non può essere vuoto'));
                        return $this->render('create',
                                [
                                'model' => $this->model,
                                'public' => "false"
                        ]);
                    }
                } else {
                    //$attivita = \Yii::$app->request->post()['Sondaggi']['attivita_formativa'];
                    if (count($this->model->tipologie_entita) > 0 && is_array($this->model->tipologie_entita)) {
                        $this->model->save();
                        \lispa\amos\sondaggi\models\SondaggiPubblicazione::deleteAll(['sondaggi_id' => $this->model->id]);
                        foreach ($this->model->tipologie_entita as $TipologAtt) {
                            $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                            $destPubb->setOtherAttribute(\Yii::$app->request->post());
                            $destPubb->sondaggi_id      = $this->model->id;
                            //$destPubb->entita_id = $attivita;
                            $destPubb->ruolo            = 'PUBBLICO';
                            $destPubb->tipologie_entita = $TipologAtt;
                            $destPubb->save(FALSE);
                        }
                        $pagine->sondaggi_id = $this->model->id;
                        return $this->render('/sondaggi-domande-pagine/create',
                                [
                                'model' => $pagine,
                        ]);
                    } else {
                        $this->model->save();
                        \lispa\amos\sondaggi\models\SondaggiPubblicazione::deleteAll(['sondaggi_id' => $this->model->id]);
                        $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                        $destPubb->setOtherAttribute(\Yii::$app->request->post());
                        $destPubb->sondaggi_id      = $this->model->id;
                        //$destPubb->entita_id = $attivita;
                        $destPubb->ruolo            = 'PUBBLICO';
                        $destPubb->tipologie_entita = 0;
                        $destPubb->save(FALSE);
                        $pagine->sondaggi_id        = $this->model->id;
                        return $this->render('/sondaggi-domande-pagine/create',
                                [
                                'model' => $pagine,
                        ]);
                    }
                }
            } else if ($pagine->load(Yii::$app->request->post())) {
                //inizio upload immagine
                $avatar_id = null;
                $modelFile = new FilemanagerMediafile();
                $modelFile->load(Yii::$app->request->post());

                $file = UploadedFile::getInstance($modelFile, 'file');
                if ($file) {
                    $routes = Yii::$app->getModule('upload')->routes;
                    $modelFile->saveUploadedFile($routes, true);
                    if ($modelFile->id) {
                        $avatar_id                        = $modelFile->id;
                        $pagine->filemanager_mediafile_id = $avatar_id;
                    }
                }
                //fine upload immagine
                $pagine->save();
                $domanda->sondaggi_id                = $pagine->sondaggi_id;
                $domanda->sondaggi_domande_pagine_id = $pagine->id;
                //Yii::$app->view->params['breadcrumbs'] = $urlSondaggi;
                return $this->render('/sondaggi-domande/create',
                        [
                        'model' => $domanda
                ]);
            } else if ($domanda->load(Yii::$app->request->post())) {
                $ordinamento = Yii::$app->request->post()['SondaggiDomande']['ordine'];
                $ordinaDopo  = 0;
                if (strlen($ordinamento) == 0) {
                    $ordinamento = 'fine';
                }
                if ($ordinamento == 'dopo') {
                    $ordinaDopo = Yii::$app->request->post()['SondaggiDomande']['ordina_dopo'];
                }
                $tipoDomanda = $domanda->sondaggiDomandeTipologie->id;
                if ($domanda->domanda_condizionata == 1 && !isset($domanda->condizione_necessaria)) {
                    $domanda->domanda_condizionata = 0;
                }
                $domanda->save();
                if ($domanda->domanda_condizionata && isset($domanda->condizione_necessaria)) {
                    $domandaCondizioneMm                                   = new SondaggiDomandeCondizionate();
                    $domandaCondizioneMm->sondaggi_domande_id              = $domanda->id;
                    $domandaCondizioneMm->sondaggi_risposte_predefinite_id = $domanda->condizione_necessaria;
                    $domandaCondizioneMm->save();
                }
                $domanda->setOrdinamento($ordinamento, $ordinaDopo,
                    (isset($domanda->condizione_necessaria)) ? $domanda->condizione_necessaria : 0);
                //Yii::$app->view->params['breadcrumbs'] = $urlSondaggi;
                if ($tipoDomanda == 1 || $tipoDomanda == 2 || $tipoDomanda == 3 || $tipoDomanda == 4) {
                    $risposta->sondaggi_domande_id = $domanda->id;
                    $risposta->tipo_domanda        = $tipoDomanda;
                    return $this->render('/sondaggi-risposte-predefinite/create',
                            [
                            'model' => $risposta,
                    ]);
                } else if ($tipoDomanda == 5 || $tipoDomanda == 6) {
                    if ($navP) {
                        $newPagina              = new SondaggiDomandePagine();
                        $newPagina->sondaggi_id = $domanda->sondaggi_id;
                        return $this->render('/sondaggi-domande-pagine/create',
                                [
                                'model' => $newPagina,
                        ]);
                    } else {
                        $newDomanda                             = new SondaggiDomande();
                        $newDomanda->sondaggi_id                = $domanda->sondaggi_id;
                        $newDomanda->sondaggi_domande_pagine_id = $domanda->sondaggi_domande_pagine_id;
                        return $this->render('/sondaggi-domande/create',
                                [
                                'model' => $newDomanda,
                        ]);
                    }
                }
            } else if ($risposta->load(Yii::$app->request->post())) {
                $tipoDomanda = $risposta->tipo_domanda;
                $ordinamento = Yii::$app->request->post()['SondaggiRispostePredefinite']['ordine'];
                $ordinaDopo  = 0;
                if (strlen($ordinamento) == 0) {
                    $ordinamento = 'fine';
                }
                if ($ordinamento == 'dopo') {
                    $ordinaDopo = Yii::$app->request->post()['SondaggiRispostePredefinite']['ordina_dopo'];
                }
                $risposta->save();
                $risposta->setOrdinamento($ordinamento, $ordinaDopo);
                //Yii::$app->view->params['breadcrumbs'] = $urlSondaggi;
                if ($navP) {
                    $newPagina              = new SondaggiDomandePagine();
                    $newPagina->sondaggi_id = $risposta->getSondaggiDomande()->one()['sondaggi_id'];
                    return $this->render('/sondaggi-domande-pagine/create',
                            [
                            'model' => $newPagina,
                    ]);
                } else if ($navD) {
                    $newDomanda                             = new SondaggiDomande();
                    $newDomanda->sondaggi_id                = $risposta->getSondaggiDomande()->one()['sondaggi_id'];
                    $newDomanda->sondaggi_domande_pagine_id = $risposta->getSondaggiDomande()->one()['sondaggi_domande_pagine_id'];
                    return $this->render('/sondaggi-domande/create',
                            [
                            'model' => $newDomanda,
                            'idSondaggio' => $risposta->getSondaggiDomande()->one()['sondaggi_id']
                    ]);
                } else {
                    $newRisposta                      = new SondaggiRispostePredefinite();
                    $newRisposta->sondaggi_domande_id = $risposta->sondaggi_domande_id;
                    $newRisposta->tipo_domanda        = $tipoDomanda;
                    return $this->render('/sondaggi-risposte-predefinite/create',
                            [
                            'model' => $newRisposta,
                    ]);
                }
            } else {
                return $this->render('create',
                        [
                        'model' => $this->model,
                        'url' => null
                ]);
            }
        }
    }

    /**
     * Updates an existing Sondaggi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $this->setUpLayout('form');
        $this->model = $this->findModel($id);
        $this->model->getOtherAttributes();
        if ($this->model->load(Yii::$app->request->post())) {
            $this->model->getOtherAttributes(Yii::$app->request->post());
            //inizio upload immagine
            $avatar_id = null;
            $modelFile = new FilemanagerMediafile();
            $modelFile->load(Yii::$app->request->post());
            $file      = UploadedFile::getInstance($modelFile, 'file');
            if ($file) {
                $routes = Yii::$app->getModule('upload')->routes;
                $modelFile->saveUploadedFile($routes, true);
                if ($modelFile->id) {
                    $avatar_id                             = $modelFile->id;
                    $this->model->filemanager_mediafile_id = $avatar_id;
                }
            }
            $this->model->pubblico         = \Yii::$app->request->post()['Sondaggi']['pubblico'];
            $this->model->tipologie_entita = \Yii::$app->request->post()['Sondaggi']['tipologie_entita'];
            if ($this->model->pubblico == 0) {
                if (\Yii::$app->request->post()['Sondaggi']['destinatari_pubblicazione'] != '') {
                    $this->model->save();
                    \lispa\amos\sondaggi\models\SondaggiPubblicazione::deleteAll(['sondaggi_id' => $id]);
                    if (count($this->model->tipologie_entita) > 0 && is_array($this->model->tipologie_entita)) {
                        foreach ($this->model->tipologie_entita as $TipologAtt) {
                            foreach (\Yii::$app->request->post()['Sondaggi']['destinatari_pubblicazione'] as $Destinatario) {
                                $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                                $destPubb->setOtherAttribute(\Yii::$app->request->post());
                                $destPubb->sondaggi_id      = $this->model->id;
                                $destPubb->ruolo            = $Destinatario;
                                $destPubb->tipologie_entita = $TipologAtt;
                                $destPubb->save();
                            }
                        }
                    } else {
                        foreach (\Yii::$app->request->post()['Sondaggi']['destinatari_pubblicazione'] as $Destinatario) {
                            $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                            $destPubb->setOtherAttribute(\Yii::$app->request->post());
                            $destPubb->sondaggi_id      = $this->model->id;
                            $destPubb->ruolo            = $Destinatario;
                            $destPubb->tipologie_entita = 0;
                            $destPubb->save();
                        }
                    }
                    Yii::$app->getSession()->addFlash('success',
                        AmosSondaggi::tHtml('amossondaggi', "Sondaggio aggiornato correttamente."));
                    return $this->redirect('index');
                } else {
                    $this->model->addError('destinatari_pubblicazione',
                        AmosSondaggi::t('amossondaggi', 'Destinatari Pubblicazione non può essere vuoto'));
                    return $this->render('update',
                            [
                            'model' => $this->model,
                            'public' => "false"
                    ]);
                }
            } else {
                //$attivita = (isset(\Yii::$app->request->post()['Sondaggi']['attivita_formativa']))? \Yii::$app->request->post()['Sondaggi']['attivita_formativa'] : 0;
                if (count($this->model->tipologie_entita) > 0 && is_array($this->model->tipologie_entita)) {
                    $this->model->save();
                    \lispa\amos\sondaggi\models\SondaggiPubblicazione::deleteAll(['sondaggi_id' => $id]);
                    foreach ($this->model->tipologie_entita as $TipologAtt) {
                        $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                        $destPubb->setOtherAttribute(\Yii::$app->request->post());
                        $destPubb->sondaggi_id      = $this->model->id;
                        //$destPubb->entita_id = $attivita;
                        $destPubb->ruolo            = 'PUBBLICO';
                        $destPubb->tipologie_entita = $TipologAtt;
                        $destPubb->save(FALSE);
                    }
                    Yii::$app->getSession()->addFlash('success',
                        AmosSondaggi::tHtml('amossondaggi', "Sondaggio aggiornato correttamente."));
                } else {
                    $this->model->save();
                    \lispa\amos\sondaggi\models\SondaggiPubblicazione::deleteAll(['sondaggi_id' => $id]);
                    $destPubb                   = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();
                    $destPubb->setOtherAttribute(\Yii::$app->request->post());
                    $destPubb->sondaggi_id      = $this->model->id;
                    //$destPubb->entita_id = $attivita;
                    $destPubb->ruolo            = 'PUBBLICO';
                    $destPubb->tipologie_entita = 0;
                    $destPubb->save(FALSE);
                    Yii::$app->getSession()->addFlash('success',
                        AmosSondaggi::tHtml('amossondaggi', "Sondaggio aggiornato correttamente."));
                }
                return $this->redirect('index');
            }
        } else {
            return $this->render('update', [
                    'model' => $this->model,
            ]);
        }
    }

    /**
     * Deletes an existing Sondaggi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return \yii\web\Response
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->model = $this->findModel($id);
        $pagine      = $this->model->getSondaggiDomandePagines()->count();
        $pubbl       = $this->model->sondaggi_stato_id;
        $pubblicato  = SondaggiStato::findOne(['stato' => 'BOZZA'])->id;
        if ($pagine) {
            Yii::$app->getSession()->addFlash('danger',
                AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare il sondaggio per la presenza di pagine."));
        } else {
            if ($pubblicato != $pubbl) {
                Yii::$app->getSession()->addFlash('danger',
                    AmosSondaggi::tHtml('amossondaggi',
                        "Impossibile cancellare il sondaggio in quanto non è in stato BOZZA."));
            } else {
                $this->model->delete();
                Yii::$app->getSession()->addFlash('success',
                    AmosSondaggi::tHtml('amossondaggi', "Sondaggio cancellato correttamente."));
            }
        }
        return $this->redirect('index');
    }
}