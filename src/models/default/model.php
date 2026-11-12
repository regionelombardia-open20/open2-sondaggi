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

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
use open20\amos\sondaggi\models\SondaggiDomandePagine;
use open20\amos\sondaggi\models\SondaggiDomandeTipologie;
use open20\amos\sondaggi\models\SondaggiDomandeCondizionate;
use open20\amos\sondaggi\models\SondaggiRispostePredefinite;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiDomande;
use open20\amos\sondaggi\models\SondaggiRisposte;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;
use open20\amos\sondaggi\models\SondaggiStato;
use open20\amos\sondaggi\AmosSondaggi;
use yii\web\UploadedFile;
use open20\amos\attachments\models\File;



/**
* Questa è la classe base per la pagina "<?= $generator->paginaSondaggio ?>" del sondaggio "<?= $generator->titoloSondaggio ?>".
*
<?php /* foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
  <?php endforeach; */ ?>

*/
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{

public $session_id;
public $read = false;

<?php foreach ($attributi as $attributo): ?>
    <?= $attributo . ";\n" ?>
<?php endforeach; ?>

/**
* @inheritdoc
*/
public function rules()
{
return [<?= "\n " . implode(",\n ", $rules) . "\n        " ?>];
}


/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
<?php foreach ($labels as $name): ?>
    <?= $name . ",\n" ?>
<?php endforeach; ?>
];
}
<?php foreach ($funzioni as $funzione): ?>
    <?= "\n" . $funzione . "\n" ?>
<?php endforeach; ?>

/**
* Salva le risposte del sondaggio relativamente a questa pagina
* @param integer $sessione Id della sessione a cui è collegata la compilazione del sondaggio
* @param integer $accesso Id dell'accesso al servizio di facilitazione se il sondaggio è stato compilato in quell'occasione
* @param integer $completato 0 | 1 di default a 0 se non specificato e indica se la pagina che si sta salvando è l'ultima o meno
*/
public function save($sessione, $accesso = NULL, $completato = false, $read = false) {
<?php foreach ($salvataggio as $Save): ?>
    <?= "\n" . $Save . "\n" ?>
<?php endforeach; ?>
}
}
<?php echo "\n?>"; ?>
