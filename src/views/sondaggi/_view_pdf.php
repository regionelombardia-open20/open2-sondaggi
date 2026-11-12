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

/** @var  $sondaggio \open20\amos\sondaggi\models\Sondaggi */

$sondaggiModule = \open20\amos\sondaggi\AmosSondaggi::instance();
$header = $data[0];
$risposte = $data[1];
$i = 1;
?>
<h2><?= $sondaggio->titolo ?></h2>
<br>

<?php foreach ($risposte as $key => $risposta){
    if ($key == $divider) echo "<hr>";
?>
    <?php
    if ( !empty($header[$key]) || !empty($risposta) ):
        ?>
        <label><strong><?= $header[$key]?></strong></label>
        <p><?= $risposta?></p>
        <?php
    endif;
    ?>
    <?php if ($key == 2) {
        if ($sondaggiModule->startEndPdfCompleteDate) {
            echo "<label><strong>Iniziato il</strong>";
            echo "<p>" . (!empty($rispostaModel->begin_date) ? \Yii::$app->formatter->asDatetime($rispostaModel->begin_date, 'humanalwaysdatetime') : '') . "</p>";
            echo "<label><strong>Completato il</strong>";
            echo "<p>" . (!empty($rispostaModel->end_date) ? \Yii::$app->formatter->asDatetime($rispostaModel->end_date, 'humanalwaysdatetime') : '') . "</p>";
        } else {
            echo "<label><strong>Iniziato il</strong>";
            echo "<p>" . (!empty($rispostaModel->begin_date) ? \Yii::$app->formatter->asDate($rispostaModel->begin_date) : '') . "</p>";
            echo "<label><strong>Completato il</strong>";
            echo "<p>" . (!empty($rispostaModel->end_date) ? \Yii::$app->formatter->asDate($rispostaModel->end_date) : '') . "</p>";
        }
    } ?>
<?php } ?>
