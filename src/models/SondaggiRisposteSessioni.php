<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\sondaggi\models
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

namespace open20\amos\sondaggi\models;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\workflow\behaviors\WorkflowLogFunctionsBehavior;
use kartik\mpdf\Pdf;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseInflector;
use open20\amos\sondaggi\utility\SondaggiUtility;
use yii\helpers\VarDumper;

/**
 * Class SondaggiRisposteSessioni
 * This is the model class for table "sondaggi_risposte_sessioni".
 * @package open20\amos\sondaggi\models
 */
class SondaggiRisposteSessioni extends \open20\amos\sondaggi\models\base\SondaggiRisposteSessioni {

    public $byBassRuleCwh = true;

    // Workflow ID
    const WORKFLOW = 'SondaggiSessioniWorkflow';
    // Workflow states IDS
    const WORKFLOW_STATUS_BOZZA = 'SondaggiSessioniWorkflow/BOZZA';
    const WORKFLOW_STATUS_RICHIESTA_INVIO = 'SondaggiSessioniWorkflow/RICHIESTAINVIO';
    const WORKFLOW_STATUS_INVIATO = 'SondaggiSessioniWorkflow/INVIATO';
    const WORKFLOW_STATUS_BOZZA_KEY = 'BOZZA';
    const WORKFLOW_STATUS_RICHIESTA_INVIO_KEY = 'RICHIESTAINVIO';
    const WORKFLOW_STATUS_INVIATO_KEY = 'INVIATO';

    public function init() {
        $moduleSondaggi = AmosSondaggi::instance();
        if (($this->isNewRecord || $this->status == null) && $moduleSondaggi->enableCompilationWorkflow == true) {
            $this->status = $this->getWorkflowSource()->getWorkflow(self::WORKFLOW)->getInitialStatusId();
        }
        // Controllo inserito perchè il model è utilizzato anche in console per esportazione tramite taskmanager
        if (\Yii::$app instanceof \yii\web\Application) {
            $user_id = \Yii::$app->getUser()->id;
            $session = $this;
            if ($moduleSondaggi->enableCompilationWorkflow == true) {
                $this->on('afterEnterStatus{' . self::WORKFLOW_STATUS_RICHIESTA_INVIO . '}',
                        function () use ($user_id) {
                            // Deleting associated "standard" users which were enabled to compile the poll
                            $result = SondaggiUsersInvitationMm::find()->andWhere(['sondaggi_id' => $this->sondaggi_id, 'user_id' => $user_id])->one();
                            $result->forceDelete();
                        }, $this);
                $this->on('afterEnterStatus{' . self::WORKFLOW_STATUS_INVIATO . '}',
                        function ($event) use ($session) {
                            // Sending emails on "Sent" status
                            $path = \Yii::getAlias("@common/uploads/temp/") . "Sondaggio_compilato" . $session->id . '_' . time() . ".pdf";
                            $fileSaved = $session->generateSondaggiPdf($path);
                            if (AmosSondaggi::instance()->enableCompilationWorkflow) {
                                if ($session->sondaggi->send_pdf_to_compiler || $session->sondaggi->send_pdf_via_email) {
                                    SondaggiUtility::sendEmailSondaggioCompilato($session->sondaggi, $session->id, $fileSaved->getPath());
                                }
                            }
                        }, $this);
            }
        }
        return parent::init();
    }

