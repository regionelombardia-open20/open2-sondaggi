<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\widgets\graphics\views
 * @category   CategoryName
 */

use open20\amos\core\helpers\Html;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\assets\ModuleSondaggiAsset;
use open20\amos\sondaggi\controllers\PubblicazioneController;
use open20\amos\sondaggi\widgets\graphics\WidgetGraphicsUltimiSondaggi;
use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var View $this
 * @var ActiveDataProvider $lista
 * @var WidgetGraphicsUltimiSondaggi $widget
 * @var string $toRefreshSectionId
 */

ModuleSondaggiAsset::register($this);

$listaModels = $lista->getModels();

?>

<?php

$modelLabel = 'sondaggi';

$labelLinkAll = AmosSondaggi::t('amossondaggi', 'Pubblica i sondaggi');
$urlLinkAll = '/sondaggi/pubblicazione/pubblicazione';
$titleLinkAll = AmosSondaggi::t('amossondaggi', 'Visualizza la lista dei sondaggi pubblicabili o ri-pubblicabili');

$subTitleSection = Html::tag('p', AmosSondaggi::t('amossondaggi', ''));

$labelCreate = AmosSondaggi::t('amossondaggi', 'Nuovo');
$titleCreate = AmosSondaggi::t('amossondaggi', 'Crea un nuovo sondaggio');
$labelManage = AmosSondaggi::t('amossondaggi', 'Gestisci');
$titleManage = AmosSondaggi::t('amossondaggi', 'Gestisci i sondaggi');
$urlCreate = '/sondaggi/dashboard/create';

$controller = PubblicazioneController::class;
$manageLinks = (method_exists($controller, 'getManageLinks') ? $controller::getManageLinks() : []);

$moduleCwh = \Yii::$app->getModule('cwh');
if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
    $scope = $moduleCwh->getCwhScope();
    $isSetScope = (!empty($scope)) ? true : false;
}

$hideCreate = !\Yii::$app->user->can('SONDAGGI_CREATE');
?>

<div class="widget-graphic-cms-bi-less card-<?= $modelLabel ?> container">
    <div class="page-header">
        <?= $this->render(
            "@vendor/open20/amos-layout/src/views/layouts/fullsize/parts/bi-less-plugin-header",
            [
                'isGuest' => \Yii::$app->user->isGuest,
                'isSetScope' => $isSetScope,
                'modelLabel' => 'news',
                'titleSection' => $widget->getLabel(),
                'subTitleSection' => $subTitleSection,
                'urlLinkAll' => $hideCreate ? null : $urlLinkAll,
                'labelLinkAll' => $labelLinkAll,
                'titleLinkAll' => $titleLinkAll,
                'hideCreate' => $hideCreate,
                'labelCreate' => $labelCreate,
                'titleCreate' => $titleCreate,
                'labelManage' => $labelManage,
                'titleManage' => $titleManage,
                'urlCreate' => $urlCreate,
                'manageLinks' => $manageLinks,
            ]
        );
        ?>
    </div>
    
    <?php if ($listaModels) { ?>
        <div class="list-view">
            <div>
                <div class="" role="listbox" data-role="list-view">
                    <?php foreach ($listaModels as $sondaggio) : ?>
                        <div class="widget-listbox-option" role="option">
                            <article class="wrap-item-box">
                                <div>
                                    <div class="container-img">
                                        <?php
                                        $url = '/img/img_default.jpg';
                                        if (!is_null($sondaggio->file)) {
                                            $url = $sondaggio->file->getUrl('dashboard_sondaggi', false, true);
                                        }
                                        ?>
                                        <?= Html::img($url, ['class' => 'img-responsive', 'alt' => AmosSondaggi::t('amossondaggi', 'Immagine del sondaggio')]); ?>
                                    </div>
                                </div>

                                <div class="container-text">
                                    <div class="h3 box-widget-subtitle">
                                        <?php
                                        if (strlen($sondaggio->titolo) > 55) {
                                            $stringCut = substr($sondaggio->titolo, 0, 55);
                                            echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                        } else {
                                            echo $sondaggio->titolo;
                                        }
                                        ?>
                                    </div>
                                    <p class="box-widget-text">
                                        <?php
                                        if (strlen($sondaggio->descrizione) > 80) {
                                            $stringCut = substr($sondaggio->descrizione, 0, 80);
                                            echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                        } else {
                                            echo $sondaggio->descrizione;
                                        }
                                        ?>
                                    </p>
                                </div>
                                <div class="footer-listbox m-t-10">
                                    <?= Html::a(AmosSondaggi::t('amossondaggi', '#readMore'), ['/sondaggi/pubblicazione/compila', 'id' => $sondaggio->id], ['class' => 'btn btn-navigation-primary']); ?>
                                </div>
                            </article>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    
    <?php } ?>
</div>