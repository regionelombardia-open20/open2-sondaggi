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

/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 29/05/2019
 * Time: 16:24
 */

namespace open20\amos\sondaggi\utility;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use open20\amos\core\exceptions\AmosException;
use open20\amos\core\helpers\StringHelper;
use open20\amos\core\utilities\Email;
use open20\amos\cwh\models\CwhConfig;
use open20\amos\cwh\models\CwhConfigContents;
use open20\amos\cwh\models\CwhPubblicazioni;
use open20\amos\cwh\models\CwhPubblicazioniCwhNodiEditoriMm;
use open20\amos\cwh\models\CwhRegolePubblicazione;
use open20\amos\organizzazioni\models\Profilo;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\controllers\PubblicazioneController;
use open20\amos\sondaggi\models\GeneratoreSondaggio;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiDomande;
use open20\amos\sondaggi\models\SondaggiDomandeCondizionate;
use open20\amos\sondaggi\models\SondaggiDomandePagine;
use open20\amos\sondaggi\models\SondaggiRisposte;
use open20\amos\sondaggi\models\SondaggiRispostePredefinite;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;
use open20\amos\core\user\User;
use PHPExcel_Exception;
use PHPExcel_Reader_Exception;
use PHPExcel_Writer_Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseInflector;
use yii\log\Logger;

class SondaggiUtility {

    const LOGIC_OPERATOR = [
        '==' => 'Uguale a',
        '>' => 'Maggiore di',
        '<' => 'Minore di',
        '<=' => 'Minore uguale di',
        '>=' => 'Maggiore uguale di',
    ];
    const RULES_GENERATOR = [
        1 => 'open20\\amos\\sondaggi\\validators\\CheckSum',
    ];
    const RULES = [
        1 => 'Somma dei campi',
    ];
    const MESSAGE_RULE = [
        1 => 'La somma degli elementi deve essere uguale a {value}',
    ];