    /**
     * @inheritdoc
     */
    public function representingColumn() {
        return [
                //inserire il campo o i campi rappresentativi del modulo
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {

        $rules = ArrayHelper::merge(parent::rules(), [
                    ['status', 'safe'],
        ]);

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        $moduleSondaggi = AmosSondaggi::instance();
        if ($moduleSondaggi->enableCompilationWorkflow == true) {
            $workflowBehaviors = [
                'workflow' => [
                    'class' => SimpleWorkflowBehavior::className(),
                    'defaultWorkflowId' => self::WORKFLOW,
                    'propagateErrorsToModel' => true
                ],
                'WorkflowLogFunctionsBehavior' => [
                    'class' => WorkflowLogFunctionsBehavior::className(),
                ],
            ];
        } else {
            $workflowBehaviors = [];
        }
        return ArrayHelper::merge(parent::behaviors(), $workflowBehaviors);
    }

    /**
     * @param $id
     * @throws \yii\web\NotFoundHttpException
     */
    public function generateSondaggiPdf($path = null) {
        $id = $this->id;
        $sondaggio = $this->sondaggi;
        $sondaggioId = $sondaggio->id;
        $xlsData = [];
        // se abilita_registrazione == 1 allora i tre campi vanno visualizzati, se no, vale il parametro statisticExtractDisableNameSurnameEmail
        $viewNameSurnameEmail = $sondaggio->abilita_registrazione || !AmosSondaggi::instance()->statisticExtractDisableNameSurnameEmail;


        // INTESTAZIONE EXCEL
        $xlsData[0] = ["", "", ""];
        if ($viewNameSurnameEmail) {
            $xlsData[0] = ["Nome", "Cognome", "Email"];
        }
        if (AmosSondaggi::instance()->compilationToOrganization) {
            $xlsData[0][] = AmosSondaggi::t('amossondaggi', '#organization');
        }

        $domande = [];
        $pagine = $sondaggio->getSondaggiDomandePagines()->orderBy('sondaggi_domande_pagine.ordinamento');
        /** @var $pagina SondaggiDomandePagine */
        foreach ($pagine->all() as $pagina) {
            $domandePagina = $pagina->getSondaggiDomandes()->andWhere(['parent_id' => null])->orderBy('ordinamento ASC')->all();
            foreach ($domandePagina as $domandaPag) {
                $domande[] = $domandaPag;
            }
        }

        $count = 1;
        foreach ($domande as $domanda) {
            $testoDomandaCompleto = "D." . $count;
            if (!empty($domanda->introduzione)) {
                $testoDomandaCompleto .= $domanda->introduzione;
            }
            $testoDomandaCompleto .= $domanda->domanda;
            if (isset($domanda->immagine) && $domanda->immagine) {
                $testoDomandaCompleto .= "<br><img width='33%' src='{$domanda->immagine->getWebUrl(['width' => 600, 'quality' => 80])}' alt=''/>";
            }
            $xlsData[0][] = $testoDomandaCompleto;
            $count++;
        }

        // CORPO FILE EXCEL
        $sondRisposta = SondaggiRisposteSessioni::find()
            ->distinct()
            ->leftJoin('user_profile', 'user_profile.user_id = sondaggi_risposte_sessioni.user_id')
            ->leftJoin('user', 'user.id = user_profile.user_id')
            ->leftJoin('sondaggi_risposte',
                'sondaggi_risposte_sessioni.id = sondaggi_risposte.sondaggi_risposte_sessioni_id')
            ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $sondaggioId])
            ->andWhere(['sondaggi_risposte_sessioni.id' => $id])
            ->andWhere(['sondaggi_risposte_sessioni.completato' => 1])
//            ->andWhere(['user_profile.user_id' => \Yii::$app->user->id])
            ->one();

        if (empty($sondRisposta)) {
            return false;
        }

        $row = 1;
        $profile = null;
        if (!empty($sondRisposta->user_id)) {
            /** @var AmosAdmin $adminModule */
            $adminModule = AmosAdmin::instance();
            /** @var UserProfile $userProfileModel */
            $userProfileModel = $adminModule->createModel('UserProfile');
            $profile = $userProfileModel::find()->andWhere(['user_id' => $sondRisposta->user_id])->one();
        }

        if ($viewNameSurnameEmail && !empty($profile)) {
            $xlsData [$row][0] = $profile->nome;
            $xlsData [$row][1] = $profile->cognome;
            $xlsData [$row][2] = $profile->user->email;
        } else {
            $xlsData [$row][0] = "";
            $xlsData [$row][1] = "";
            $xlsData [$row][2] = "";
        }
        $session_id = $sondRisposta->id;

        /** @var  $domanda SondaggiDomande */
        $colum = 3;
        if (AmosSondaggi::instance()->compilationToOrganization) {
            $profilo = \open20\amos\organizzazioni\models\Profilo::find()->andWhere(['id' => $sondRisposta->organization_id])->one();
            $xlsData[$row][$colum] = !empty($profilo->name) ? $profilo->name : '';
            $colum++;
        }
        $divider = $colum;
        foreach ($domande as $domanda) {
            $query = $domanda->getRispostePerUtente((empty($profile) ? null : $profile->user_id), $session_id);
            // RISPOSTE LIBERE
            if (in_array($domanda->sondaggi_domande_tipologie_id, [5, 6, 13, 15, 16, 17])) {
                $risposteArray = [];
                foreach ($query->all() as $risposta) {
                    $domandaSpecifica = $risposta->sondaggiDomande;
                    if (!$domandaSpecifica) {
                        continue;
                    }

                    $rispostaFormattata = $risposta->risposta_libera;
                    $tipoDomandaSpecifica = $domandaSpecifica->sondaggi_domande_tipologie_id;

                    //ALLEGATI
                    if ($tipoDomandaSpecifica == 13) {
                        $rispostaFormattata = \Yii::$app->formatter->asDate($risposta->risposta_libera);
                    } else if ($tipoDomandaSpecifica == 15) {
                        $provincia = \open20\amos\comuni\models\IstatProvince::find()->andWhere(['id' => $risposta->risposta_libera])->asArray()->one();
                        if ($provincia) {
                            $rispostaFormattata = $provincia['nome'];
                        }
                    } else if (in_array($tipoDomandaSpecifica, [16, 17])) {
                        $comune = \open20\amos\comuni\models\IstatComuni::find()->andWhere(['id' => $risposta->risposta_libera])->asArray()->one();
                        if ($comune) {
                            $rispostaFormattata = $comune['nome'];
                        }
                    } else {
                        $rispostaFormattata = str_replace(['<', '>'], ' ', $risposta->risposta_libera);
                    }

                    if (!empty($domandaSpecifica->parent_id)) {
                        $risposteArray[] = $domandaSpecifica->domanda . ': ' . $rispostaFormattata;
                    } else {
                        $risposteArray[] = $rispostaFormattata;
                    }
                }
                $xlsData[$row][$colum] = "<ul><li>" . implode("</li><li>", $risposteArray) . "</ul>";
            } else if ($domanda->sondaggi_domande_tipologie_id == 12) {
                $xlsData [$row][$colum] = '';
            } else if ($domanda->sondaggi_domande_tipologie_id == 10 || $domanda->sondaggi_domande_tipologie_id == 11) {
                $risposta = $query->one();
                if ($risposta) {
                    $attribute = 'domanda_' . $domanda->id;
                    if (!empty($risposta->$attribute)) {
                        $attachments = $risposta->getFiles();
                        $listAttachUrls = [];
                        $risposteString = "<ul>";
                        /** @var  $attach File */
                        foreach ($attachments as $attach) {
                            if (AmosSondaggi::instance()->xlsAsZip) {
                                if(!empty($profile) && !AmosSondaggi::instance()->forceOnlyFrontend) {
                                    $folder = BaseInflector::slug($profile->cognome . ' ' . $profile->nome);
                                } else {
                                    $folder = BaseInflector::slug($this->id . ' ' . $this->begin_date . ' sondaggio');
                                }
                                if (AmosSondaggi::instance()->compilationToOrganization) {
                                    $profilo = \open20\amos\organizzazioni\models\Profilo::find()->andWhere(['id' => $sondRisposta->organization_id])->one();
                                    $folder = BaseInflector::slug($profilo->name);
                                }
                                $risposteString .= "<li>" . $folder . '/' . $attach->name . '.' . $attach->type . "</li>";
                            } else {
                                $risposteString .= "<li><a href='" . \Yii::$app->params['platform']['backendUrl'] . $attach->getUrl() . "'>" . $attach->name . "</a></li>";
                            }
                        }
                        $risposteString .= '</ul>';
                        $xlsData [$row][$colum] = $risposteString;
//                        $xlsData [$row][$colum] = "<ul><li>".implode("</li><li>", $listAttachUrls)."</ul>";
//                            $xlsData [$row][$colum] = implode("\n", $listAttachUrls);
                    }
                } else {
                    $xlsData [$row][$colum] = '';
                }
            } else {
                $risposteArray = [];

                foreach ($query->all() as $risposta) {
                    $rispPred = $risposta->sondaggiRispostePredefinite;
                    if ($rispPred) {
                        if (empty($rispPred->code)) {

                            $domanda = $risposta->sondaggiDomande;

                            // Caso domanda contenitore, visualizzo anche la domanda che ha generato la risposta
                            if (!empty($domanda->parent_id) && $domanda->parent_id >= 1) {
                                $risposteArray [] = $domanda->domanda . ': ' . $rispPred->risposta;
                            } else {
                                $risposteArray [] = $rispPred->risposta;
                            }

                        } else
                            $risposteArray [] = $rispPred->code . ' - ' . $rispPred->risposta;
                    }
                }
//                    $xlsData [$row][$colum] = implode("\n", $risposteArray);

                $xlsData [$row][$colum] = "<ul><li>" . implode("</li><li>", $risposteArray) . "</ul>";

//                    pr(implode(',', $risposteArray), 'D. ' . $domanda->id);
            }
            $colum++;
        }
        return $this->savePdf($xlsData, $sondaggio, $sondRisposta, $path, $divider);
    }

