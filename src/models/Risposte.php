<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\models
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi\models;

use lispa\amos\core\user\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "datamart_file".
 */
class Risposte extends \yii\base\Model
{
    public $data_inizio;
    public $data_fine;
    public $tipologia = [
        0 => 'Pubblico',
        1 => 'Pubblico attivita singola',
        2 => 'Pubblico attivita multiple',
        3 => 'Riservato ruolo singolo',
        4 => 'Riservato ruoli multipli'
    ];
    public $colori = [
        2 => '#8ec44e',
        3 => '#f8b439',
        4 => '#3aa060',
        5 => '#ea5c6f',
        6 => '#0dc988',
        7 => '#53cfc4',
    ];
    public $attivita;
    public $area_formativa;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['data_inizio', 'data_fine'], 'safe'],
            [['attivita', 'area_formativa'], 'integer']
        ]);
    }

    public function getTipologia($id)
    {
        $pubblicazione = SondaggiPubblicazione::findOne(['sondaggi_id' => $id]);
        if ($pubblicazione) {
            if ($pubblicazione->ruolo == 'PUBBLICO' && $pubblicazione->tipologie_entita == 0) {
                return 0;
            } else if ($pubblicazione->ruolo == 'PUBBLICO' && $pubblicazione->tipologie_entita != 0) {
                $numero = SondaggiPubblicazione::find()->andWhere(['sondaggi_id' => $id])->count();
                if ($numero == 1) {
                    return 1;
                } else {
                    return 2;
                }
            } else {
                $numero = SondaggiPubblicazione::find()->andWhere(['sondaggi_id' => $id])->count();
                if ($numero == 1) {
                    return 3;
                } else {
                    return 4;
                }
            }
        } else {
            return -1;
        }
    }

    public function getDati($id, $idPagina)
    {
        set_time_limit(600);
        $sondaggio = Sondaggi::findOne($id);
        $tipologia = $this->getTipologia($id);
        $ritorno = [];
        $sql = "";
        $condizione = "";
        if ($this->data_inizio) {
            $condizione .= " AND DATE(S.begin_date) >= '$this->data_inizio'";
        }
        if ($this->data_fine) {
            $condizione .= " AND DATE(S.end_date) <= '$this->data_fine'";
        }

        if (!empty($this->attivita) && count($this->attivita)) {
            $arrAtt = "";
            $indc = 0;
            foreach ($this->attivita as $attID) {
                $arrAtt .= ($indc == 0) ? "$attID" : ", $attID";
                $indc++;
            }
            $condizione .= " AND S.entita_id IN ($arrAtt) ";
        }

        if (!empty($this->area_formativa) && count($this->area_formativa)) {
            $attivitaAll = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $id])
                ->innerJoin('pei_entita_formative', 'pei_entita_formative.id = sondaggi_risposte_sessioni.entita_id')
                ->innerJoin('pei_entita_formative_tag_mm', 'pei_entita_formative_tag_mm.entita_id = pei_entita_formative.id')
                ->innerJoin('tag', 'tag.id = pei_entita_formative_tag_mm.tag_id')
                ->andWhere(['IN', 'tag.id', $this->area_formativa])
                ->groupBy('pei_entita_formative.id')
                ->select('pei_entita_formative.id as id')
                ->asArray()
                ->all();

            $attivita = "";
            $ind = 0;
            foreach ($attivitaAll as $Att) {
                $attivita .= (($ind == 0) ? "" : ",") . $Att['id'];
                $ind++;
            }
            if (strlen($attivita) == 0) {
                $attivita = 'null';
            }
            $condizione .= " AND S.entita_id IN ($attivita)";
        }

        switch ($tipologia) {
            case -1:
                $ritorno[] = NULL;
                break;
            case 0:
                if ($idPagina == -1) {
                    $sql = "SELECT count(distinct(IF(S.end_date IS NOT NULL, S.id, null))) terminato,
                            count(distinct(IF(S.end_date IS NULL and R.id is null, S.id, null))) non_risposto, 
                            count(distinct(IF(S.user_id IS NOT NULL, S.id, null))) loggati, 
                            count(distinct(S.id)) accessi, 
                            count(distinct(IF(R.id is not null and S.end_date is null, S.id, null))) non_terminato 
                            FROM sondaggi_risposte_sessioni S 
                            LEFT JOIN sondaggi_risposte R ON S.id = R.sondaggi_risposte_sessioni_id 
                            WHERE S.sondaggi_id = $id
                            AND S.deleted_by is null AND R.deleted_by is null $condizione";

                    $command = Yii::$app->db->createCommand($sql);
                    $query = $command->queryAll();
                    if (count($query)) {
                        $ritorno[] = ['Rilevazioni', 'Numero dei partecipanti'];
                        foreach ($query as $Res) {
                            $ritorno[] = ['Accessi al sondaggio', floatval($Res['accessi'])];
                            $ritorno[] = ['Non hanno risposto ad alcuna domanda', floatval($Res['non_risposto'])];
                            $ritorno[] = ['Non hanno terminato il sondaggio', floatval($Res['non_terminato'])];
                            $ritorno[] = ['Hanno terminato il sondaggio', floatval($Res['terminato'])];
                            $ritorno[] = ['Utenti loggati', floatval($Res['loggati'])];
                        }
                    }
                } else if ($idPagina == 0) {
                    $domande = $this->getDomandeNonStatistiche($id);
                    $query = NULL;
                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {

                            $sql = "SELECT P.titolo pagina, P.descrizione descrizione, D.domanda domanda, R.risposta_libera risposta
                                    FROM sondaggi_risposte R
                                    INNER JOIN sondaggi_domande as D on D.id = R.sondaggi_domande_id
                                    INNER JOIN sondaggi_domande_pagine as P on P.id = D.sondaggi_domande_pagine_id
                                    INNER JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id
                                    WHERE D.id = $Domanda->id AND R.deleted_by is null AND P.deleted_by is null $condizione
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null
                                    GROUP BY risposta ORDER BY P.ordinamento";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();
                        }
                    }
                    $ritorno = new \yii\data\ArrayDataProvider([
                        'allModels' => $query,
                        'pagination' => FALSE
                    ]);
                } else {
                    $domande = $this->getDomandeStatistiche($id, $idPagina);
                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {

                            $sql = "SELECT distinct(P.risposta) risposta, count(distinct(R.id)) numero
                                    FROM sondaggi_risposte_predefinite P
                                    LEFT JOIN sondaggi_risposte R ON P.id = R.sondaggi_risposte_predefinite_id
                                    LEFT JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id
                                    WHERE P.sondaggi_domande_id = $Domanda->id AND R.deleted_by is null AND P.deleted_by is null
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null $condizione
                                    GROUP BY risposta ORDER BY P.ordinamento";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();
                            if (count($query)) {
                                $ritorno[$Domanda->id][] = ['Risposte', 'Numero occorrenze'];
                                foreach ($query as $Res) {
                                    $ritorno[$Domanda->id][] = [$Res['risposta'], floatval($Res['numero'])];
                                }
                            } else {
                                $ritorno[$Domanda->id] = NULL;
                            }
                        }
                    } else {
                        $ritorno[] = NULL;
                    }
                }
                break;
            case 1:
                $Area = SondaggiPubblicazione::find()->andWhere(['sondaggi_id' => $id])->select('tipologie_entita')->orderBy('tipologie_entita')->asArray()->one();

                if ($idPagina == -1) {
                    $attivitaAll = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $id])
                        ->innerJoin('pei_entita_formative', 'pei_entita_formative.id = sondaggi_risposte_sessioni.entita_id')
                        ->innerJoin('pei_entita_formative_tag_mm', 'pei_entita_formative_tag_mm.entita_id = pei_entita_formative.id')
                        ->innerJoin('tag', 'tag.id = pei_entita_formative_tag_mm.tag_id')
                        ->andWhere(['tag.id' => $Area['tipologie_entita']])
                        ->groupBy('pei_entita_formative.id')
                        ->select('pei_entita_formative.id as id')
                        ->asArray()
                        ->all();

                    $attivita = "";
                    $ind = 0;
                    foreach ($attivitaAll as $Att) {
                        $attivita .= (($ind == 0) ? "" : ",") . $Att['id'];
                        $ind++;
                    }

                    $sql = "SELECT count(distinct(IF(S.end_date IS NOT NULL, S.id, null))) terminato,
                                count(distinct(IF(S.end_date IS NULL and R.id is null, S.id, null))) non_risposto, 
                                count(distinct(IF(S.user_id IS NOT NULL, S.id, null))) loggati, 
                                count(distinct(S.id)) accessi, 
                                count(distinct(IF(R.id is not null and S.end_date is null, S.id, null))) non_terminato 
                                FROM sondaggi_risposte_sessioni S 
                                LEFT JOIN sondaggi_risposte R ON S.id = R.sondaggi_risposte_sessioni_id 
                                WHERE S.sondaggi_id = $id
                                AND S.deleted_by is null AND R.deleted_by is null 
                                AND S.entita_id IN ($attivita) $condizione";

                    $command = Yii::$app->db->createCommand($sql);
                    $query = $command->queryAll();

                    if (count($query)) {
                        $testoArea = \lispa\amos\tag\models\Tag::findOne(['id' => $Area['tipologie_entita']])->nome;
                        $ritorno[$Area['tipologie_entita']][] = ['Rilevazioni', 'Area formativa: ' . $testoArea, ['role' => 'style']];
                        foreach ($query as $Res) {
                            $ritorno[$Area['tipologie_entita']][] = ['Accessi al sondaggio', floatval($Res['accessi']), $this->colori[$Area['tipologie_entita']]];
                            $ritorno[$Area['tipologie_entita']][] = ['Non hanno risposto ad alcuna domanda', floatval($Res['non_risposto']), $this->colori[$Area['tipologie_entita']]];
                            $ritorno[$Area['tipologie_entita']][] = ['Non hanno terminato il sondaggio', floatval($Res['non_terminato']), $this->colori[$Area['tipologie_entita']]];
                            $ritorno[$Area['tipologie_entita']][] = ['Hanno terminato il sondaggio', floatval($Res['terminato']), $this->colori[$Area['tipologie_entita']]];
                            $ritorno[$Area['tipologie_entita']][] = ['Utenti loggati', floatval($Res['loggati']), $this->colori[$Area['tipologie_entita']]];
                        }
                    }
                } else if ($idPagina == 0) {
                    $domande = $this->getDomandeNonStatistiche($id);
                    $query = NULL;
                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {

                            $sql = "SELECT P.titolo pagina, P.descrizione descrizione, D.domanda domanda, R.risposta_libera risposta
                                    FROM sondaggi_risposte R
                                    INNER JOIN sondaggi_domande as D on D.id = R.sondaggi_domande_id
                                    INNER JOIN sondaggi_domande_pagine as P on P.id = D.sondaggi_domande_pagine_id
                                    INNER JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id
                                    WHERE D.id = $Domanda->id AND R.deleted_by is null AND P.deleted_by is null $condizione
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null
                                    GROUP BY risposta ORDER BY P.ordinamento";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();
                        }
                    }
                    $ritorno = new \yii\data\ArrayDataProvider([
                        'allModels' => $query,
                        'pagination' => FALSE
                    ]);
                } else {
                    $domande = $this->getDomandeStatistiche($id, $idPagina);

                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {

                            $sql = "SELECT distinct(P.risposta) risposta, count(distinct(R.id)) numero
                                    FROM sondaggi_risposte_predefinite P
                                    LEFT JOIN sondaggi_risposte R ON P.id = R.sondaggi_risposte_predefinite_id
                                    LEFT JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id
                                    WHERE P.sondaggi_domande_id = $Domanda->id AND R.deleted_by is null AND P.deleted_by is null $condizione
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null
                                    GROUP BY risposta ORDER BY P.ordinamento";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();
                            if (count($query)) {
                                $ritorno[$Domanda->id][] = ['Risposte', 'Numero occorrenze'];
                                foreach ($query as $Res) {
                                    $ritorno[$Domanda->id][] = [$Res['risposta'], floatval($Res['numero'])];
                                }
                            } else {
                                $ritorno[$Domanda->id] = NULL;
                            }
                        }
                    } else {
                        $ritorno[] = NULL;
                    }
                }
                break;
            case 2:
                $aree = SondaggiPubblicazione::find()->andWhere(['sondaggi_id' => $id])->select('tipologie_entita')->orderBy('tipologie_entita')->asArray()->all();

                if ($idPagina == -1) {
                    foreach ($aree as $Area) {
                        $attivitaAll = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $id])
                            ->innerJoin('pei_entita_formative', 'pei_entita_formative.id = sondaggi_risposte_sessioni.entita_id')
                            ->innerJoin('pei_entita_formative_tag_mm', 'pei_entita_formative_tag_mm.entita_id = pei_entita_formative.id')
                            ->innerJoin('tag', 'tag.id = pei_entita_formative_tag_mm.tag_id')
                            ->andWhere(['tag.id' => $Area['tipologie_entita']])
                            ->groupBy('pei_entita_formative.id')
                            ->select('pei_entita_formative.id as id')
                            ->asArray()
                            ->all();

                        $attivita = "";
                        $ind = 0;
                        foreach ($attivitaAll as $Att) {
                            $attivita .= (($ind == 0) ? "" : ",") . $Att['id'];
                            $ind++;
                        }

                        $sql = "SELECT count(distinct(IF(S.end_date IS NOT NULL, S.id, null))) terminato,
                                count(distinct(IF(S.end_date IS NULL and R.id is null, S.id, null))) non_risposto, 
                                count(distinct(IF(S.user_id IS NOT NULL, S.id, null))) loggati, 
                                count(distinct(S.id)) accessi, 
                                count(distinct(IF(R.id is not null and S.end_date is null, S.id, null))) non_terminato 
                                FROM sondaggi_risposte_sessioni S 
                                LEFT JOIN sondaggi_risposte R ON S.id = R.sondaggi_risposte_sessioni_id 
                                WHERE S.sondaggi_id = $id
                                AND S.deleted_by is null AND R.deleted_by is null
                                AND S.entita_id IN ($attivita) $condizione";

                        $command = Yii::$app->db->createCommand($sql);
                        $query = $command->queryAll();

                        if (count($query)) {
                            $testoArea = \backend\modules\tag\models\Tag::findOne(['id' => $Area['tipologie_entita']])->nome;
                            $ritorno[$Area['tipologie_entita']][] = ['Rilevazioni', 'Area formativa: ' . $testoArea, ['role' => 'style']];
                            foreach ($query as $Res) {
                                $ritorno[$Area['tipologie_entita']][] = ['Accessi al sondaggio', floatval($Res['accessi']), $this->colori[$Area['tipologie_entita']]];
                                $ritorno[$Area['tipologie_entita']][] = ['Non hanno risposto ad alcuna domanda', floatval($Res['non_risposto']), $this->colori[$Area['tipologie_entita']]];
                                $ritorno[$Area['tipologie_entita']][] = ['Non hanno terminato il sondaggio', floatval($Res['non_terminato']), $this->colori[$Area['tipologie_entita']]];
                                $ritorno[$Area['tipologie_entita']][] = ['Hanno terminato il sondaggio', floatval($Res['terminato']), $this->colori[$Area['tipologie_entita']]];
                                $ritorno[$Area['tipologie_entita']][] = ['Utenti loggati', floatval($Res['loggati']), $this->colori[$Area['tipologie_entita']]];
                            }
                        }
                    }
                } else if ($idPagina == 0) {
                    $domande = $this->getDomandeNonStatistiche($id);
                    $query = NULL;
                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {

                            $sql = "SELECT P.titolo pagina, P.descrizione descrizione, D.domanda domanda, R.risposta_libera risposta
                                    FROM sondaggi_risposte R
                                    INNER JOIN sondaggi_domande as D on D.id = R.sondaggi_domande_id
                                    INNER JOIN sondaggi_domande_pagine as P on P.id = D.sondaggi_domande_pagine_id
                                    INNER JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id
                                    WHERE D.id = $Domanda->id AND R.deleted_by is null AND P.deleted_by is null $condizione
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null
                                    GROUP BY risposta ORDER BY P.ordinamento";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();
                        }
                    }
                    $ritorno = new \yii\data\ArrayDataProvider([
                        'allModels' => $query,
                        'pagination' => FALSE
                    ]);
                } else {
                    $domande = $this->getDomandeStatistiche($id, $idPagina);

                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {

                            $sql = "SELECT distinct(P.risposta) risposta, count(distinct(R.id)) numero
                                    FROM sondaggi_risposte_predefinite P
                                    LEFT JOIN sondaggi_risposte R ON P.id = R.sondaggi_risposte_predefinite_id
                                    LEFT JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id
                                    WHERE P.sondaggi_domande_id = $Domanda->id AND R.deleted_by is null AND P.deleted_by is null $condizione
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null
                                    GROUP BY risposta ORDER BY P.ordinamento";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();
                            if (count($query)) {
                                $ritorno[$Domanda->id][] = ['Risposte', 'Numero occorrenze'];
                                foreach ($query as $Res) {
                                    $ritorno[$Domanda->id][] = [$Res['risposta'], floatval($Res['numero'])];
                                }
                            } else {
                                $ritorno[$Domanda->id] = NULL;
                            }
                        }
                    } else {
                        $ritorno[] = NULL;
                    }
                }
                break;
            case 3:
                $Ruolo = SondaggiPubblicazione::find()->andWhere(['sondaggi_id' => $id])->select('ruolo')->asArray()->one();

                //$users = User::find();
                //$allIdUser = [];
                $allIdUserStr = "";
                $allIdUser = \Yii::$app->authManager->getUserIdsByRole($Ruolo['ruolo']);
                /*foreach ($users->all() as $user) {
                    if (\Yii::$app->authManager->checkAccess($user->id, $Ruolo['ruolo'])) {
                        $allIdUser[] = $user->id;
                    }
                }*/
                $utentiAll = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $id])
                    ->andWhere(['IN', 'sondaggi_risposte_sessioni.user_id', $allIdUser])
                    ->asArray()
                    ->all();

                $utenti = "";
                $ind = 0;
                $ind2 = 0;

                if (count($utentiAll)) {
                    foreach ($utentiAll as $Utente) {
                        $utenti .= (($ind == 0) ? "" : ",") . $Utente['user_id'];
                        $ind++;
                    }
                }
                if (count($allIdUser)) {
                    foreach ($allIdUser as $usr) {
                        $allIdUserStr .= (($ind2 == 0) ? "" : ",") . $usr;
                        $ind2++;
                    }
                }
                if ($idPagina == -1) {

                    if (strlen($utenti)) {

                        $sql = "SELECT count(distinct(IF(S.end_date IS NOT NULL, S.id, null))) terminato,
                                count(distinct(IF(S.end_date IS NULL and R.id is null, S.id, null))) non_risposto,                                 
                                count(distinct(S.id)) accessi, 
                                count(distinct(IF(R.id is not null and S.end_date is null, S.id, null))) non_terminato 
                                FROM sondaggi_risposte_sessioni S 
                                LEFT JOIN sondaggi_risposte R ON S.id = R.sondaggi_risposte_sessioni_id 
                                INNER JOIN user_profile U ON U.user_id = S.user_id
                                WHERE S.sondaggi_id = $id
                                AND S.deleted_by is null AND R.deleted_by is null AND U.deleted_by is null AND S.user_id IS NOT NULL
                                AND S.user_id IN ($utenti) $condizione";

                        $command = Yii::$app->db->createCommand($sql);
                        $query = $command->queryAll();

                        if (count($query)) {
                            $dbRuolo = \mdm\admin\models\AuthItem::find($Ruolo['ruolo']);
                            if ($dbRuolo) {
                                $testoRuolo = $dbRuolo->description;
                            } else {
                                $testoRuolo = 'Iscritto';
                            }
                            $ritorno[$Ruolo['ruolo']][] = ['Rilevazioni', 'Ruolo: ' . $testoRuolo];
                            foreach ($query as $Res) {
                                $ritorno[$Ruolo['ruolo']][] = ['Accessi al sondaggio', floatval($Res['accessi'])];
                                $ritorno[$Ruolo['ruolo']][] = ['Non hanno risposto ad alcuna domanda', floatval($Res['non_risposto'])];
                                $ritorno[$Ruolo['ruolo']][] = ['Non hanno terminato il sondaggio', floatval($Res['non_terminato'])];
                                $ritorno[$Ruolo['ruolo']][] = ['Hanno terminato il sondaggio', floatval($Res['terminato'])];
                            }
                        }
                        if (\Yii::$app->controller->module->enableGeoChart) {
                            $sql = "SELECT IF(U.domicilio_provincia_id is not null, PR.nome, null) provincia,                                                             
                                count(distinct(S.id)) accessi, 
                                count(distinct(IF(R.id is not null and S.end_date is null, S.id, null))) iniziato 
                                FROM sondaggi_risposte_sessioni S 
                                LEFT JOIN sondaggi_risposte R ON S.id = R.sondaggi_risposte_sessioni_id 
                                INNER JOIN user_profile U ON U.user_id = S.user_id
                                LEFT JOIN istat_province PR on U.domicilio_provincia_id = PR.id
                                WHERE S.sondaggi_id = $id
                                AND S.deleted_by is null AND R.deleted_by is null AND U.deleted_by is null AND S.user_id IS NOT NULL"
                                . ((strlen($utenti)) ? " AND S.user_id IN ($utenti)" : "") . " $condizione                                
                                GROUP BY U.domicilio_provincia_id";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();

                            if (count($query)) {
                                $dbRuolo = \mdm\admin\models\AuthItem::find($Ruolo['ruolo']);
                                if ($dbRuolo) {
                                    $testoRuolo = $dbRuolo->description;
                                } else {
                                    $testoRuolo = 'Iscritto';
                                }

                                $ritorno['provincia'][] = ['Provincia', 'Accessi al sondaggio', 'Hanno risposto almeno ad una domanda'];
                                foreach ($query as $Res) {
                                    $ritorno['provincia'][] = [$Res['provincia'], floatval($Res['accessi']), floatval($Res['iniziato'])];
                                }
                            }
                        }
                        if (\Yii::$app->controller->module->enablePartecipantsReport) {
                            $allUserIdStr = "";

                            $sql = "SELECT distinct(U.id) id, U.cognome cognome, U.nome nome, USR.email email, USR.username username, U.telefono telefono, " .
                                (!empty(\Yii::$app->controller->module->fieldsByPartecipants) ? (implode(',', \Yii::$app->controller->module->fieldsByPartecipants) . ',') : '') .
                                "IF(S.end_date is not null, 'terminato', IF(R.id is not null and S.end_date is null, 'iniziato', IF(S.id is not null, 'visualizzato', 'nessun accesso'))) stato,
                                    IF(S.end_date is not null, S.end_date, null) end_date,
                                    IF(S.begin_date is not null, S.begin_date, null) begin_date
                                    FROM user_profile U   
                                    INNER JOIN user USR ON USR.id = U.user_id
                                    LEFT JOIN sondaggi_risposte_sessioni S ON U.user_id = S.user_id
                                    LEFT JOIN sondaggi_risposte R ON S.id = R.sondaggi_risposte_sessioni_id                                                    
                                    WHERE (S.sondaggi_id = $id OR S.sondaggi_id is null)
                                    AND S.deleted_by is null AND R.deleted_by is null AND U.deleted_by is null AND U.user_id IS NOT NULL"
                                . ((strlen($allIdUserStr)) ? " AND U.user_id IN ($allIdUserStr)" : "") . " $condizione
                                    ORDER BY FIELD(stato, 'non visualizzato', 'visualizzato', 'iniziato', 'terminato'), cognome, nome;";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();

                            if (count($query)) {
                                $dbRuolo = \mdm\admin\models\AuthItem::find($Ruolo['ruolo']);
                                if ($dbRuolo) {
                                    $testoRuolo = $dbRuolo->description;
                                } else {
                                    $testoRuolo = 'Iscritto';
                                }

                                if (!empty(\Yii::$app->controller->module->fieldsByPartecipants)) {
                                    //TO-DO
                                    $ritorno['partecipants'][] = ['Cognome', 'Nome', 'Stato', 'Data inizio', 'Data fine'];
                                    foreach ($query as $Res) {
                                        $ritorno['partecipants'][$Res['id']] = ['cognome' => $Res['cognome'], 'nome' => $Res['nome'], 'stato' => $Res['stato'], 'begin_date' => $Res['begin_date'], 'end_date' => $Res['end_date'], 'email' => $Res['email'], 'username' => $Res['username'], 'telefono' => $Res['telefono'], 'descRole' => $testoRuolo, 'role' => $Ruolo['ruolo']];
                                    }
                                } else {
                                    $ritorno['partecipants'][] = ['Cognome', 'Nome', 'Stato', 'Data inizio', 'Data fine'];
                                    foreach ($query as $Res) {
                                        $ritorno['partecipants'][$Res['id']] = ['cognome' => $Res['cognome'], 'nome' => $Res['nome'], 'stato' => $Res['stato'], 'begin_date' => $Res['begin_date'], 'end_date' => $Res['end_date'], 'email' => $Res['email'], 'username' => $Res['username'], 'telefono' => $Res['telefono'], 'descRole' => $testoRuolo, 'role' => $Ruolo['ruolo']];
                                    }
                                }
                            }
                        }
                    }
                } else if ($idPagina == 0) {
                    $domande = $this->getDomandeNonStatistiche($id);
                    $query = NULL;
                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {

                            $sql = "SELECT P.titolo pagina, P.descrizione descrizione, D.domanda domanda, R.risposta_libera risposta
                                    FROM sondaggi_risposte R
                                    INNER JOIN sondaggi_domande as D on D.id = R.sondaggi_domande_id
                                    INNER JOIN sondaggi_domande_pagine as P on P.id = D.sondaggi_domande_pagine_id                                   
                                    INNER JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id                                    
                                    WHERE D.id = $Domanda->id AND R.deleted_by is null AND P.deleted_by is null $condizione
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null
                                    GROUP BY risposta ORDER BY P.ordinamento";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();
                        }
                    }
                    $ritorno = new \yii\data\ArrayDataProvider([
                        'allModels' => $query,
                        'pagination' => FALSE
                    ]);
                } else {
                    $domande = $this->getDomandeStatistiche($id, $idPagina);

                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {
                            if (strlen($utenti) > 0) {
                                $sql = "SELECT distinct(P.risposta) risposta, count(distinct(R.id)) numero
                                    FROM sondaggi_risposte_predefinite P
                                    LEFT JOIN sondaggi_risposte R ON P.id = R.sondaggi_risposte_predefinite_id
                                    LEFT JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id
                                    LEFT JOIN user_profile U ON S.user_id = U.user_id 
                                    LEFT JOIN auth_assignment A ON U.user_id = A.user_id 
                                    WHERE P.sondaggi_domande_id = $Domanda->id AND (S.user_id IN ($utenti) OR S.user_id is null) $condizione
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null AND U.deleted_by is null
                                    GROUP BY risposta ORDER BY P.ordinamento";

                                $command = Yii::$app->db->createCommand($sql);
                                $query = $command->queryAll();
                                if (count($query)) {
                                    $ritorno[$Domanda->id][] = ['Risposte', 'Numero occorrenze'];
                                    foreach ($query as $Res) {
                                        $ritorno[$Domanda->id][] = [$Res['risposta'], floatval($Res['numero'])];
                                    }
                                } else {
                                    $ritorno[$Domanda->id] = NULL;
                                }
                            } else {
                                $ritorno[$Domanda->id] = NULL;
                            }
                        }
                    } else {
                        $ritorno[] = NULL;
                    }
                }
                break;
            case 4:
                $ruoli = SondaggiPubblicazione::find()->andWhere(['sondaggi_id' => $id])->select('ruolo')->orderBy('ruolo')->asArray()->all();

                if ($idPagina == -1) {
                    $indice = 0;
                    foreach ($ruoli as $Ruolo) {
                        //$users = User::find();
                        //$allIdUser = [];
                        /*foreach ($users->all() as $user) {
                            if (\Yii::$app->authManager->checkAccess($user->id, $Ruolo['ruolo'])) {
                                $allIdUser[] = $user->id;
                            }
                        }*/

                        $allIdUser = \Yii::$app->authManager->getUserIdsByRole($Ruolo['ruolo']);
                        $utentiAll = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $id])
                            ->andWhere(['IN', 'sondaggi_risposte_sessioni.user_id', $allIdUser])
                            ->asArray()
                            ->all();

                        $utenti = "";
                        $ind = 0;
                        foreach ($utentiAll as $Utente) {
                            $utenti .= (($ind == 0) ? "" : ",") . $Utente['user_id'];
                            $ind++;
                        }

                        if (strlen($utenti)) {
                            $sql = "SELECT count(distinct(IF(S.end_date IS NOT NULL, S.id, null))) terminato,
                                count(distinct(IF(S.end_date IS NULL and R.id is null, S.id, null))) non_risposto,                                 
                                count(distinct(S.id)) accessi, 
                                count(distinct(IF(R.id is not null and S.end_date is null, S.id, null))) non_terminato 
                                FROM sondaggi_risposte_sessioni S 
                                LEFT JOIN sondaggi_risposte R ON S.id = R.sondaggi_risposte_sessioni_id 
                                INNER JOIN user_profile U ON U.user_id = S.user_id
                                WHERE S.sondaggi_id = $id
                                AND S.deleted_by is null AND R.deleted_by is null AND U.deleted_by is null AND S.user_id IS NOT NULL
                                AND S.user_id IN ($utenti) $condizione";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();

                            if (count($query)) {
                                $dbRuolo = Yii::$app->authManager->getRole($Ruolo['ruolo']);
                                if ($dbRuolo) {
                                    $testoRuolo = $dbRuolo->name;
                                } else {
                                    $testoRuolo = 'Iscritto';
                                }
                                $ritorno[$Ruolo['ruolo']][] = ['Rilevazioni', 'Ruolo: ' . $testoRuolo];
                                foreach ($query as $Res) {
                                    $ritorno[$Ruolo['ruolo']][] = ['Accessi al sondaggio', floatval($Res['accessi'])];
                                    $ritorno[$Ruolo['ruolo']][] = ['Non hanno risposto ad alcuna domanda', floatval($Res['non_risposto'])];
                                    $ritorno[$Ruolo['ruolo']][] = ['Non hanno terminato il sondaggio', floatval($Res['non_terminato'])];
                                    $ritorno[$Ruolo['ruolo']][] = ['Hanno terminato il sondaggio', floatval($Res['terminato'])];
                                }
                            }
                        }

                        if (\Yii::$app->controller->module->enableGeoChart) {

                            $sql = "SELECT IF(U.domicilio_provincia_id is not null, PR.nome, null) provincia,                                                             
                                count(distinct(S.id)) accessi, 
                                count(distinct(IF(R.id is not null and S.end_date is null, S.id, null))) iniziato 
                                FROM sondaggi_risposte_sessioni S 
                                LEFT JOIN sondaggi_risposte R ON S.id = R.sondaggi_risposte_sessioni_id 
                                INNER JOIN user_profile U ON U.user_id = S.user_id
                                LEFT JOIN istat_province PR on U.domicilio_provincia_id = PR.id
                                WHERE S.sondaggi_id = $id
                                AND S.deleted_by is null AND R.deleted_by is null AND U.deleted_by is null AND S.user_id IS NOT NULL"
                                . ((strlen($utenti)) ? " AND S.user_id IN ($utenti)" : "") . " $condizione
                                GROUP BY U.domicilio_provincia_id";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();

                            if (count($query)) {
                                $dbRuolo = Yii::$app->authManager->getRole($Ruolo['ruolo']);
                                if ($dbRuolo) {
                                    $testoRuolo = $dbRuolo->name;
                                } else {
                                    $testoRuolo = 'Iscritto';
                                }

                                $ritorno['provincia' . $indice][] = ['Provincia', 'Accessi al sondaggio', 'Hanno risposto almeno ad una domanda'];
                                foreach ($query as $Res) {
                                    $ritorno['provincia' . $indice][] = [$Res['provincia'], floatval($Res['accessi']), floatval($Res['iniziato'])];
                                }
                            }
                        }
                        $indice++;
                    }
                } else if ($idPagina == 0) {
                    $domande = $this->getDomandeNonStatistiche($id);
                    $query = NULL;
                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {

                            $sql = "SELECT P.titolo pagina, P.descrizione descrizione, D.domanda domanda, R.risposta_libera risposta
                                    FROM sondaggi_risposte R
                                    INNER JOIN sondaggi_domande as D on D.id = R.sondaggi_domande_id
                                    INNER JOIN sondaggi_domande_pagine as P on P.id = D.sondaggi_domande_pagine_id                                   
                                    INNER JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id                                    
                                    WHERE D.id = $Domanda->id AND R.deleted_by is null AND P.deleted_by is null $condizione
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null
                                    GROUP BY risposta ORDER BY P.ordinamento";

                            $command = Yii::$app->db->createCommand($sql);
                            $query = $command->queryAll();
                        }
                    }
                    $ritorno = new \yii\data\ArrayDataProvider([
                        'allModels' => $query,
                        'pagination' => FALSE
                    ]);
                } else {
                    $domande = $this->getDomandeStatistiche($id, $idPagina);

                    $Ruoli = "";
                    $ind = 0;
                    foreach ($ruoli as $Ruolo) {
                        (($ind == 0) ? "" : ",") . "'" . $Ruolo['ruolo'] . "'";
                    }

                    $users = User::find();
                    $allIdUser = [];
                    foreach ($users->all() as $user) {
                        foreach ($ruoli as $singleRole){
                            if (\Yii::$app->authManager->checkAccess($user->id, $singleRole['ruolo'])) {
                                $allIdUser[] = $user->id;
                                continue;
                            }
                        }
                    }
                    $utentiAll = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $id])
                        ->andWhere(['IN', 'sondaggi_risposte_sessioni.user_id', $allIdUser])
                        ->asArray()
                        ->all();

                    $utenti = "";
                    $ind = 0;
                    foreach ($utentiAll as $Utente) {
                        $utenti .= (($ind == 0) ? "" : ",") . $Utente['user_id'];
                        $ind++;
                    }

                    if ($domande->count()) {
                        foreach ($domande->all() as $Domanda) {
                            if (strlen($utenti)) {
                                $sql = "SELECT distinct(P.risposta) risposta, count(distinct(R.id)) numero
                                    FROM sondaggi_risposte_predefinite P
                                    LEFT JOIN sondaggi_risposte R ON P.id = R.sondaggi_risposte_predefinite_id
                                    LEFT JOIN sondaggi_risposte_sessioni S ON R.sondaggi_risposte_sessioni_id = S.id
                                    WHERE P.sondaggi_domande_id = $Domanda->id AND (S.user_id IN ($utenti) OR S.user_id is null) $condizione
                                    AND S.deleted_by is null AND R.deleted_by is null AND P.deleted_by is null
                                    GROUP BY risposta ORDER BY P.ordinamento";

                                $command = Yii::$app->db->createCommand($sql);
                                $query = $command->queryAll();
                                if (count($query)) {
                                    $ritorno[$Domanda->id][] = ['Risposte', 'Numero occorrenze'];
                                    foreach ($query as $Res) {
                                        $ritorno[$Domanda->id][] = [$Res['risposta'], floatval($Res['numero'])];
                                    }
                                } else {
                                    $ritorno[$Domanda->id] = NULL;
                                }
                            } else {
                                $ritorno[$Domanda->id] = NULL;
                            }
                        }
                    } else {
                        $ritorno[] = NULL;
                    }
                }
                break;
        }

        return $ritorno;
    }

    public function getDomandeStatistiche($id, $idPagina)
    {
        return SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $idPagina])->andWhere(['IN', 'sondaggi_domande_tipologie_id', [1, 2, 3, 4]])->orderBy('ordinamento ASC');
    }

    public function getDomandeNonStatistiche($id)
    {
        return SondaggiDomande::find()->andWhere(['sondaggi_id' => $id])->andWhere(['NOT IN', 'sondaggi_domande_tipologie_id', [1, 2, 3, 4]])->orderBy('ordinamento ASC');
    }
}