    /**
     * @param $to
     * @param $profile
     * @param $subject
     * @param $message
     * @param array $files
     * @return bool
     */
    public static function sendEmailGeneral($to, $profile, $subject, $message, $files = [], $from = null, $titolo) {
        if(AmosSondaggi::instance()->stopEmail){
            return true;
        }
        try {
            if (empty($from)) {
                $from = '';
                if (isset(\Yii::$app->params['email-assistenza'])) {
//use default platform email assistance
                    $from = \Yii::$app->params['email-assistenza'];
                }
            }

            if (empty($subject) && strpos($message, 'nessun valore')) {
                $subject = 'Utente guest ha compilato il sondaggio "' . $titolo . '"';
                $message = 'Un utente guest ha compilato il sondaggio "' . $titolo . '", in allegato trovi il sondaggio compilato.';
            }

            /** @var \open20\amos\core\utilities\Email $email */
            $email = new Email();
            $email->sendMail($from, $to, $subject, $message, $files, [], ['profile' => $profile]);
        } catch (\Exception $ex) {
            pr($ex->getMessage());
            \Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return true;
    }

    /**
     * @param $model Sondaggi
     * @param int $request_info
     * @param string $path
     */
    public static function sendEmailSondaggioCompilato($model, $idSessione, $path = null, $utente = null,
            $dati_utente = null) {
        $sondaggiModule = AmosSondaggi::instance();
        $risposteSessioni = SondaggiRisposteSessioni::findOne($idSessione);
        $userDefault = null;
        $users = [];
        if (!empty($risposteSessioni->user) && $model->send_pdf_to_compiler) {
            $users[] = $risposteSessioni->user;
        }

        $additionalEmails = [];
        if (!empty($model->additional_emails) && $model->send_pdf_via_email) {
            $additionalEmails = explode(';', $model->additional_emails);
        }

        $compilatore = !empty($users[0]) ? $users[0] : null;

        if (!empty($utente)) {
            $dati_utente = [
                'nome' => $utente->nome,
                'cognome' => $utente->cognome
            ];
        }
        $data = [];
        if (!empty($compilatore)) {
            $nomeCognome = $compilatore->userProfile->nomeCognome;
        } else if (!empty($dati_utente)) {
            $nomeCognome = $dati_utente['nome'] . ' ' . $dati_utente['cognome'];
        } else {
            $nomeCognome = AmosSondaggi::t('amossondaggi', 'Utente Anonimo');
        }
        if (!empty($compilatore))
            $data = ['titolo' => $model->titolo, 'nomeCognome' => !empty($nomeCognome) ? $nomeCognome : 'Utente anonimo'];
        else if (!empty($dati_utente))
            $data = ['titolo' => $model->titolo, 'nomeCognome' => !empty($dati_utente['nome']) ? ($dati_utente['nome'] . ' ' . $dati_utente['cognome']) : AmosSondaggi::t('amossondaggi', 'Utente Anonimo')];

        if ($model->send_pdf_to_compiler && !empty(strip_tags($model->mail_send_pdf_guest))) {
            $htmlMessage = $model->mail_send_pdf_guest;
            if (!empty($compilatore)) {
                $htmlMessage = str_replace('{{{nome}}}', $compilatore->userProfile->nome, $htmlMessage);
                $htmlMessage = str_replace('{{{NOME}}}', $compilatore->userProfile->nome, $htmlMessage);
                $htmlMessage = str_replace('{{{cognome}}}', $compilatore->userProfile->cognome, $htmlMessage);
                $htmlMessage = str_replace('{{{COGNOME}}}', $compilatore->userProfile->cognome, $htmlMessage);
            }

            $message = $htmlMessage;
        } else {
            $message = "<p>" . AmosSondaggi::t('amossondaggi',
                    'Grazie per aver compilato il sondaggio <strong>{titolo}</strong>, in allegato trovi il sondaggio compilato.', $data) . "</p>";
        }

        if (!$sondaggiModule->enableAnonimousPoll) {

            if ($model->send_pdf_to_compiler && !empty($model->mail_subject_send_pdf_guest)) {
                $subject = $model->mail_subject_send_pdf_guest;
            } else {
                $subject = AmosSondaggi::t("amossondaggi", "{nomeCognome} ha compilato il sondaggio '$model->titolo'", $data);
            }

        } else {
            $subject = AmosSondaggi::t("amossondaggi", "Utente Anonimo ha compilato il sondaggio '$model->titolo'", $data);
        }

        if (empty($path)) {
            $files = [];
        } else {
            $files = [$path];
        }

        foreach ($users as $user) {
            if (!in_array($user->email, $additionalEmails)) {
                SondaggiUtility::sendEmailGeneral([$user->email], $user, $subject, $message, $files, null, $model->titolo);
            }
        }

        if (!empty($dati_utente) && !empty($dati_utente['email'])) {
            if (!in_array($dati_utente['email'], $additionalEmails)) {
                SondaggiUtility::sendEmailGeneral([$dati_utente['email']], $user, $subject, $message, $files, null, $model->titolo);
            }
        }

        $message = "<p>" . AmosSondaggi::t('amossondaggi',
                        'L’utente {utente} ha compilato il sondaggio <strong>{titolo}</strong>, in allegato trovi il sondaggio compilato.',
                        ['titolo' => $model->titolo, 'utente' => !empty($nomeCognome) ? $nomeCognome : 'Utente anonimo']) . "</p>";

        foreach ($additionalEmails as $email) {
            $user = User::find()->andWhere(['email' => $email])->one();
            if (!empty($email)) {
                SondaggiUtility::sendEmailGeneral([trim($email)], $user, $subject, $message, $files, null, $model->titolo);
            }
        }
    }

    /**
     * Sends an email to the user to which a specific poll was assigned via the "Assign compiler" option.
     *
     * @param Sondaggi $model The assigned poll
     * @param User $user User to which it was assigned
     *
     * @return null
     */
    public static function sendEmailAssignedPoll($id, $user_id, $to_id = null) {
        $model = Sondaggi::findOne($id);
        $user = User::findOne($user_id);
        $to = null;
        if (AmosSondaggi::instance()->compilationToOrganization)
            $to = Profilo::findOne($to_id);

        $message = '';
        $close_date = '';

        if (!empty($model->close_date)) {
            $close_date .= AmosSondaggi::t('amossondaggi', '#email_assigned_compiler_close_date', [
                        'closeDate' => \Yii::$app->formatter->asDate($model->close_date)
            ]);
        }

        $link = \Yii::$app->urlManager->createAbsoluteUrl([
            '/' . AmosSondaggi::getModuleName() . '/pubblicazione/compila',
            'id' => $model->id
        ]);

        if (!empty($to)) {
            $message .= AmosSondaggi::t('amossondaggi', '#email_assigned_compiler_to', [
                        'firstName' => $user->userProfile->nome,
                        'lastName' => $user->userProfile->cognome,
                        'title' => $model->titolo,
                        'to' => $to->name,
                        'link' => $link,
                        'closeDateMessage' => $close_date
            ]);
        } else {
            $message .= AmosSondaggi::t('amossondaggi', '#email_assigned_compiler', [
                        'firstName' => $user->userProfile->nome,
                        'lastName' => $user->userProfile->cognome,
                        'title' => $model->titolo,
                        'link' => $link
            ]);
        }

        $subject = AmosSondaggi::t('amossondaggi', '#email_assigned_compiler_subject', [
                    'title' => $model->titolo
        ]);

        if (!empty($user->email)) {
            SondaggiUtility::sendEmailGeneral([trim($user->email)], $user, $subject, $message);
        }

        return;
    }

    public static function sendEmailPublishedPoll($id, $user_id) {
        $model = Sondaggi::findOne($id);
        $user = User::findOne($user_id);
        $link = \Yii::$app->urlManager->createAbsoluteUrl([
            '/' . AmosSondaggi::getModuleName() . '/dashboard/dashboard',
            'id' => $model->id
        ]);
        $message = AmosSondaggi::t('amossondaggi', '#email_poll_published', [
                    'firstName' => $user->userProfile->nome,
                    'lastName' => $user->userProfile->cognome,
                    'title' => $model->titolo,
                    'link' => $link
        ]);
        $subject = AmosSondaggi::t('amossondaggi', '#email_poll_published_subject', [
                    'title' => $model->titolo
        ]);

        $users = User::find()->where(['status' => User::STATUS_ACTIVE])->all();
        foreach ($users as $user) {
            if (!empty($user->email) && array_key_exists('SUPER_USER', \Yii::$app->authManager->getRolesByUser($user->id))) {
                SondaggiUtility::sendEmailGeneral([trim($user->email)], $user, $subject, $message,[],null,$model->titolo);
            }
        }
    }

    public static function sendEmailRemovedCompilation($id, $entity) {
        $model = Sondaggi::findOne($id);
        $name = '';
        $email = '';
        $profile = null;
        if ($entity instanceof \open20\amos\organizzazioni\models\Profilo) {
            $profile = $entity->referenteOperativo;
            if (!is_null($profile)) {
                $name = $profile->nomeCognome;
                $email = $profile->user->email;
            } else {
                $name = $entity->name;
                $email = $entity->operativeHeadquarter->email;
            }
        } else {
            $profile = $entity;
            $name = $profile->nomeCognome;
            $email = $profile->user->email;
        }

        $message = AmosSondaggi::t('amossondaggi', '#email_compilation_removed', [
                    'name' => $name,
                    'title' => $model->titolo
        ]);
        $subject = AmosSondaggi::t('amossondaggi', '#email_compilation_removed_subject', [
                    'title' => $model->titolo
        ]);

        if (!empty($email)) {
            SondaggiUtility::sendEmailGeneral([trim($email)], $profile, $subject, $message);
        }
    }

    /**
     * @param $model
     * @return array
     */
    public static function getSidebarPages($model, $idQuestion = null, $page = null) {
        $controllerDashboard = 'dashboard';
        $menu = [];
        if (!\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') &&
            !\Yii::$app->getUser()->can('CREATORE_SONDAGGI')) {
            if (empty($page))
                return [];
            else
                return [[
                'undo' => [
                    'label' => AmosSondaggi::t('amossondaggi', 'Indietro'),
                    'activeTargetAction' => '',
                    'activeTargetController' => 'dashboard',
                    'titleLink' => AmosSondaggi::t('amossondaggi', 'Indietro'),
                    'url' => '/sondaggi/dashboard/dashboard?id=' . $model->id,
                    'icon' => 'caret-left'
                ]
                ]];
        }
        if (empty($idQuestion))
            $menu[] = [
                'mainMenu' => [
                    'label' => AmosSondaggi::t('amossondaggi', 'Dashboard'),
                    'icon' => 'view-dashboard',
                    'activeTargetAction' => 'dashboard',
                    'activeTargetController' => 'dashboard',
                    'titleLink' => AmosSondaggi::t('amossondaggi', 'Dashboard'),
                    'url' => '/sondaggi/dashboard/dashboard?id=' . $model->id
                ]
            ];
        else
            $menu[] = [
                'undo' => [
                    'label' => AmosSondaggi::t('amossondaggi', 'Indietro'),
                    'activeTargetAction' => '',
                    'activeTargetController' => 'dashboard',
                    'titleLink' => AmosSondaggi::t('amossondaggi', 'Indietro'),
                    'url' => '/sondaggi/dashboard/dashboard?id=' . $model->id,
                    'icon' => 'caret-left',
                ],
            ];

        $context = $model->sondaggiContext;
        if ($context) {
            array_push($menu,
                    [
                        'mainMenu' => [
                            'label' => $context->label ?: AmosSondaggi::t('amossondaggi', 'Vai al padre'),
                            'activeTargetAction' => '',
                            'activeTargetController' => 'dashboard',
                            'titleLink' => $context->label ?: AmosSondaggi::t('amossondaggi', 'Vai al padre'),
                            'url' => $context->context_url,
                            'icon' => 'caret-left',
                        ],
            ]);
        }

        array_push($menu,
                [
                    'mainMenu' => [
                        'label' => AmosSondaggi::t('amossondaggi', 'Gestione sondaggio'),
                        'icon' => 'sondaggi',
                        'framework' => 'ic',
                        'activeTargetAction' => 'info',
                        'activeTargetController' => 'dashboard',
                        'titleLink' => AmosSondaggi::t('amossondaggi', 'Gestione sondaggio'),
                        'url' => '/sondaggi/dashboard/info?id=' . $model->id,
                    ],
                ],
                [
                    'mainMenu' => [
                        'label' => AmosSondaggi::t('amossondaggi', 'Gestione pagine'),
                        'icon' => 'collection-text',
                        'activeTargetAction' => 'index',
                        'activeTargetController' => 'dashboard-domande-pagine',
                        'titleLink' => AmosSondaggi::t('amossondaggi', 'Gestione pagine'),
                        'url' => '/sondaggi/dashboard-domande-pagine?idSondaggio=' . $model->id,
                    ],
                ],
                [
                    'mainMenu' => [
                        'label' => AmosSondaggi::t('amossondaggi', 'Gestione domande'),
                        'icon' => 'pin-help',
                        'activeTargetAction' => 'index',
                        'activeTargetController' => 'dashboard-domande',
                        'titleLink' => AmosSondaggi::t('amossondaggi', 'Gestione domande'),
                        'url' => '/sondaggi/dashboard-domande?idSondaggio=' . $model->id
                    ]
                ],
                [
                    'mainMenu' => [
                        'label' => AmosSondaggi::t('amossondaggi', 'Gestione regole'),
                        'icon' => 'view-list',
                        'activeTargetAction' => 'rules',
                        'activeTargetController' => 'dashboard-domande-pagine',
                        'titleLink' => AmosSondaggi::t('amossondaggi', 'Gestione regole'),
                        'url' => '/sondaggi/dashboard-domande-pagine/rules?idSondaggio=' . $model->id
                    ]
                ]
// [
//     'mainMenu' => [
//         'label' => AmosSondaggi::t('amossondaggi', 'Landing Page'),
//         'icon' => 'view-compact',
//         'activeTargetAction' => 'landing-page',
//         'activeTargetController' => $controllerDashboard,
//         'titleLink' => AmosSondaggi::t('amossondaggi', 'Landing Page'),
//         'url' => '/sondaggi/dashboard/landing?id='.$model->id,
//     ],
// ],
// [
//     'mainMenu' => [
//         'label' => AmosSondaggi::t('amossondaggi', 'Modifica community'),
//         'icon' => 'accounts',
//         'activeTargetAction' => 'edit-community',
//         'activeTargetController' => $controllerDashboard,
//         'titleLink' => AmosSondaggi::t('amossondaggi', 'Modifica community'),
//         'url' => '/sondaggi/dashboard/community?idSondaggio='.$model->id.'&url='.\yii\helpers\Url::current(),
//     ],
// ],
// [
//     'mainMenu' => [
//         'label' => AmosSondaggi::t('amossondaggi', 'Template email'),
//         'icon' => 'email',
//         'activeTargetAction' => 'email-template',
//         'activeTargetController' => $controllerDashboard,
//         'titleLink' => AmosSondaggi::t('amossondaggi', 'Template email'),
//         'url' => '/sondaggi/dashboard/email-template?id='.$model->id,
//     ],
// ],
        );

        if (AmosSondaggi::instance()->hasInvitation && \Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
            array_push($menu,
                    [
                        'mainMenu' => [
                            'label' => AmosSondaggi::t('amossondaggi', 'Gestione inviti'),
                            'icon' => 'account-box-mail',
                            'activeTargetAction' => 'index',
                            'activeTargetController' => 'dashboard-invitations',
                            'titleLink' => AmosSondaggi::t('amossondaggi', 'Gestione inviti'),
                            'url' => '/sondaggi/dashboard-invitations?idSondaggio=' . $model->id,
                        ],
                    ]
            );
        }

        if (AmosSondaggi::instance()->hasComunications && \Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI')) {
            array_push($menu,
                    [
                        'mainMenu' => [
                            'label' => AmosSondaggi::t('amossondaggi', 'Gestione comunicazioni'),
                            'icon' => 'mail-send',
                            'activeTargetAction' => 'communications',
                            'activeTargetController' => $controllerDashboard,
                            'titleLink' => AmosSondaggi::t('amossondaggi', 'Gestione comunicazioni'),
                            'url' => '/sondaggi/dashboard/communications?sondaggi_id=' . $model->id,
                        ],
                    ]
            );
        }

        return $menu;
    }

    /**
     * @param $controller
     * @param $id
     */
    public static function regenerateSondaggioPagesModelAndViews($id) {
        $controller = new PubblicazioneController('tmp-controller', AmosSondaggi::instance());
        $dir_models = $controller->alias_path . DS . $controller->base_dir . DS . "models" . DS . "q" . $id;
        $dir_views = $controller->alias_path . DS . $controller->base_dir . DS . "views" . DS . "q" . $id;

        if (!is_dir($dir_models)) {
            mkdir($dir_models, 0777, true);
        }
        if (!is_dir($dir_views)) {
            mkdir($dir_views, 0777, true);
        }
        $sondaggio = Sondaggi::findOne(['id' => $id]);
        $pagine = $sondaggio->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
        $num_pagine = $pagine->count();
        $np = 1;
        $generatore = new GeneratoreSondaggio();
        foreach ($pagine->all() as $pagina) {
            $generatore->creaValidator($controller->percorso_validator, $pagina['id']);
            $generatore->creaView("common" . DS . "uploads" . DS . $controller->base_dir . DS . "views" . DS . "q" . $id, $pagina['id'],
                    $controller->percorso_view . $id);
            $generatore->creaModel("common" . DS . "uploads" . DS . $controller->base_dir . DS . "models" . DS . "q" . $id, $pagina['id'],
                    $controller->percorso_validator, $controller->percorso_model . $id);
        }
    }

    /**
     * if return string 'ok' delete is correct
     * in other cases message to display...
     *
     * @param $id
     * @return string
     * @throws AmosException
     */
    public static function deleteSondaggio($id) {
// $id deve essere un sondaggio...
        /** @var Sondaggi $model */
        $model = Sondaggi::findOne(['id' => $id]);
        if (empty($model)) {
            throw new AmosException('Parametro $id=' . $id . ' errato, sondaggio inesistente');
        }

// se il sondaggio è in stato bozza/disattivato non è possibile eliminare!
        if ($model->status != Sondaggi::WORKFLOW_STATUS_BOZZA) {
            return AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare il sondaggio in quanto non è in stato BOZZA.");
        }

        $model->delete();
        return 'ok';
    }

    /**
     * if return string 'ok' delete is correct
     * in other cases message to display...
     *
     * @param $id
     * @return string
     * @throws AmosException
     */
    public static function deletePage($id) {
// $id deve essere un sondaggio...
        /** @var SondaggiDomandePagine $model */
        $model = SondaggiDomandePagine::findOne(['id' => $id]);
        if (empty($model)) {
            throw new AmosException('Parametro $id=' . $id . ' errato, pagina inesistente');
        }

// se il sondaggio è in stato bozza/disattivato non è possibile eliminare!
        if ($model->sondaggi->status != Sondaggi::WORKFLOW_STATUS_BOZZA) {
            return AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare la pagina in quanto il sondaggio a cui è collegata non è in stato BOZZA.");
        }

// l'eliminazione di una pagina deve eliminare anche tutte le domande al suo interno
// altrimenti poi compaiono nell'elenco di tutte le domande del sondaggio

        $qlist = $model->sondaggiDomandes;
        if (!empty($qlist) && is_array($qlist)) {
            /** @var SondaggiDomande $q */
            foreach ($qlist as $q) {
                $q->delete();
            }
        }

        $model->delete();
        return 'ok';
    }

    /**
     * if return string 'ok' delete is correct
     * in other cases message to display...
     *
     * @param $id
     * @return string
     * @throws AmosException
     */
    public static function deleteAnswer($id) {
// $id deve essere un sondaggio...
        /** @var SondaggiDomande $model */
        $model = SondaggiDomande::findOne(['id' => $id]);
        if (empty($model)) {
            throw new AmosException('Parametro $id=' . $id . ' errato, domanda inesistente');
        }

// se il sondaggio è in stato bozza/disattivato non è possibile eliminare!
        if ($model->sondaggi->status != Sondaggi::WORKFLOW_STATUS_BOZZA) {
            return AmosSondaggi::tHtml('amossondaggi', "Impossibile cancellare la domanda in quanto il sondaggio a cui è collegata non è in stato BOZZA.");
        }

        $rispostePredefiniteIds = SondaggiRispostePredefinite::find()
            ->select('id')
            ->andWhere(['sondaggi_domande_id' => $id])
            ->column();
        if (!empty($rispostePredefiniteIds)) {
            SondaggiRisposte::deleteAll(['sondaggi_risposte_predefinite_id' => $rispostePredefiniteIds]);
        }
        SondaggiRispostePredefinite::deleteAll(['sondaggi_domande_id' => $id]);
        SondaggiDomande::deleteAll(['parent_id' => $id]);
        SondaggiDomandeCondizionate::deleteAll(['sondaggi_domande_id' => $id]);
        $model->delete();
        return 'ok';
    }

    /**
     * Get invitation email content message
     * @param $sondaggio Sondaggi
     * @param $userProfile UserProfile
     * @return string
     * @throws InvalidConfigException
     */
    public static function getInvitationEmailContent($sondaggio, $userProfile) {
        return AmosSondaggi::t('amossondaggi', 'invitation_message', [
                    'titolo' => $sondaggio->titolo,
                    'platformName' => Yii::$app->name,
                    'urlPollCompilation' => Yii::$app->urlManager->createAbsoluteUrl([
                        '/' . AmosSondaggi::getModuleName() . '/pubblicazione/compila',
                        'id' => $sondaggio->id
                    ]),
                    'urlPlatform' => Yii::$app->urlManager->createAbsoluteUrl('/'),
                    'nomeCognome' => $userProfile->nomeCognome,
                    'data' => Yii::$app->formatter->asDate($sondaggio->close_date),
        ]);
    }

    /**
     * Get invitation email content message for users invitations
     * @param $sondaggio Sondaggi
     * @param $userProfile UserProfile
     * @return string
     * @throws InvalidConfigException
     */
    public static function getInvitationUserEmailContent($sondaggio, $userProfile) {
        return AmosSondaggi::t('amossondaggi', 'invitation_message', [
                    'titolo' => $sondaggio->titolo,
                    'platformName' => Yii::$app->name,
                    'urlPollCompilation' => Yii::$app->urlManager->createAbsoluteUrl([
                        '/' . AmosSondaggi::getModuleName() . '/pubblicazione/compila',
                        'id' => $sondaggio->id
                    ]),
                    'urlPlatform' => Yii::$app->urlManager->createAbsoluteUrl('/'),
                    'nomeCognome' => $userProfile->nomeCognome,
                    'data' => Yii::$app->formatter->asDate($sondaggio->close_date),
        ]);
    }

    /**
     * Get invitation organization email content message
     * @param $sondaggio
     * @param $userProfile
     * @return string
     * @throws InvalidConfigException
     */
    public static function getInvitationOrganizationEmailContent($sondaggio, $organization) {
        return AmosSondaggi::t('amossondaggi', 'invitation_organization_message', [
                    'titolo' => $sondaggio->titolo,
                    'platformName' => Yii::$app->name,
                    'urlPollCompilation' => Yii::$app->urlManager->createAbsoluteUrl([
                        '/' . AmosSondaggi::getModuleName() . '/pubblicazione/compila',
                        'id' => $sondaggio->id
                    ]),
                    'urlPlatform' => Yii::$app->urlManager->createAbsoluteUrl('/'),
                    'ente' => $organization->name,
                    'data' => Yii::$app->formatter->asDate($sondaggio->close_date),
        ]);
    }

    /**
     * Generate xls results file
     * @param $id
     * @return string
     * @throws InvalidConfigException
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    public static function generateXlsResults($id) { 
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $model = Sondaggi::findOne($id);
        $xlsData = [];
        $basePath = \Yii::getAlias('@vendor/../common/uploads/temp');
        $zipAttachs = [];

// se abilita_registrazione == 1 allora i tre campi vanno visualizzati, se no, vale il parametro statisticExtractDisableNameSurnameEmail
        $viewNameSurnameEmail = $model->abilita_registrazione || !AmosSondaggi::instance()->statisticExtractDisableNameSurnameEmail;

        $intestazioneStart = [];
        $offset = -3;
        if ($viewNameSurnameEmail) {
            $intestazioneStart = ["Nome", "Cognome", "Email"];
            $offset = 0;
        }

// INTESTAZIONE EXCEL
        $xlsData[0] = array_merge($intestazioneStart, ["Iniziato il", "Completato il"]);
        if (AmosSondaggi::instance()->compilationToOrganization) {
            $xlsData[0] = array_merge($intestazioneStart, ["Ente", "Iniziato il", "Completato il"]);
        }
        $domande = [];
        $pagine = $model->getSondaggiDomandePagines()->orderBy('sondaggi_domande_pagine.ordinamento');
        foreach ($pagine->all() as $pagina) {
            $domandePagina = $pagina->getSondaggiDomandes()->andWhere(['parent_id' => null])->orderBy('ordinamento ASC')->all();
            foreach ($domandePagina as $domandaPag) {
                $domande[] = $domandaPag;
            }
        }

        $count = 1;
        $totCount = 5;
        if (AmosSondaggi::instance()->compilationToOrganization) {
            $totCount = 6;
        }
        if (AmosSondaggi::instance()->enableCompilationWorkflow) {
            $xlsData[0][] = "Stato";
            $totCount++;
        }
        $colRisp = [];
        $colRispLibere = [];
        $colRispAllegati = [];
        foreach ($domande as $domanda) {
            $rispostePredefinite = $domanda->getSondaggiRispostePredefinites();
            $countRisposte = $rispostePredefinite->count();
            $localCount = 1;
            if (in_array($domanda->sondaggi_domande_tipologie_id, [10, 11])) {
                $xlsData[0][] = "D." . $count . " " . $domanda->domanda;
                $colRispAllegati[$domanda->id] = $totCount;
                $totCount++;
            } else if (in_array($domanda->sondaggi_domande_tipologie_id, [5, 6, 13, 15, 16, 17])) {
                $xlsData[0][] = "D." . $count . " " . $domanda->domanda;
                $colRispLibere[$domanda->id] = $totCount;
                $totCount++;
            } else {
                if (!empty($countRisposte) && in_array($domanda->sondaggi_domande_tipologie_id, [1, 2, 3, 4])) {
                    if ($domanda->is_parent) {
                        $childs = $domanda->childs;
                        foreach ($childs as $ch) {
                            foreach ($rispostePredefinite->orderBy('ordinamento ASC')->all() as $rispPre) {
                                $xlsData[0][] = "D." . $count . " " . $domanda->domanda . " \n" . $ch->domanda . " \nR." . $localCount . " " . $rispPre->risposta;
                                $colRisp[$rispPre->id][$ch->id] = $totCount;
                                $localCount++;
                                $totCount++;
                            }
                        }
                    } else {
                        foreach ($rispostePredefinite->orderBy('ordinamento ASC')->all() as $rispPre) {
                            $xlsData[0][] = "D." . $count . " " . $domanda->domanda . " \nR." . $localCount . " " . $rispPre->risposta;
                            $colRisp[$rispPre->id] = $totCount;
                            $localCount++;
                            $totCount++;
                        }
                    }
                }
            }
            $count++;
        }

// CORPO FILE EXCEL
        $sondaggiRisposte = SondaggiRisposteSessioni::find()
                ->distinct()
                ->innerJoin('sondaggi_risposte',
                        'sondaggi_risposte_sessioni.id = sondaggi_risposte.sondaggi_risposte_sessioni_id')
                ->leftJoin('user_profile', 'user_profile.user_id = sondaggi_risposte_sessioni.user_id')
                ->leftJoin('user', 'user_profile.user_id = user.id')
                ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $id])
                ->orderBy('sondaggi_risposte_sessioni.begin_date')
                ->all();

        $row = 1;

        $srpArray = SondaggiRispostePredefinite::find()->asArray()->all();
        $sondRispPredefAll = [];
        foreach ($srpArray as $element) {
            $sondRispPredefAll[$element['id']] = $element;
        }

        foreach ($sondaggiRisposte as $sondRisposta) {
            $profile = null;
            if (!empty($sondRisposta->user_id)) {

                /** @var AmosAdmin $adminModule */
                $adminModule = AmosAdmin::instance();
                /** @var UserProfile $userProfileModel */
                $userProfileModel = $adminModule->createModel('UserProfile');
                $profile = $userProfileModel::find()->andWhere(['user_id' => $sondRisposta->user_id])->one();
            }

