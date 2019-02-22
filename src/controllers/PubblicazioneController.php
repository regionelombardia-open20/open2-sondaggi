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
use lispa\amos\core\user\User;
use lispa\amos\sondaggi\AmosSondaggi;
use lispa\amos\sondaggi\models\GeneratoreSondaggio;
use lispa\amos\sondaggi\models\search\SondaggiSearch;
use lispa\amos\sondaggi\models\Sondaggi;
use lispa\amos\sondaggi\models\SondaggiDomande;
use lispa\amos\sondaggi\models\SondaggiRisposte;
use lispa\amos\sondaggi\models\SondaggiRisposteSessioni;
use lispa\amos\sondaggi\models\SondaggiStato;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class PubblicazioneController
 * PubblicazioneController implements the CRUD actions for Sondaggi model.
 * @package lispa\amos\sondaggi\controllers
 */
class PubblicazioneController extends CrudController
{
    public $base_dir;
    public $percorso_model;
    public $percorso_view;
    public $percorso_validator;
    public $alias_path;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config = []);
        if (!defined('DS'))
            define('DS', DIRECTORY_SEPARATOR);
        if (!isset($this->alias_path))
            $this->alias_path = Yii::getAlias('@backend');
        if (!isset($this->base_dir))
            $this->base_dir = "sondaggi" . DS . "pubblicazione";
        if (!isset($this->percorso_model))
            $this->percorso_model = "backend\\sondaggi\\pubblicazione\\models\\q";
        if (!isset($this->percorso_view))
            $this->percorso_view = "backend\\sondaggi\\pubblicazione\\views\\q";
        if (!isset($this->percorso_validator))
            $this->percorso_validator = "backend" . DS . "sondaggi" . DS . "validators" . DS;
    }

    /**
     * @var string $layout
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!defined('DS'))
            define('DS', DIRECTORY_SEPARATOR);
        $this->setModelObj(new Sondaggi());
        $this->setModelSearch(new SondaggiSearch());

        $this->setAvailableViews([
            'list' => [
                'name' => 'list',
                'label' => AmosIcons::show('view-list') . Html::tag('p', AmosSondaggi::tHtml('amossondaggi', 'Lista')),
                'url' => '?currentView=list'
            ],
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
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'compila',
                            'notifica',
                            'pubblica',
                            'pubblicazione',
                            'sondaggio-pubblico',
                            'sondaggi-pubblici',
                        ],
                        'roles' => ['@']
                    ]
                ]
            ]
        ]);
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Lists all PeiClassiUtenti models.
     * @param string|null $layout
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex($layout = null)
    {
        Url::remember();
        $this->setDataProvider($this->getModelSearch()->searchDominio(Yii::$app->request->getQueryParams()));
        return parent::actionIndex($layout);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionCreate()
    {
        return $this->redirect('/sondaggi/sondaggi/create');
    }

    /**
     * Lists all PeiClassiUtenti models.
     * @return string
     */
    public function actionPubblicazione()
    {
        $this->setUpLayout('list');
        Url::remember();
        $this->setDataProvider($this->getModelSearch()->search(Yii::$app->request->getQueryParams()));
        return $this->render('pubblicazione', [
            'dataProvider' => $this->getDataProvider(),
            'currentView' => $this->getCurrentView()
        ]);
    }

    /**
     * Displays a single PeiClassiUtenti model.
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
     * Genera i models e le view del sondaggio che si sta pubblicando
     * @param integer $idSondaggio L'id del sondaggi da pubblicare
     * @param string|null $url
     * @return \yii\web\Response
     */
    public function actionPubblica($idSondaggio, $url = null)
    {
        $dir_models = $this->alias_path . DS . $this->base_dir . DS . "models" . DS . "q" . $idSondaggio;
        $dir_views = $this->alias_path . DS . $this->base_dir . DS . "views" . DS . "q" . $idSondaggio;

        if (!is_dir($dir_models)) {
            mkdir($dir_models, 0777, TRUE);
        }
        if (!is_dir($dir_views)) {
            mkdir($dir_views, 0777, TRUE);
        }
        $sondaggio = Sondaggi::findOne(['id' => $idSondaggio]);
        $idValidato = SondaggiStato::findOne(['stato' => 'VALIDATO'])->id;
        $pagine = $sondaggio->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
        $num_pagine = $pagine->count();
        $np = 1;
        $generatore = new GeneratoreSondaggio();
        foreach ($pagine->all() as $pagina) {
            $generatore->creaValidator($this->percorso_validator, $pagina['id']);
            $generatore->creaView("backend" . DS . $this->base_dir . DS . "views" . DS . "q" . $idSondaggio, $pagina['id'], $this->percorso_view . $idSondaggio);
            $generatore->creaModel("backend" . DS . $this->base_dir . DS . "models" . DS . "q" . $idSondaggio, $pagina['id'], $this->percorso_validator, $this->percorso_model . $idSondaggio);
        }
        $sondaggio->sondaggi_stato_id = $idValidato;
        $sondaggio->save();
        if ($url) {
            Yii::$app->getSession()->addFlash('success', AmosSondaggi::tHtml('amossondaggi', 'Sondaggio pubblicato correttamente.'));
            return $this->redirect($url);
        } else {
            Yii::$app->getSession()->addFlash('success', AmosSondaggi::tHtml('amossondaggi', 'Sondaggio pubblicato correttamente.'));
            return $this->redirect('index');
        }
    }

    /**
     * Send notification e-mail to user by role
     * @param int $idSondaggio
     * @param string|null $url
     * @return \yii\web\Response
     */
    public function actionNotifica($idSondaggio, $url = null)
    {
        $sondaggio = Sondaggi::findOne(['id' => $idSondaggio]);
        $pubblicazione = $sondaggio->getSondaggiPubblicaziones();
        $subject = $pubblicazione->one()->mail_subject;
        $message = $pubblicazione->one()->mail_message;
        $module = \Yii::$app->controller->module;
        $email = \Yii::$app->getModule('email');
        if (!empty($module) && isset($module->enableNotificationEmailByRoles) && $module->enableNotificationEmailByRoles == true && !empty($email) && !empty($module->defaultEmailSender)) {
            foreach ($pubblicazione->all() as $key => $value) {
                $role = $value->ruolo;
                $users = User::find();
                foreach ($users->all() as $user) {
                    if (!empty(trim($user->email)) && \Yii::$app->authManager->checkAccess($user->id, $role)) {
                        $email->queue($module->defaultEmailSender, $user->email, $subject, $message);
                    }
                }
            }
        }
        if ($url) {
            Yii::$app->getSession()->addFlash('success', AmosSondaggi::tHtml('amossondaggi', 'Notifiche aggiunte alla coda di invio correttamente.'));
            return $this->redirect($url);
        } else {
            Yii::$app->getSession()->addFlash('success', AmosSondaggi::tHtml('amossondaggi', 'Notifiche aggiunte alla coda di invio correttamente.'));
            return $this->redirect('index');
        }
    }

    /**
     * Action che permette di compilare il sondaggio
     * @param int $id Id del sondaggio da compilare
     * @param int|null $idPagina
     * @param int|null $utente User ID
     * @param null $idSessione
     * @param null $accesso
     * @param null $url
     * @return string
     */
    public function actionCompila($id, $idPagina = null, $utente = null, $idSessione = null, $accesso = null, $url = null)
    {
        $this->setUpLayout('main');
        if (!$utente) {
            $utente = Yii::$app->getUser()->getId();
        }
        $this->model = Sondaggi::findOne(['id' => $id]);
        $pagine = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
        $primaPagina = $pagine->all()[0]['id'];
        $ultimaPagina = $pagine->all()[$pagine->count() - 1]['id'];
        $prossimaPagina = null;
        $arrayPag = [];
        $completato = false;
        foreach ($pagine->all() as $Pag) {
            $arrayPag[] = $Pag['id'];
        }
        if ($idPagina) {
            if ($idPagina != $ultimaPagina) {
                $idPag = array_search($idPagina, $arrayPag);
                $prossimaPagina = $arrayPag[$idPag + 1];
            }
        } else {
            $idPagina = $primaPagina;
            $idPag = array_search($primaPagina, $arrayPag);
            $prossimaPagina = (isset($arrayPag[$idPag + 1])) ? $arrayPag[$idPag + 1] : 0;
        }
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $idPagina = $data['idPagina'];
            if ($idPagina != $ultimaPagina) {
                $idPag = array_search($idPagina, $arrayPag);
                $prossimaPagina = $arrayPag[$idPag + 1];
            } else {
                $completato = true;
            }
            $utente = $data['utente'];
            $idSessione = $data['idSessione'];
            $percorso = $this->percorso_model . $id . "\\Pagina_" . $idPagina;
            $percorsoNew = $this->percorso_model . $id . "\\Pagina_" . $prossimaPagina;
            $newModel = new $percorso;
            if ($newModel->load($data) && $newModel->validate()) {
                $newModel->save($idSessione, $accesso, $completato);
                if ($completato) {
                    return $this->render('/pubblicazione/compilato', ['url' => $url, 'pubblicazioni' => $this->model->getSondaggiPubblicaziones()]);
                } else {
                    $prossimoModel = new $percorsoNew;
                    return $this->render('/pubblicazione/compila', ['model' => $prossimoModel, 'idSessione' => $idSessione, 'idPagina' => $prossimaPagina, 'utente' => $utente, 'id' => $id]);
                }
            } else {
                return $this->render('/pubblicazione/compila', ['model' => $newModel, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'utente' => $utente, 'id' => $id]);
            }
        } else {
            $inCorso = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_id' => $id])->andWhere(['user_id' => $utente]);
            if ($inCorso->count() == 0) {
                $idSondaggio = $id;
                $sessione = new SondaggiRisposteSessioni();
                $sessione->begin_date = date('Y-m-d H:i:s');
                $sessione->end_date = null;
                $sessione->sondaggi_id = $id;
                $sessione->user_id = $utente;
                $sessione->save();
                $idSessione = $sessione->id;
                $modelloPagina = $this->percorso_model . $id . "\\Pagina_" . $primaPagina;
                $pagina = new $modelloPagina;
                return $this->render('/pubblicazione/compila', ['model' => $pagina, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'utente' => $utente, 'id' => $id]);
            } else {
                $nonCompletato = 0;
                foreach ($inCorso->all() as $InCorso) {
                    if ($InCorso['completato'] == 0) {
                        $nonCompletato = $InCorso['id'];
                    }
                }
                if ($nonCompletato) {
                    $sessione = SondaggiRisposteSessioni::findOne(['id' => $nonCompletato]);
                    $risposte = $sessione->getSondaggiRispostes();
                    if ($risposte->count() > 0) {
                        //se esistono risposte date al sondaggio
                        $arrDomande = [];
                        foreach ($risposte->all() as $risposta) {
                            $arrDomande[] = $risposta['sondaggi_domande_id'];
                        }
                        $domande = SondaggiDomande::find()->andWhere(['IN', 'id', $arrDomande])->orderBy('ordinamento ASC');
                        $idPagina = $domande->all()[$domande->count() - 1]['sondaggi_domande_pagine_id'];
                        if ($idPagina != $ultimaPagina) {
                            $idPag = array_search($idPagina, $arrayPag);
                            $prossimaPagina = $arrayPag[$idPag + 1];
                        }
                        $percorso = $this->percorso_model . $id . "\\Pagina_" . $idPagina;
                        $newModel = new $percorso;
                        $tutteDomande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $idPagina]);
                        foreach ($tutteDomande->all() as $precompilaRisposte) {
                            $rispostaDomanda = SondaggiRisposte::find()->andWhere(['sondaggi_domande_id' => $precompilaRisposte['id']])->andWhere(['sondaggi_risposte_sessioni_id' => $nonCompletato]);
                            if ($rispostaDomanda->count() == 1) {
                                if ($rispostaDomanda->one()['risposta_libera'] != null) {
                                    $idDom = "domanda_" . $precompilaRisposte['id'];
                                    $newModel->$idDom = $rispostaDomanda->one()['risposta_libera'];
                                } else {
                                    $idDom = "domanda_" . $precompilaRisposte['id'];
                                    $newModel->$idDom = $rispostaDomanda->one()['sondaggi_risposte_predefinite_id'];
                                }
                            } else if ($rispostaDomanda->count() > 1) {
                                $arrRisposte = [];
                                foreach ($rispostaDomanda->all() as $risposteSingole) {
                                    $arrRisposte[] = $risposteSingole['sondaggi_risposte_predefinite_id'];
                                }
                                $idDom = "domanda_" . $precompilaRisposte['id'];
                                $newModel->$idDom = $arrRisposte;
                            }
                        }
                        return $this->render('/pubblicazione/compila', ['model' => $newModel, 'idPagina' => $idPagina, 'idSessione' => $nonCompletato, 'id' => $id, 'utente' => $utente]);
                    } else {//se non esistono risposte date al sondaggio
                        $percorso = ($this->percorso_model . $id . "\\Pagina_" . $primaPagina);
                        $newModel = new $percorso;
                        return $this->render('/pubblicazione/compila', ['model' => $newModel, 'idPagina' => $primaPagina, 'idSessione' => $nonCompletato, 'id' => $id, 'utente' => $utente]);
                    }
                } else {//Se non esiste un sondaggio incompleto da completare                     
                    if ($inCorso->count() < $this->model->compilazioni_disponibili || $this->model->compilazioni_disponibili == 0) {
                        $idSondaggio = $id;
                        $sessione = new SondaggiRisposteSessioni();
                        $sessione->begin_date = date('Y-m-d H:i:s');
                        $sessione->end_date = null;
                        $sessione->sondaggi_id = $id;
                        $sessione->user_id = $utente;
                        $sessione->save();
                        $idSessione = $sessione->id;
                        $modelloPagina = $this->percorso_model . $id . "\\Pagina_" . $primaPagina;
                        $pagina = new $modelloPagina;
                        return $this->render('/pubblicazione/compila', ['model' => $pagina, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'utente' => $utente, 'id' => $id]);
                    } else {
                        return $this->render('/pubblicazione/non_compilabile', ['url' => $url, 'pubblicazioni' => $this->model->getSondaggiPubblicaziones()]);
                    }
                }
            }
        }
    }

    /**
     * Action che permette di compilare il sondaggio di accesso
     * @param integer $id Id dell'accesso
     *//* TODO TODO TODO TODO TODO TODO TODO TODO TODO
      public function actionCompilaSondaggioAccesso($id, $url = null) {
      $sessioni = SondaggiRisposteSessioni::find()->andWhere(['pei_accessi_servizi_facilitazione_id' => $id])->andWhere(['completato' => 0]);
      $idSessione = $sessioni->one()['id'];
      if ($sessioni->count() > 0) {
      $idSondaggio = $sessioni->one()['sondaggi_id'];
      $this->model = Sondaggi::findOne(['id' => $idSondaggio]);
      $pagine = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
      $primaPagina = $pagine->all()[0]['id'];
      $ultimaPagina = $pagine->all()[$pagine->count() - 1]['id'];
      $prossimaPagina = null;
      $arrayPag = [];
      $completato = false;
      $precompilato = PeiAccessiServiziFacilitazioneConfigurazioneSondaggi::findOne(['sondaggi_id' => $idSondaggio])->precompilato;
      $vecchiQuest = SondaggiRisposteSessioni::find()->andWhere(['user_id' => $sessioni->one()['user_id']])->andWhere(['sondaggi_id' => $idSondaggio])->orderBy('updated_at, id DESC');
      $verifica = ($vecchiQuest->count() > 0) ? 1 : 0;

      foreach ($pagine->all() as $Pag) {
      $arrayPag[] = $Pag['id'];
      }
      if (Yii::$app->request->isPost) {
      $data = Yii::$app->request->post();
      $idPagina = $data['idPagina'];
      if ($idPagina != $ultimaPagina) {
      $IdPag = array_search($idPagina, $arrayPag);
      $prossimaPagina = $arrayPag[$IdPag + 1];
      } else {
      $completato = true;
      }
      $utente = $data['utente'];
      $idSessione = $data['idSessione'];
      $percorso = $this->percorso_model . $idSondaggio . "\\Pagina_" . $idPagina;
      $percorsoNew = $this->percorso_model . $idSondaggio . "\\Pagina_" . $prossimaPagina;
      $newModel = new $percorso;
      if ($newModel->load($data) && $newModel->validate()) {
      $newModel->save($idSessione, $id, $completato);
      if ($completato) {
      $url = Yii::$app->urlManager->createUrl([
      '/puntopei/pei-accessi-servizi-facilitazione/update',
      'id' => $id,
      'verifica' => TRUE
      ]);
      return $this->redirect($url);
      } else {
      $ProssimoModel = new $percorsoNew;
      if ($verifica && $precompilato) {
      $vecchioSondaggio = $vecchiQuest->one()['id'];
      $TutteDomande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $prossimaPagina]);
      foreach ($TutteDomande->all() as $precompilaRisposte) {
      $RispostaDomanda = SondaggiRisposte::find()->andWhere(['sondaggi_domande_id' => $precompilaRisposte['id']])->andWhere(['sondaggi_risposte_sessioni_id' => $vecchioSondaggio]);
      if ($RispostaDomanda->count() == 1) {
      if ($RispostaDomanda->one()['risposta_libera'] != null) {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $ProssimoModel->$idDom = $RispostaDomanda->one()['risposta_libera'];
      } else {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $ProssimoModel->$idDom = $RispostaDomanda->one()['sondaggi_risposte_predefinite_id'];
      }
      } else if ($RispostaDomanda->count() > 1) {
      $arrRisposte = [];
      foreach ($RispostaDomanda->all() as $RisposteSingole) {
      $arrRisposte[] = $RisposteSingole['sondaggi_risposte_predefinite_id'];
      }
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $ProssimoModel->$idDom = $arrRisposte;
      }
      }
      }
      return $this->render('/pubblicazione/compila_sondaggio_accesso', ['model' => $ProssimoModel, 'idSessione' => $idSessione, 'idPagina' => $prossimaPagina, 'utente' => $utente, 'id' => $idSondaggio, 'idAccesso' => $id]);
      }
      } else {
      return $this->render('/pubblicazione/compila_sondaggio_accesso', ['model' => $newModel, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'utente' => $utente, 'id' => $idSondaggio, 'idAccesso' => $id]);
      }
      } else {
      $inCorso = SondaggiRisposteSessioni::findOne(['id' => $idSessione]);

      $risposte = $inCorso->getSondaggiRispostes();
      if ($risposte->count() > 0) {
      //se esistono risposte date al sondaggio
      $arrDomande = [];
      foreach ($risposte->all() as $Risposta) {
      $arrDomande[] = $Risposta['sondaggi_domande_id'];
      }
      $domande = SondaggiDomande::find()->andWhere(['IN', 'id', $arrDomande])->orderBy('ordinamento ASC');
      $idPagina = $domande->all()[$domande->count() - 1]['sondaggi_domande_pagine_id'];
      if ($idPagina != $ultimaPagina) {
      $IdPag = array_search($idPagina, $arrayPag);
      $prossimaPagina = $arrayPag[$IdPag + 1];
      }
      $percorso = $this->percorso_model . $idSondaggio . "\\Pagina_" . $idPagina;
      $newModel = new $percorso;
      $TutteDomande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $idPagina]);
      foreach ($TutteDomande->all() as $precompilaRisposte) {
      $RispostaDomanda = SondaggiRisposte::find()->andWhere(['sondaggi_domande_id' => $precompilaRisposte['id']])->andWhere(['sondaggi_risposte_sessioni_id' => $idSessione]);
      if ($RispostaDomanda->count() == 1) {
      if ($RispostaDomanda->one()['risposta_libera'] != null) {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $RispostaDomanda->one()['risposta_libera'];
      } else {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $RispostaDomanda->one()['sondaggi_risposte_predefinite_id'];
      }
      } else if ($RispostaDomanda->count() > 1) {
      $arrRisposte = [];
      foreach ($RispostaDomanda->all() as $RisposteSingole) {
      $arrRisposte[] = $RisposteSingole['sondaggi_risposte_predefinite_id'];
      }
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $arrRisposte;
      }
      }
      return $this->render('/pubblicazione/compila_sondaggio_accesso', ['model' => $newModel, 'idPagina' => $idPagina, 'idSessione' => $idSessione, 'id' => $idSondaggio, 'utente' => $inCorso->user_id, 'idAccesso' => $id]);
      } else {//se non esistono risposte date al sondaggio
      $percorso = $this->percorso_model . $idSondaggio . "\\Pagina_" . $primaPagina;
      $newModel = new $percorso;
      if ($verifica && $precompilato) {
      $vecchioSondaggio = $vecchiQuest->one()['id'];
      $TutteDomande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $primaPagina]);
      foreach ($TutteDomande->all() as $precompilaRisposte) {
      $RispostaDomanda = SondaggiRisposte::find()->andWhere(['sondaggi_domande_id' => $precompilaRisposte['id']])->andWhere(['sondaggi_risposte_sessioni_id' => $vecchioSondaggio]);
      if ($RispostaDomanda->count() == 1) {
      if ($RispostaDomanda->one()['risposta_libera'] != null) {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $RispostaDomanda->one()['risposta_libera'];
      } else {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $RispostaDomanda->one()['sondaggi_risposte_predefinite_id'];
      }
      } else if ($RispostaDomanda->count() > 1) {
      $arrRisposte = [];
      foreach ($RispostaDomanda->all() as $RisposteSingole) {
      $arrRisposte[] = $RisposteSingole['sondaggi_risposte_predefinite_id'];
      }
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $arrRisposte;
      }
      }
      }
      return $this->render('/pubblicazione/compila_sondaggio_accesso', ['model' => $newModel, 'idPagina' => $primaPagina, 'idSessione' => $idSessione, 'id' => $idSondaggio, 'utente' => $inCorso->user_id, 'idAccesso' => $id]);
      }
      }
      } else {
      $url = Yii::$app->urlManager->createUrl([
      '/puntopei/pei-accessi-servizi-facilitazione/update',
      'id' => $id,
      'verifica' => TRUE
      ]);
      return $this->redirect($url);
      }
      }

      public function actionSondaggioPubblicoAttivita() {
      $this->layout = '@vendor/lispa/amos/core/views/layouts/sondaggio';
      $this->model = new \lispa\amos\sondaggi\models\SondaggiPubblicazione();

      if (\Yii::$app->request->isPost) {
      if (isset(\Yii::$app->request->post()['SondaggiPubblicazione']['attivita'])) {
      $attivita = \Yii::$app->request->post()['SondaggiPubblicazione']['attivita'];
      $verificaAtt = \backend\modules\attivitaformative\models\PeiAttivitaFormative::findOne(['codice_attivita' => $attivita]);
      if (count($verificaAtt) == 1) {
      $idTipologia = $verificaAtt->getAreaFormativa()->one()['id'];
      $verificaSondaggio = \lispa\amos\sondaggi\models\SondaggiPubblicazione::find()->leftJoin(Sondaggi::tableName(), 'id = sondaggi_id')->andWhere(['tipologie_attivita' => $idTipologia])->andWhere(['sondaggi_stato_id' => 3])->andWhere(['ruolo' => 'PUBBLICO'])->orderBy('sondaggi_id DESC');
      if ($verificaSondaggio->count() > 0) {
      $sondaggio = $verificaSondaggio->one()['sondaggi_id'];
      return $this->render('sondaggio_pubblico_attivita', [
      'model' => $this->model,
      'id' => $sondaggio,
      'attivita' => $attivita,
      ]);
      }
      }
      Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', 'E\' necessario inserire un codice attività valido per poter compilare il sondaggio.'));
      } else {
      Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', 'E\' necessario inserire il codice dell\'attività svolta per poter compilare il sondaggio.'));
      }
      }
      return $this->render('sondaggio_pubblico_attivita', [
      'model' => $this->model
      ]);
      } */

    /**
     * Action che permette di compilare il sondaggio pubblico
     * @param integer $id Id del sondaggio da compilare
     * @param null $idPagina
     * @param null $idSessione
     * @param null $accesso
     * @param null $url
     * @param null $attivita
     * @param bool $inizio
     * @param bool $libero
     * @return string|\yii\web\Response
     */
    public function actionSondaggioPubblico($id, $idPagina = null, $idSessione = null, $accesso = null, $url = null, $attivita = null, $inizio = FALSE, $libero = FALSE)
    {
        $this->layout = '@vendor/lispa/amos/core/views/layouts/sondaggio';

        if ($libero && $id) {
            $verificaSondaggio = \lispa\amos\sondaggi\models\SondaggiPubblicazione::find()->innerJoin(Sondaggi::tableName(), 'id = sondaggi_id')->andWhere(['id' => $id])->andWhere(['tipologie_attivita' => 0])->andWhere(['sondaggi_stato_id' => 3])->andWhere(['ruolo' => 'PUBBLICO']);
            if ($verificaSondaggio->count() == 1) {
                $idAttivita = null;
                $this->model = Sondaggi::findOne(['id' => $id]);
                $pagine = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
                $primaPagina = $pagine->all()[0]['id'];
                $ultimaPagina = $pagine->all()[$pagine->count() - 1]['id'];
                $prossimaPagina = null;
                $arrayPag = [];
                $completato = false;
                foreach ($pagine->all() as $Pag) {
                    $arrayPag[] = $Pag['id'];
                }
                if ($idPagina) {
                    if ($idPagina != $ultimaPagina) {
                        $idPag = array_search($idPagina, $arrayPag);
                        $prossimaPagina = $arrayPag[$idPag + 1];
                    }
                } else {
                    $idPagina = $primaPagina;
                    $idPag = array_search($primaPagina, $arrayPag);
                    $prossimaPagina = (isset($arrayPag[$idPag + 1])) ? $arrayPag[$idPag + 1] : 0;
                }
                if (Yii::$app->request->isPost && !$inizio) {
                    $data = Yii::$app->request->post();
                    $idPagina = $data['idPagina'];
                    if ($idPagina != $ultimaPagina) {
                        $idPag = array_search($idPagina, $arrayPag);
                        $prossimaPagina = $arrayPag[$idPag + 1];
                    } else {
                        $completato = true;
                    }

                    $idSessione = $data['idSessione'];
                    $percorso = $this->percorso_model . $id . "\\Pagina_" . $idPagina;
                    $percorsoNew = $this->percorso_model . $id . "\\Pagina_" . $prossimaPagina;
                    $newModel = new $percorso;
                    if ($newModel->load($data) && $newModel->validate()) {
                        $newModel->save($idSessione, $accesso, $completato);
                        if ($completato) {
                            return $this->render('/pubblicazione/sondaggio_pubblico_completato', ['url' => $url]);
                        } else {
                            $prossimoModel = new $percorsoNew;
                            return $this->render('/pubblicazione/sondaggio_pubblico', ['model' => $prossimoModel, 'idSessione' => $idSessione, 'idPagina' => $prossimaPagina, 'id' => $id, 'attivita' => $attivita, 'inizio' => FALSE, 'libero' => TRUE]);
                        }
                    } else {
                        return $this->render('/pubblicazione/sondaggio_pubblico', ['model' => $newModel, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'id' => $id, 'attivita' => $attivita, 'inizio' => FALSE, 'libero' => TRUE]);
                    }
                } else {
                    // $inCorso = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_id' => $id]);
                    // if ($inCorso->count() == 0) {
                    $idSondaggio = $id;
                    $sessione = new SondaggiRisposteSessioni();
                    $sessione->begin_date = date('Y-m-d H:i:s');
                    $sessione->end_date = null;
                    $sessione->sondaggi_id = $id;
                    $sessione->pei_attivita_formative_id = $idAttivita;
                    $sessione->save();
                    $idSessione = $sessione->id;
                    $modelloPagina = $this->percorso_model . $id . "\\Pagina_" . $primaPagina;
                    $pagina = new $modelloPagina;
                    return $this->render('/pubblicazione/sondaggio_pubblico', ['model' => $pagina, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'id' => $id, 'attivita' => $attivita, 'inizio' => FALSE, 'libero' => TRUE]);
                }
            } else {
                return $this->redirect('sondaggi-pubblici');
            }
        } else if (!$attivita || !$id) {
            return $this->redirect('sondaggio-pubblico-attivita');
        } else {
            $modelAttivita = \backend\modules\attivitaformative\models\PeiAttivitaFormative::findOne(['codice_attivita' => $attivita]);
            $idAttivita = $modelAttivita->id;
            $tipologieAttivita = $modelAttivita->getTags()->andWhere(['lvl' => 1])->andWhere(['root' => 1])->one()['id'];
            $verificaSondaggio = \lispa\amos\sondaggi\models\SondaggiPubblicazione::find()->innerJoin(Sondaggi::tableName(), 'id = sondaggi_id')->andWhere(['id' => $id])->andWhere(['tipologie_attivita' => $tipologieAttivita])->andWhere(['sondaggi_stato_id' => 3])->andWhere(['ruolo' => 'PUBBLICO']);
            //pr($verificaSondaggio->createCommand()->rawSql);pr($verificaSondaggio->count(), 'count');die;
            if ($verificaSondaggio->count() == 1) {
                $this->model = Sondaggi::findOne(['id' => $id]);
                $pagine = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
                $primaPagina = $pagine->all()[0]['id'];
                $ultimaPagina = $pagine->all()[$pagine->count() - 1]['id'];
                $prossimaPagina = null;
                $arrayPag = [];
                $completato = false;
                foreach ($pagine->all() as $Pag) {
                    $arrayPag[] = $Pag['id'];
                }
                if ($idPagina) {
                    if ($idPagina != $ultimaPagina) {
                        $idPag = array_search($idPagina, $arrayPag);
                        $prossimaPagina = $arrayPag[$idPag + 1];
                    }
                } else {
                    $idPagina = $primaPagina;
                    $idPag = array_search($primaPagina, $arrayPag);
                    $prossimaPagina = (isset($arrayPag[$idPag + 1])) ? $arrayPag[$idPag + 1] : 0;
                }
                if (Yii::$app->request->isPost && !$inizio) {
                    $data = Yii::$app->request->post();
                    $idPagina = $data['idPagina'];
                    if ($idPagina != $ultimaPagina) {
                        $idPag = array_search($idPagina, $arrayPag);
                        $prossimaPagina = $arrayPag[$idPag + 1];
                    } else {
                        $completato = true;
                    }

                    $idSessione = $data['idSessione'];
                    $percorso = $this->percorso_model . $id . "\\Pagina_" . $idPagina;
                    $percorsoNew = $this->percorso_model . $id . "\\Pagina_" . $prossimaPagina;
                    $newModel = new $percorso;
                    if ($newModel->load($data) && $newModel->validate()) {
                        $newModel->save($idSessione, $accesso, $completato);
                        if ($completato) {
                            return $this->render('/pubblicazione/sondaggio_pubblico_compilato', ['url' => $url, 'pubblicazioni' => $this->model->getSondaggiPubblicaziones()]);
                        } else {
                            $prossimoModel = new $percorsoNew;
                            return $this->render('/pubblicazione/sondaggio_pubblico', ['model' => $prossimoModel, 'idSessione' => $idSessione, 'idPagina' => $prossimaPagina, 'id' => $id, 'attivita' => $attivita, 'inizio' => FALSE, 'libero' => FALSE]);
                        }
                    } else {
                        return $this->render('/pubblicazione/sondaggio_pubblico', ['model' => $newModel, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'id' => $id, 'attivita' => $attivita, 'inizio' => FALSE, 'libero' => FALSE]);
                    }
                } else {
                    // $inCorso = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_id' => $id]);
                    // if ($inCorso->count() == 0) {
                    $idSondaggio = $id;
                    $sessione = new SondaggiRisposteSessioni();
                    $sessione->begin_date = date('Y-m-d H:i:s');
                    $sessione->end_date = null;
                    $sessione->sondaggi_id = $id;
                    $sessione->pei_attivita_formative_id = $idAttivita;
                    $sessione->save();
                    $idSessione = $sessione->id;
                    $modelloPagina = $this->percorso_model . $id . "\\Pagina_" . $primaPagina;
                    $pagina = new $modelloPagina;
                    return $this->render('/pubblicazione/sondaggio_pubblico', ['model' => $pagina, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'id' => $id, 'attivita' => $attivita, 'inizio' => FALSE, 'libero' => FALSE]);
                }
            } else {
                return $this->redirect('sondaggio-pubblico-attivita');
            }
        }
    }

    /**
     * @param int $id
     * @return string
     */
    public function actionSondaggiPubblici($id = 0)
    {
        $this->layout = '@vendor/lispa/amos/core/views/layouts/sondaggio';
        $models = \lispa\amos\sondaggi\models\SondaggiPubblicazione::find()->innerJoin(Sondaggi::tableName(), 'id = sondaggi_id')->andWhere(['tipologie_attivita' => 0])->andWhere(['sondaggi_stato_id' => 3])->andWhere(['ruolo' => 'PUBBLICO'])->select('sondaggi_id as id');
        $this->model = Sondaggi::find()->andWhere(['IN', 'id', $models])->orderBy('titolo ASC');

        if ($id) {
            $verifica = \lispa\amos\sondaggi\models\SondaggiPubblicazione::find()->innerJoin(Sondaggi::tableName(), 'id = sondaggi_id')->andWhere(['id' => $id])->andWhere(['tipologie_attivita' => 0])->andWhere(['sondaggi_stato_id' => 3])->andWhere(['ruolo' => 'PUBBLICO']);
            if ($verifica->count() == 1) {
                return $this->render('sondaggio_pubblico', [
                    'id' => $id,
                    'libero' => TRUE,
                ]);
            }
        }
        return $this->render('sondaggi_pubblici', [
            'model' => $this->model
        ]);
    }
}
