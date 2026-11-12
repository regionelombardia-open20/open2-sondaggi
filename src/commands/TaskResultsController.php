<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\quiz\commands
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

namespace open20\amos\sondaggi\commands;

use open20\amos\admin\AmosAdmin;
use open20\amos\comuni\models\base\IstatComuni;
use open20\amos\comuni\models\base\IstatProvince;
use open20\amos\core\utilities\SpreadSheetFactory;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiDomande;
use open20\amos\sondaggi\models\SondaggiRisposte;
use open20\amos\sondaggi\models\SondaggiRispostePredefinite;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;
use open20\amos\sondaggi\models\SondaggiRisultatiFormato;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseInflector;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use yii\log\Logger;
use Yii;
use open20\amos\admin\models\UserProfile;

/**
 * Class GenerateResultsController
 * GenerateResultsController extends \yii\console\Controller
 */
class TaskResultsController extends \yii\console\Controller
{
    const TYPE_EXCEL = 'excel';
    const TYPE_PDF = 'pdf';

    /**
     * @var
     */
    public $data;

    protected $filename;

    /**
     * @var Sondaggi $model
     */
    protected $model;

    /**
     * @var AmosSondaggi $model
     */
    protected $sondaggiModule;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'data',
            'filename',
        ]);
    }

    /**
     *  $this->data:
     *      'sondaggio_id' => number
     *      'format_id' => number
     *      'excel_rows' => number/null
     *      'only_completed' => false/true
     */
    public function actionRun()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        try {
            $traceDateTimeStr = '[' . date_format(new \DateTime('now'), 'Y-m-d H:i:s') . ']: ';
            Console::stdout($traceDateTimeStr . 'INIZIO estrazione risultati, dati task:' . PHP_EOL);
            Console::stdout(VarDumper::dumpAsString($this->data) . PHP_EOL);


            $this->model = Sondaggi::findOne(['id' => $this->data['sondaggio_id']]);
            $this->sondaggiModule = Yii::$app->getModule('sondaggi');
            $xlsData = [];
            $basePath = \Yii::getAlias('@vendor/../common/uploads/temp');
            $zipAttachs = [];

            // se abilita_registrazione == 1 allora i tre campi vanno visualizzati, se no, vale il parametro statisticExtractDisableNameSurnameEmail
            $viewNameSurnameEmail = $this->model->abilita_registrazione || !AmosSondaggi::instance()->statisticExtractDisableNameSurnameEmail;

            $isCommunityManager = false;
            if (!empty(\Yii::$app->getModule('community'))) {
                $isCommunityManager = \open20\amos\community\utilities\CommunityUtil::isLoggedCommunityManager();
            }

            if ($this->sondaggiModule->enableExportBeforeClosing == false && ($this->model->status != Sondaggi::WORKFLOW_STATUS_VALIDATO || $this->model->close_date >= date('Y-m-d'))) {
                throw new \Exception('Sondaggio non pubblicato o non terminato');
            }

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
            $pagine = $this->model->getSondaggiDomandePagines()->orderBy('sondaggi_domande_pagine.ordinamento');
            foreach ($pagine->all() as $pagina) {
                $domandePagina = $pagina->getSondaggiDomandes()->andWhere(['parent_id' => null])->orderBy('ordinamento ASC')->all();
                foreach ($domandePagina as $domandaPag) {
                    $domande[] = $domandaPag;
                }
            }
            //$domande         = $model->getSondaggiDomandes()->orderBy('ordinamento ASC')->all();
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
                $domCountString = "D." . $count . " " . $domanda->domanda;
                if (isset($domanda->immagine) &&$domanda->immagine) {
                    $domCountString = "D." . $count . " " . $domanda->domanda . " (". basename($domanda->immagine->getPath()) .")";
                }
                if (in_array($domanda->sondaggi_domande_tipologie_id, [10, 11])) {
                    $xlsData[0][] = $domCountString;
                    $colRispAllegati[$domanda->id] = $totCount;
                    $totCount++;
                } else if (in_array($domanda->sondaggi_domande_tipologie_id, [5, 6, 13, 15, 17])) {
                    if ($domanda->is_parent) {
                        $childs = $domanda->childs;
                        foreach ($childs as $child) {
                            $xlsData[0][] = $domCountString . " \n" . $child->domanda;
                            $colRispLibere[$child->id] = $totCount;
                            $totCount++;
                        }
                    } else {
                        $xlsData[0][] = $domCountString;
                        $colRispLibere[$domanda->id] = $totCount;
                        $totCount++;
                    }
                } else {
                    if (!empty($countRisposte) && in_array($domanda->sondaggi_domande_tipologie_id, [1, 2, 3, 4])) {
                        if ($domanda->is_parent) {
                            $childs = $domanda->childs;
                            foreach ($childs as $ch) {
                                foreach ($rispostePredefinite->orderBy('ordinamento ASC')->all() as $rispPre) {
                                    $xlsData[0][] = $domCountString . " \n" . $ch->domanda . " \nR." . $localCount . " " . $rispPre->risposta;
                                    $colRisp[$rispPre->id][$ch->id] = $totCount;
                                    $localCount++;
                                    $totCount++;
                                }
                            }
                        } else {
                            foreach ($rispostePredefinite->orderBy('ordinamento ASC')->all() as $rispPre) {
                                $xlsData[0][] = $domCountString . " \nR." . $localCount . " " . $rispPre->risposta;
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
            $query = SondaggiRisposteSessioni::find()
                ->distinct()
                ->innerJoin('sondaggi_risposte',
                    'sondaggi_risposte_sessioni.id = sondaggi_risposte.sondaggi_risposte_sessioni_id')
                ->leftJoin('user_profile', 'user_profile.user_id = sondaggi_risposte_sessioni.user_id')
                ->leftJoin('user', 'user_profile.user_id = user.id')
                ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $this->model->id])
                ->orderBy('sondaggi_risposte_sessioni.begin_date')
            ;

            if (isset($this->data['only_completed']) && $this->data['only_completed']) {
                $query->andWhere(['IS NOT', 'end_date', null]);
            }

            $sondaggiRisposte = $query->all();
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
                        $xlsData [$row][0 + $offset] = ($this->model->abilita_criteri_valutazione == 1 ? AmosSondaggi::t('amossondaggi',
                            'L\'utente non ha effettuato la registrazione') : AmosSondaggi::t('amossondaggi',
                            'L\'utente non è stato registrato'));
                        $xlsData [$row][1 + $offset] = ($this->model->abilita_criteri_valutazione == 1 ? AmosSondaggi::t('amossondaggi',
                            'L\'utente non ha effettuato la registrazione') : AmosSondaggi::t('amossondaggi',
                            'L\'utente non è stato registrato'));
                        $xlsData [$row][2 + $offset] = ($this->model->abilita_criteri_valutazione == 1 ? AmosSondaggi::t('amossondaggi',
                            'L\'utente non ha effettuato la registrazione') : AmosSondaggi::t('amossondaggi',
                            'L\'utente non è stato registrato'));
                    }
                } else {
                    if ($viewNameSurnameEmail) {
                        $xlsData [$row][0 + $offset] = $profile->nome;
                        $xlsData [$row][1 + $offset] = $profile->cognome;
                        $xlsData [$row][2 + $offset] = $profile->user->email;
                    }
                }

                if (AmosSondaggi::instance()->compilationToOrganization) {
                    $profilo = \open20\amos\organizzazioni\models\Profilo::find()->andWhere(['id' => $sondRisposta->organization_id])->one();
                    $xlsData [$row][3 + $offset] = $profilo->name;
                    $xlsData [$row][4 + $offset] = $sondRisposta->begin_date;
                    $xlsData [$row][5 + $offset] = $sondRisposta->end_date;
                    if (AmosSondaggi::instance()->enableCompilationWorkflow) {
                        $xlsData[$row][6 + $offset] = AmosSondaggi::t('amossondaggi', $sondRisposta->status);
                    }
                } else {
                    $xlsData [$row][3 + $offset] = $sondRisposta->begin_date;
                    $xlsData [$row][4 + $offset] = $sondRisposta->end_date;
                    if (AmosSondaggi::instance()->enableCompilationWorkflow) {
                        $xlsData[$row][5 + $offset] = AmosSondaggi::t('amossondaggi', $sondRisposta->status);
                    }
                }
                $session_id = $sondRisposta->id;

                /** @var  $domanda SondaggiDomande */
                foreach ($domande as $domanda) {

                    $query = $domanda->getRispostePerUtente((empty($profile) ? null : $profile->user_id), $session_id);
                    // RISPOSTE LIBERE
                    if (in_array($domanda->sondaggi_domande_tipologie_id, [5, 6, 13, 15, 17])) {
                        foreach ($query->asArray()->all() as $risposta) {
                            if ($risposta) {
                                $rispostaFormattata = '';
                                if (!empty($risposta['risposta_libera'])) {
                                    $rispostaFormattata = $risposta['risposta_libera'];
                                }
                                $idDomandaSpecifica = $risposta['sondaggi_domande_id'];

                                if ($domanda->sondaggi_domande_tipologie_id == 15) { // PROVINCIA
                                    $provincia = \open20\amos\comuni\models\IstatProvince::find()->andWhere(['id' => $risposta['risposta_libera']])->asArray()->one();
                                    $rispostaFormattata = empty($provincia)? '': $provincia['nome'];
                                }
                                if ($domanda->sondaggi_domande_tipologie_id == 17) { // COMUNE
                                    $comune = \open20\amos\comuni\models\IstatComuni::find()->andWhere(['id' => $risposta['risposta_libera']])->asArray()->one();
                                    $rispostaFormattata = empty($comune)? '': $comune['nome'];
                                }

                                if (isset($colRispLibere[$idDomandaSpecifica])) {
                                    $xlsData[$row][$colRispLibere[$idDomandaSpecifica] + $offset] = $rispostaFormattata;
                                }
                            }
                        }
                        //ALLEGATI
                    } else if ($domanda->sondaggi_domande_tipologie_id == 10 || $domanda->sondaggi_domande_tipologie_id == 11) {
                        $risposta = $query->one();
                        if ($risposta) {
                            $attribute = 'domanda_' . $domanda->id;
                            if (!empty($risposta->$attribute)) {
                                $attachments = $risposta->getFiles();
                                $listAttachUrls = [];
                                foreach ($attachments as $attach) {
                                    if (!empty($profile)) {
                                        $folder = $sondRisposta->id . '_' . BaseInflector::slug($profile->cognome . ' ' . $profile->nome);
                                    } else {
                                        $folder = $sondRisposta->id . '_' . BaseInflector::slug('user not defined ' . time());
                                    }

                                    if (AmosSondaggi::instance()->compilationToOrganization) {
                                        $profilo = \open20\amos\organizzazioni\models\Profilo::find()->andWhere(['id' => $sondRisposta->organization_id])->one();
                                        $folder = $sondRisposta->id . '_' . BaseInflector::slug($profilo->name);
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
                            $srpId = $risposta['sondaggi_risposte_predefinite_id'] ?? null;

                            if (!empty($srpId) && isset($sondRispPredefAll[$srpId])) {
                                $srp = $sondRispPredefAll[$srpId];

                                if (!empty($srp)) {
                                    if ($domanda->is_parent) {
                                        if (empty($srp['code'])) {

                                            $xlsData[$row][$colRisp[$srp['id']][$risposta['sondaggi_domande_id']]
                                            + $offset] = $srp['risposta'];
                                        } else {
                                            $xlsData[$row][$colRisp[$srp['id']][$risposta['sondaggi_domande_id']]
                                            + $offset] = $srp['code'];
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
                }
                $row++;
                gc_collect_cycles();
            }

            // $zip_filepath = $basePath.'/Risposte_sondaggio_'.$this->model->id.'.zip';
            if ($this->data['format_id'] == SondaggiRisultatiFormato::FORMAT_EXCEL) {
                $this->excelElaboration($xlsData, $basePath, $this->model->id, $totCount, $offset, $row, $zipAttachs);
            }

            if ($this->data['format_id'] == SondaggiRisultatiFormato::FORMAT_PDF) {
                $this->pdfElaboration($basePath, $zipAttachs);
            }

            $traceDateTimeStr = '[' . date_format(new \DateTime('now'), 'Y-m-d H:i:s') . ']: ';
            Console::stdout($traceDateTimeStr . 'FINE estazione risultati' . PHP_EOL);
        } catch (\Exception $ex) {
            Console::stdout($ex->getMessage() . PHP_EOL);
            Yii::getLogger()->log($ex->getMessage() . PHP_EOL . $ex->getTraceAsString(), Logger::LEVEL_ERROR);
            return 1;
        }

        return 0;
    }

    /**
     * @param $basePath
     * @param $id
     * @param $totCount
     * @param $offset
     * @param $row
     * @param $zipAttachs
     * @return void
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    private function excelElaboration($xlsData, $basePath, $id, $totCount, $offset, $row, $zipAttachs)
    {
        $maxNumOfRows = $this->data['excel_rows'];
        $lastColumn = $totCount + $offset;

        for ($i = 1; $i <= $row; $i++) {
            for ($c = 0; $c <= $lastColumn; $c++) {
                if (empty($xlsData[$i]) || !array_key_exists($c, $xlsData[$i])) {
                    $xlsData[$i][$c] = '';
                }
            }
        }

        foreach ($xlsData as $key => $value) {
            ksort($xlsData[$key]);
        }

        $numOfFiles = round(($row / $maxNumOfRows),0,PHP_ROUND_HALF_DOWN) + (($row % $maxNumOfRows)? 1: 0);
        for ($i =0; $i < $numOfFiles; $i++) {
            $objPHPExcel = new Spreadsheet();
            // set Style first row
            $lastColumnLetter = Coordinate::stringFromColumnIndex($lastColumn);

            $objPHPExcel->getActiveSheet()->getStyle('A1:' . $lastColumnLetter . '1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('C0C0C0');

            $nomeFile = $basePath . '/Risposte_sondaggio_' . $id . '_parte_' . ($i + 1) . '.xls';

            $from = $maxNumOfRows * $i;
            $xlsDataPart = [];
            // titoli
            $xlsDataPart = array_slice($xlsData, 0, 1, true);
            // dati
            $xlsDataPart = ArrayHelper::merge($xlsDataPart, array_slice($xlsData, $from + 1, $maxNumOfRows, true));

            //li pone nella tab attuale del file xls
            $objPHPExcel->getActiveSheet()->fromArray($xlsDataPart, NULL, 'A1');
            $objWriter = SpreadSheetFactory::createWriter($objPHPExcel, 'Xlsx');
            $objWriter->save($nomeFile);
            /** @var \ZipArchive $zip */
            $zip = new \ZipArchive();
            if ($zip->open($this->filename, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Cannot create a zip file');
            }
            $zip->addFile($nomeFile, basename($nomeFile));
            foreach ($zipAttachs as $name => $file) {
                $zip->addFile($file, $name);
            }
            $zip->close();
        }
    }

    private function pdfElaboration($basePath, $zipAttachs)
    {
        if (empty($this->model->id)) throw new \Exception('Sondaggio non settato');
        if (empty($basePath)) throw new \Exception('BasePath non settato');
        $dir = $basePath . '/attachments/' . $this->model->id . '/';
        if (is_dir($dir)) {
            array_map('unlink', glob("$dir*.*"));
            rmdir($dir);
        }
        mkdir($dir, 0775, true);

        /** @var \ZipArchive $zip */
        $zip = new \ZipArchive();

        if ($zip->open($this->filename, \ZipArchive::CREATE) !== true) {
            throw new \Exception('Cannot create a zip file');
        }

        $query = $this->model->getSondaggiRisposteSessionis();
        $query->distinct();
        $query->innerJoin('sondaggi_risposte',
            'sondaggi_risposte_sessioni.id = sondaggi_risposte.sondaggi_risposte_sessioni_id');
        $query->orderBy('sondaggi_risposte_sessioni.begin_date');
        $query->distinct();

        if (isset($this->data['only_completed']) && $this->data['only_completed']) {
            $query->andWhere(['IS NOT', 'end_date', null]);
        }

        foreach ($query->all() as $sessione) {
            if (!empty($profile) && !AmosSondaggi::instance()->forceOnlyFrontend) {
                $folder = $sessione->id . '_' .BaseInflector::slug($profile->cognome . ' ' . $profile->nome);
            } else {
                $folder = $sessione->id . '_' .BaseInflector::slug($sessione->begin_date . ' sondaggio');
            }


            if (AmosSondaggi::instance()->compilationToOrganization) {
                $folder = $sessione->id . '_' .BaseInflector::slug($sessione->organization->name);
            }
            $pdfPath = $basePath . '/attachments/' . $this->model->id . '/' . $folder . '.pdf';
            $ret=$sessione->generateSondaggiPdf($pdfPath);
            if (file_exists($pdfPath)) {
                $zip->addFile($pdfPath, basename($pdfPath));
            }
        }

        // Attachments
        foreach ($zipAttachs as $name => $file) {
            $zip->addFile($file, $name);
        }

        $zip->close();
    }
}