            if (empty($profile)) {
                if ($viewNameSurnameEmail) {
                    $xlsData [$row][0 + $offset] = ($model->abilita_criteri_valutazione == 1 ? AmosSondaggi::t('amossondaggi',
                                    'L\'utente non ha effettuato la registrazione') : AmosSondaggi::t('amossondaggi',
                                    'Sondaggio anonimo'));
                    $xlsData [$row][1 + $offset] = ($model->abilita_criteri_valutazione == 1 ? AmosSondaggi::t('amossondaggi',
                                    'L\'utente non ha effettuato la registrazione') : AmosSondaggi::t('amossondaggi',
                                    'Sondaggio anonimo'));
                    $xlsData [$row][2 + $offset] = ($model->abilita_criteri_valutazione == 1 ? AmosSondaggi::t('amossondaggi',
                                    'L\'utente non ha effettuato la registrazione') : AmosSondaggi::t('amossondaggi',
                                    'Sondaggio anonimo'));
                }
            } else {
                if ($viewNameSurnameEmail) {
                    $xlsData [$row][0 + $offset] = $profile->nome;
                    $xlsData [$row][1 + $offset] = $profile->cognome;
                    $xlsData [$row][2 + $offset] = $profile->user->email;
                }
            }
            $dateDiff = (new \DateTime())->diff(new \DateTime($sondRisposta->updated_at));
            if (($dateDiff->invert * $dateDiff->days) > 730 && AmosSondaggi::instance()->resetGdpr) {
                $xlsData [$row][0 + $offset] = "#####";
                $xlsData [$row][1 + $offset] = "#####";
                $xlsData [$row][2 + $offset] = "#####";
            }