    /**
     * @param $data
     * @param $modelSondaggio
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function savePdf($data, $modelSondaggio, $modelSondaggioRisposta, $path = null, $divider = 0) {
        $content = \Yii::$app->controller->renderPartial('@vendor/open20/amos-sondaggi/src/views/sondaggi/_view_pdf',
            [
                'data' => $data,
                'sondaggio' => $modelSondaggio,
                'rispostaModel' => $modelSondaggioRisposta,
                'divider' => $divider
            ]
        );
//        $footer = $this->renderPartial('@vendor/open20/amos-proposte-collaborazione-een/src/views/een-expr-of-interest/_pdf_footer', ['model' => $eenExpr]);


        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'content' => $content,
            'cssInline' => '',
            'options' => ['title' => ''],
            'methods' => [
                'SetFooter' => ['{PAGENO}']
            ],
        ]);

//        $pdf->getApi()->SetHTMLFooter($footer);
        $pdf->getApi()->SetMargins(0, 0, 20);
//        $pdf->getApi()->SetAutoPageBreak(TRUE, 25);
        $pdf->getApi()->margin_header = 6;
        $pdf->getApi()->margin_footer = 10;

        if (!empty($path)) {
            if (AmosSondaggi::instance()->enableAdvancedResultsDownload) {
                return $pdf->output($content, $path, Pdf::DEST_FILE);
            }
            $pdf->output($content, $path, Pdf::DEST_FILE);
            $fileSaved = \open20\amos\attachments\FileModule::instance()->attachFile($path, $modelSondaggioRisposta, 'questionario');
            return $fileSaved;
        } else {
            return $pdf->output($content, "Sondaggio_compilato.pdf", Pdf::DEST_DOWNLOAD);
        }
    }
}
