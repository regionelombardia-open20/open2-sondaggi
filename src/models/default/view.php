<?php
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
use open20\amos\core\helpers\Html;
use yii\helpers\Url;
use open20\amos\core\forms\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use open20\amos\core\forms\Tabs;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\attachments\components\AttachmentsInput;
use kartik\file\FileInput;
use open20\amos\attachments\components\AttachmentsList;
use open20\amos\sondaggi\assets\ModuleRisultatiFrontendAsset;




/**
* Questa è la view per la pagina "<?= $generator->paginaSondaggio ?>" del sondaggio "<?= $generator->titoloSondaggio ?>".
*
*/

$this->title = AmosSondaggi::t('amossondaggi', 'Compila il sondaggio: <?=
(str_replace("'", "\'", $generator->titoloSondaggio))
?>');
$this->params['breadcrumbs'][] = $this->title;
if(!isset($libero)){
$libero = FALSE;
}
<?php
echo "\n?>\n";
?>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<?php
if ($sondaggio->frontend == 1) {
    echo "<?php \n"
    ."if(strpos(\yii\helpers\Url::current(), '/frontend/compila?id') !== false){"
    ."\$sondaggioFrontendAsset = ModuleRisultatiFrontendAsset::register(\$this); \n"
    ."?>\n";
    if (!empty($sondaggio->forza_lingua)) {
        echo "<?php\n \Yii::\$app->language = '".$sondaggio->forza_lingua."'; \n?>\n";
    }
    $baseurl = '<?= $sondaggioFrontendAsset->baseUrl ?>';
    ?>
    <div class="container-padding">
        <img class="icon-sondaggio" src="<?= $baseurl ?>/img/poll.png">

        <h1><?= $generator->titoloSondaggio ?></h1>
        <div style="background: #ddd;">
            <?php if ($progress > 0) : ?>
                <div class="simple-progressbar" style="height:24px;min-width:10%;width:<?= $progress ?>%;background: green;text-align:center;color:#fff"><?= $progress ?>%</div>
            <?php else : ?>
                <div class="simple-progressbar" style="height:24px;width:<?= $progress ?>%;background: green;text-align:center;color:#000;position:relative;left:10px;"><?= $progress ?>%</div>
            <?php endif; ?>
        </div>

    </div>

    <?php
    echo "<?php \n"
    ."}"
    ."?>\n";
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        var submitClicked = false;
        $('#form_true_sondaggio-<?= $pagina->id ?>').submit(function (e) {
            if (submitClicked === false) {
                e.preventDefault();
                return false;
            }
        });
        $('#truesubmit-<?= $pagina->id ?>').click(function () {
            submitClicked = true;
            $('#form_true_sondaggio-<?= $pagina->id ?>').submit();

        });

        // find elements
        var sortCheck = $(".sortable-response");

        //Rendo draggabile le risposte
        sortCheck.sortable({
            handle: ".dragger",
        });


        jQuery('.mover').click(function(e){
            var arrow = jQuery(this);
            var dir = arrow.data('direction');
            var row = arrow.parent().parent();

            switch (dir) {
                case 'up':
                    var prev = row.prev();

                    if(prev) {
                        prev.before(row);
                    }
                    break;
                case 'down':
                    var next = row.next();

                    if(next) {
                        next.after(row);
                    }
                    break;
            }

            //My Own Sortable
            var sortable = row.parents('.sortable-response');

            //Trigger Update
            sortable.trigger('sortupdate');
        });

        sortCheck.on('sortupdate', function (e, ui) {
            var questionId = jQuery(this).data('question');
            var sortInputsResponse = jQuery('input[type!="hidden"]', this);
            var sortQuestions = jQuery(this).parents('form>div.row');

            //Salvo ordinamento per il POST nelle risposte
            sortInputsResponse.each(function (k, v) {
                var responseId = jQuery(this).val();
                var sortInput = jQuery('input[type="hidden"][data-response="' + responseId + '"]');
                sortInput.val(k + 1);
            });

            //Items to sort
            var toBeSorted = sortQuestions.children('[data-sorttype="response"]');

            //Element preceding the onnes to be sorted
            var whereToAppend = toBeSorted.first().prev();

            //Procedura di ordinamento
            var sorted = toBeSorted.sort(function (a, b) {
                var aResponseId = jQuery(a).data('sortby');
                var bResponseId = jQuery(b).data('sortby');
                var aParent = jQuery('input[type="hidden"][data-response="' + aResponseId + '"]');
                var bParent = jQuery('input[type="hidden"][data-response="' + bResponseId + '"]');
                var aVal = aParent.val();
                var bVal = bParent.val();

                return (aVal < bVal) ? -1 : (aVal > bVal) ? 1 : 0;
            });

            //Ordino Graficamente le domandevincolate
            whereToAppend.after(sorted);
        });
    });
</script>
<div class="sondaggi-compilazione pagid-<?= $pagina->id ?>">
    <div class="sondaggi-form">
        <?php
        echo "<?php \n";
        ?>
        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'form_true_sondaggio-<?= $pagina->id ?>']]);
        $customView = Yii::$app->getViewPath() . '/imageField.php';
        <?php
        echo "\n ?>\n";
        ?>
        <?php if (!empty(trim($generator->paginaSondaggio))) { ?>
            <h3 class="sondaggio"><?= $generator->paginaSondaggio ?></h3>
        <?php } ?>
        <?php if (!empty(trim($generator->descPaginaSondaggio))) { ?>
            <h4 class="sondaggio"><?= $generator->descPaginaSondaggio ?></h4>
        <?php } ?>
        <div class="row">
            <?php foreach ($campi as $campo) : ?>
                <?= $campo ?>
            <?php endforeach; ?>
        </div>
        <div class="col-xs-12 text-right">
            <?php
            if (!empty($obbligatorie) && $obbligatorie > 0) {
                echo "<?= '* ' . \Yii::t('amossondaggi', 'campi obbligatori') ?>";
            }
            ?>
        </div>
    </div>
    <div id="form-actions" class="bk-btnFormContainer">

        <?php
        $urlSondaggio = trim($sondaggio->url_chiudi_sondaggio);

        echo "<?php
                if(!isset(\$attivita) && !\$libero): ?>\n
                <?=
                Html::a(AmosSondaggi::t('amossondaggi', 'Chiudi'), ".(!empty($urlSondaggio) ? "'$urlSondaggio'" : "Url::previous()").", [
                    'class' => 'btn btn-secondary undo-edit mr10'
                ]);
                ?>\n
                <?php endif; ?>\n
                <?= Html::button(
                                AmosSondaggi::t('amossondaggi', 'Prosegui'), [
                                'id' => 'truesubmit-".$pagina->id."',
                    'class' => 'btn btn-navigation-primary'
                ]);
                ?>\n                
            ";
        ?>

    </div>
    <input type="hidden" name="idPagina" value="<?= '<?=$idPagina?>' ?>">
    <input type="hidden" name="idSessione" value="<?= '<?=$idSessione?>' ?>">
    <input type="hidden" name="utente" value="<?= '<?=$utente?>' ?>">
    <?php echo "\n<?php ActiveForm::end(); ?>\n" ?>
</div>