            if (AmosSondaggi::instance()->compilationToOrganization) {
                $profilo = \open20\amos\organizzazioni\models\Profilo::find()->andWhere(['id' => $sondRisposta->organization_id])->one();
                $xlsData [$row][3 + $offset] = !empty($profilo->name) ? $profilo->name : '';
                $xlsData [$row][4 + $offset] = Yii::$app->formatter->asDatetime($sondRisposta->begin_date, 'php:d/m/Y H:i:s');
                $xlsData [$row][5 + $offset] = Yii::$app->formatter->asDatetime($sondRisposta->end_date, 'php:d/m/Y H:i:s');
                if (AmosSondaggi::instance()->enableCompilationWorkflow) {
                    $xlsData[$row][6 + $offset] = AmosSondaggi::t('amossondaggi', $sondRisposta->status);
                }
            } else {
                $xlsData [$row][3 + $offset] = Yii::$app->formatter->asDatetime($sondRisposta->begin_date, 'php:d/m/Y H:i:s');
                $xlsData [$row][4 + $offset] = Yii::$app->formatter->asDatetime($sondRisposta->end_date, 'php:d/m/Y H:i:s');
                if (AmosSondaggi::instance()->enableCompilationWorkflow) {
                    $xlsData[$row][5 + $offset] = AmosSondaggi::t('amossondaggi', $sondRisposta->status);
                }
            }

