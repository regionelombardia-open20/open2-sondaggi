<?php
echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
use lispa\amos\sondaggi\models\SondaggiDomandePagine;
use lispa\amos\sondaggi\models\SondaggiDomandeTipologie;
use lispa\amos\sondaggi\models\SondaggiDomandeCondizionate;
use lispa\amos\sondaggi\models\SondaggiRispostePredefinite;
use lispa\amos\sondaggi\models\Sondaggi;
use lispa\amos\sondaggi\models\SondaggiDomande;
use lispa\amos\sondaggi\models\SondaggiRisposte;
use lispa\amos\sondaggi\models\SondaggiRisposteSessioni;
use lispa\amos\sondaggi\models\SondaggiStato;
use lispa\amos\core\helpers\Html;
use yii\helpers\Url;
use lispa\amos\core\forms\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use lispa\amos\core\forms\Tabs;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use lispa\amos\sondaggi\assets\ModuleRisultatiAsset;
use lispa\amos\sondaggi\AmosSondaggi;

/**
* Questa è la view per la pagina "<?= $generator->paginaSondaggio ?>" del sondaggio "<?= $generator->titoloSondaggio ?>".
*
*/

ModuleRisultatiAsset::register($this);

$this->title = AmosSondaggi::t('amossondaggi', 'Compila il sondaggio: <?= addslashes($generator->titoloSondaggio) ?>');
$this->params['breadcrumbs'][] = $this->title;
if(!isset($libero)){
$libero = FALSE;
}
<?php
echo "\n?>\n";
?>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<div class="sondaggi-compilazione">
    <div class="sondaggi-form">
        <?php
        echo "<?php \n";
        ?>        
        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
        $customView = Yii::$app->getViewPath() . '/imageField.php';
        <?php
        echo "\n ?>\n";
        ?>
        <h3 class="sondaggio"><?= $generator->paginaSondaggio ?></h3>
        <h4 class="sondaggio"><?= $generator->descPaginaSondaggio ?></h4>    
        <div class="row">
            <?php foreach ($campi as $campo) : ?>                          
                <?= $campo ?>                                   
            <?php endforeach; ?>
        </div> 
    </div>     
    <div id="form-actions" class="bk-btnFormContainer">

        <?php echo "<?=
                Html::submitButton(
                                AmosSondaggi::t('amossondaggi', 'Prosegui'), [
                    'class' => 'btn btn-navigation-primary'
                ]);
                ?>\n
                <?php
                if(!isset(\$attivita) && !\$libero): ?>\n
                <?=
                Html::a(AmosSondaggi::t('amossondaggi', 'Chiudi'), Url::previous(), [
                    'class' => 'btn btn-secondary undo-edit mr10'
                ]);
                ?>\n
                <?php endif; ?>\n
            ";
        ?>

    </div>
    <input type="hidden" name="idPagina" value="<?= '<?=$idPagina?>' ?>">
    <input type="hidden" name="idSessione" value="<?= '<?=$idSessione?>' ?>">
    <input type="hidden" name="utente" value="<?= '<?=$utente?>' ?>">    
    <?php echo "\n<?php ActiveForm::end(); ?>\n" ?>
</div>