            $session_id = $sondRisposta->id;

            /** @var  $domanda SondaggiDomande */
            foreach ($domande as $domanda) {

                $query = $domanda->getRispostePerUtente((empty($profile) ? null : $profile->user_id), $session_id);
// RISPOSTE LIBERE
                if (in_array($domanda->sondaggi_domande_tipologie_id, [6, 5, 13, 15, 16, 17])) {

                    $risposta = $query->asArray()->one();
                    if ($risposta) {
                        if ($domanda->sondaggi_domande_tipologie_id == 13) {
                            $risposta['risposta_libera'] = Yii::$app->formatter->asDate($risposta['risposta_libera'], 'php:d/m/Y');
                        } else if($domanda->sondaggi_domande_tipologie_id == 15){
                            $provincia = \open20\amos\comuni\models\IstatProvince::find()->andWhere(['id' => $risposta['risposta_libera']])->asArray()->one();
                            $risposta['risposta_libera'] = $provincia['nome'];
                        } else if(in_array($domanda->sondaggi_domande_tipologie_id, [16, 17])){
                            $comune = \open20\amos\comuni\models\IstatComuni::find()->andWhere(['id' => $risposta['risposta_libera']])->asArray()->one();
                            $risposta['risposta_libera'] = $comune['nome'];
                        }
                        $xlsData[$row][$colRispLibere[$domanda->id] + $offset] = $risposta['risposta_libera'];
                    } else {

                    }
//ALLEGATI
                } else if ($domanda->sondaggi_domande_tipologie_id == 10 || $domanda->sondaggi_domande_tipologie_id == 11) {
                    $risposta = $query->one();
                    if ($risposta) {
                        $attribute = 'domanda_' . $domanda->id;
                        if (!empty($risposta->$attribute)) {
                            $attachments = $risposta->getFiles();
                            $listAttachUrls = [];
                            $i = 0;
                            foreach ($attachments as $attach) {
                                if (!AmosSondaggi::instance()->enableAnonimousPoll) {
                                    $folder = BaseInflector::slug($profile->cognome . ' ' . $profile->nome);
                                } else {
                                    $folder = 'Sondaggio anonimo ' . $i;
                                    $i++;
                                }
                                if (AmosSondaggi::instance()->compilationToOrganization) {
                                    $profilo = \open20\amos\organizzazioni\models\Profilo::find()->andWhere(['id' => $sondRisposta->organization_id])->one();
                                    $folder = BaseInflector::slug($profilo->name);
                                }
                                if (AmosSondaggi::instance()->xlsAsZip)
                                    $listAttachUrls [] = $folder . '/' . $attach->name . '.' . $attach->type;
                                else
                                    $listAttachUrls [] = \Yii::$app->params['platform']['backendUrl'] . $attach->getUrl();
                                $zipAttachs[$folder . '/' . $attach->name . '.' . $attach->type] = $attach->path;
                            }
                            $xlsData[$row][$colRispAllegati[$domanda->id] + $offset] = implode(" \n", $listAttachUrls);
                        }
                    } else {
                        
                    }
                } else {
                    $risposteArray = [];
                    /** @var SondaggiRisposte $risposta */
                    foreach ($query->asArray()->all() as $risposta) {
                        $srp = $sondRispPredefAll[$risposta['sondaggi_risposte_predefinite_id']];

                        if (!empty($srp)) {
                            if ($domanda->is_parent) {
                                if (empty($srp['code'])) {

                                    $xlsData[$row][$colRisp[$srp['id']][$risposta['sondaggi_domande_id']] + $offset] = $srp['risposta'];
                                } else {
                                    $xlsData[$row][$colRisp[$srp['id']][$risposta['sondaggi_domande_id']] + $offset] = $srp['code'];
                                }
                            } else {
                                if (empty($srp['code'])) {
                                    $xlsData[$row][$colRisp[$srp['id']] + $offset] = $srp['risposta'];
                                } else {
                                    $xlsData[$row][$colRisp[$srp['id']] + $offset] = $srp['code'];
                                }
                            }
                        }
                    }
                }
            }
            $row++;

            gc_collect_cycles();
        }

        $zip_filepath = $basePath . '/Risposte_sondaggio_' . $id . '.zip';
//inizializza l'oggetto excel
        $nomeFile = $basePath . '/Risposte_sondaggio_' . $id . '.xls';
        $objPHPExcel = new \PHPExcel();

// set Style first row
        $lastColumn = $totCount + $offset;
        $lastColumnLetter = \PHPExcel_Cell::stringFromColumnIndex($lastColumn);

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $lastColumnLetter . '1')->getFill()
                ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setRGB('C0C0C0');

        for ($i = 1;
                $i <= $row;
                $i++) {
            for ($c = 0;
                    $c <= $lastColumn;
                    $c++) {
                if (empty($xlsData[$i]) || !array_key_exists($c, $xlsData[$i])) {
                    $xlsData[$i][$c] = '';
                }
            }
        }

        foreach ($xlsData as $key => $value) {
            ksort($xlsData[$key]);
        }
//li pone nella tab attuale del file xls
        $objPHPExcel->getActiveSheet()->fromArray($xlsData, NULL, 'A1');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($nomeFile);
        if (!AmosSondaggi::instance()->xlsAsZip) {
            return $nomeFile;
        } else {
            /** @var \ZipArchive $zip */
            $zip = new \ZipArchive();
            if ($zip->open($zip_filepath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Cannot create a zip file');
            }
            $zip->addFile($nomeFile, 'Risposte_sondaggio.xls');
            foreach ($zipAttachs as $name => $file) {
                $zip->addFile($file, $name);
            }

            $zip->close();
            return $zip_filepath;
        }
    }

    /**
     * Statuses for search filters
     * @return array
     */
    public static function getSearchStatuses() {
        return [
            null => AmosSondaggi::t('amossondaggi', '#all'),
            Sondaggi::WORKFLOW_STATUS_BOZZA => AmosSondaggi::t('amossondaggi', Sondaggi::WORKFLOW_STATUS_BOZZA),
            Sondaggi::WORKFLOW_STATUS_VALIDATO => AmosSondaggi::t('amossondaggi', Sondaggi::WORKFLOW_STATUS_VALIDATO),
            Sondaggi::STATUS_CONCLUSO => AmosSondaggi::t('amossondaggi', 'Concluso'),
        ];
    }

    /**
     * Checks if the poll is terminated
     * @param $model
     * @return bool
     */
    public static function isTerminated($model) {
        return $model->close_date < date('Y-m-d');
    }

    /**
     * @return string[]
     */
    public static function getFileExtensionLabel() {
        return [
            'xls' => 'Excel',
            'pdf' => 'PDF',
        ];
    }

    /**
     * @param $model
     * @return Sondaggi
     * @throws InvalidConfigException
     */
    public static function cloneSondaggio($model) {
        /** @var  $model Sondaggi */
        $created_by = \Yii::$app->user->id;

        $data['Sondaggi'] = $model->attributes;
        $sondaggio = new Sondaggi();
        $sondaggio->load($data);
        $sondaggio->status = Sondaggi::WORKFLOW_STATUS_BOZZA;
        $sondaggio->titolo = $model->titolo . ' (clone)';
        $sondaggio->validatori = $model->validatori;
        $sondaggio->regola_pubblicazione = $model->regola_pubblicazione;
        $sondaggio->destinatari = $model->destinatari;
        $sondaggio->created_by = $created_by;
        $sondaggio->updated_by = $created_by;
        if(AmosSondaggi::instance()->stopEmail){
            $sondaggio->detachBehavior('NotifyBehavior');
        }
        $ok = $sondaggio->save(false);

        $pagine = $model->sondaggiDomandePagines;
//PAGINE
        foreach ($pagine as $pagina) {
            $newDomCond = [];
            $data = [];
            $newPagina = new SondaggiDomandePagine();
            $data['SondaggiDomandePagine'] = $pagina->attributes;
            $newPagina->load($data);
            $newPagina->sondaggi_id = $sondaggio->id;
            $newPagina->created_by = $created_by;
            $newPagina->updated_by = $created_by;
            $newPagina->save(false);

//DOMANDE
            $domande = $pagina->sondaggiDomandes;
            foreach ($domande as $domanda) {
                $data = [];
                $newDomanda = new SondaggiDomande();
                $data['SondaggiDomande'] = $domanda->attributes;
                $newDomanda->load($data);
                $newDomanda->id = null;
                $newDomanda->sondaggi_id = $sondaggio->id;
                $newDomanda->domanda_condizionata = $domanda->domanda_condizionata;
                $newDomanda->sondaggi_domande_pagine_id = $newPagina->id;
                $newDomanda->created_by = $created_by;
                $newDomanda->updated_by = $created_by;
                $okDom = $newDomanda->save(false);

                if ($okDom) {
                    if ($domanda->domanda_condizionata == 1) {
                        $rispCond = $domanda->sondaggiRispostePredefinitesCondizionate;
                        $newDomCond[$newDomanda->id] = ['ordinamento' => $rispCond->ordinamento, 'risposta' => $rispCond->risposta,
                            'pagina' => $newPagina->id];
                    }
                }

// RISPOSTE PREDEFINITE
                $risposte = $domanda->sondaggiRispostePredefinites;
                if (!empty($risposte)) {
                    foreach ($risposte as $risposta) {
                        $data = [];
                        $newRisposta = new SondaggiRispostePredefinite();
                        $data['SondaggiRispostePredefinite'] = $risposta->attributes;
                        $newRisposta->load($data);
                        $newRisposta->id = null;
                        $newRisposta->sondaggi_domande_id = $newDomanda->id;
                        $newRisposta->created_by = $created_by;
                        $newRisposta->updated_by = $created_by;
                        $newRisposta->save(false);
                    }
                }
            }

// DOMANDE CONDIZIONATE
            foreach ($newDomCond as $d => $r) {
                $allDomande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $r['pagina']])->select('id');

                $risp = SondaggiRispostePredefinite::find()
                        ->andWhere(['sondaggi_domande_id' => $allDomande])
                        ->andWhere(['risposta' => $r['risposta']])
                        ->andWhere(['ordinamento' => $r['ordinamento']])
                        ->one();

                if (!empty($risp)) {
                    $newDomandaCondiz = new SondaggiDomandeCondizionate();
                    $newDomandaCondiz->sondaggi_domande_id = $d;
                    $newDomandaCondiz->sondaggi_risposte_predefinite_id = $risp->id;
                    $newDomandaCondiz->created_by = $created_by;
                    $newDomandaCondiz->updated_by = $created_by;
                    $newDomandaCondiz->save(false);
                }
            }
        }
        return $sondaggio;
    }

    public static function publishSondaggio($sondaggio) {
        if (!defined('DS'))
            define('DS', DIRECTORY_SEPARATOR);
        $alias_path = Yii::getAlias('@common');
        $base_dir = "sondaggi" . DS . "pubblicazione";
        $percorso_model = "common\\uploads\\sondaggi\\pubblicazione\\models\\q";
        $percorso_view = "common\\uploads\\sondaggi\\pubblicazione\\views\\q";
        $percorso_validator = "common" . DS . "uploads" . DS . "sondaggi" . DS . "validators" . DS;

        $dir_models = $alias_path . DS . $base_dir . DS . "models" . DS . "q" . $sondaggio->id;
        $dir_views = $alias_path . DS . $base_dir . DS . "views" . DS . "q" . $sondaggio->id;

        if (!is_dir($dir_models)) {
            mkdir($dir_models, 0777, true);
        }
        if (!is_dir($dir_views)) {
            mkdir($dir_views, 0777, true);
        }

        if (!$sondaggio->verificaSondaggioPubblicabile()) {
            Yii::$app->getSession()->addFlash('danger',
                    AmosSondaggi::t('amossondaggi', '#cannot_publish'));
            return false;
        }

        $pagine = $sondaggio->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
        $num_pagine = $pagine->count();
        $np = 1;
        $generatore = new GeneratoreSondaggio();
        foreach ($pagine->all() as $pagina) {
            $generatore->creaValidator($percorso_validator, $pagina['id']);
            $generatore->creaView("common" . DS . "uploads" . DS . $base_dir . DS . "views" . DS . "q" . $sondaggio->id, $pagina['id'],
                    $percorso_view . $sondaggio->id);
            $generatore->creaModel("common" . DS . "uploads" . DS . $base_dir . DS . "models" . DS . "q" . $sondaggio->id, $pagina['id'],
                    $percorso_validator, $percorso_model . $sondaggio->id);
        }
        $sondaggio->status = Sondaggi::WORKFLOW_STATUS_VALIDATO;
        if(AmosSondaggi::instance()->stopEmail){
            $sondaggio->detachBehavior('NotifyBehavior');
        }

        $ok = $sondaggio->save(false);
        return true;
    }

    public static function isCommunityManagerOfContent($modelSondaggio) {
        $moduleSondaggi = \Yii::$app->getModule('sondaggi');

        $cwhConfigContentId = CwhConfigContents::findOne(['classname' => get_class($modelSondaggio)])->id;
        if(!empty($cwhConfigContentId)) {
            $cwhPubblicazioneData = CwhPubblicazioni::findOne([
                'cwh_config_contents_id' => $cwhConfigContentId,
                'content_id' => $modelSondaggio->id,
                'cwh_regole_pubblicazione_id' => CwhRegolePubblicazione::ALL_USERS_IN_DOMAINS,
            ]);

            if(!empty($cwhPubblicazioneData)) {
                $idAllowedNodiToCheck = CwhConfig::find()
                    ->andWhere(['IN', 'tablename', $moduleSondaggi->cwhNodiIdPrefixToCheck])
                    ->asArray()->all();
                $idAllowedNodiToCheckArray = ArrayHelper::map($idAllowedNodiToCheck, 'id', 'tablename');

                $nodiEditori = CwhPubblicazioniCwhNodiEditoriMm::find()
                    ->andWhere(['cwh_pubblicazioni_id' => $cwhPubblicazioneData->id])
                    ->andWhere(['IN', 'cwh_config_id', array_keys($idAllowedNodiToCheckArray)])
                    ->asArray()->one();

                if(empty($nodiEditori)) {
                    return false;
                }

                $communityManagerRecord = (new Query())
                    ->select('*')
                    ->from($moduleSondaggi->userMmTableToCheck[$idAllowedNodiToCheckArray[$nodiEditori['cwh_config_id']]])
                    ->andWhere([$idAllowedNodiToCheckArray[$nodiEditori['cwh_config_id']] . '_id' => $nodiEditori['cwh_network_id']])
                    ->andWhere(['IN', 'role', $moduleSondaggi->communityManagerRolesList])
                    ->andWhere(['user_id' => Yii::$app->user->id])
                    ->andWhere(['deleted_at' => null])
                    ->andWhere(['deleted_by' => null])
                    ->one();

                if(!empty($communityManagerRecord)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}
