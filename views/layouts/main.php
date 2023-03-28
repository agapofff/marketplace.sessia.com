<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Toast;
use lavrentiev\widgets\toastr\NotificationFlash;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag([
    'name' => 'viewport', 
    'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no'
]);
$this->registerMetaTag([
    'name' => 'description', 
    'content' => $this->params['meta_description'] ?? ''
]);
$this->registerMetaTag([
    'name' => 'keywords', 
    'content' => $this->params['meta_keywords'] ?? ''
]);
$this->registerLinkTag([
    'rel' => 'icon', 
    'type' => 'image/x-icon', 
    'href' => Yii::getAlias('@web/favicon.png')
]);

$menu = [];
if (Yii::$app->user->isGuest) {
    $menu = [
        [
            'label' => Yii::t('app', 'Войти'), 
            'url' => ['/site/login']
        ]
    ];
} else {
    $importLinks = [
        [
            'label' => Yii::t('app', 'Из всех маркетплейсов'),
            'url' => ['/import'],
            'linkOptions' => [
                'target' => '_blank',
            ],
        ]
    ];
    
    foreach (Yii::$app->params['marketplace'] as $marketplaceName => $marketplace) {
        if ($marketplace['active']) {
            $importLinks[] = [
                'label' => Yii::t('app', 'Из') . ' ' . ucfirst(strtolower($marketplaceName)),
                'url' => ['/import/' . $marketplaceName],
                'linkOptions' => [
                    'target' => '_blank',
                ],
            ];
        }
    }
    
    $menu = [
        [
            'label' => Yii::t('app', 'Импорт заказов'), 
            'url' => '#',
            'items' => $importLinks,
        ],
        [
            'label' => 'Товары', 
            'url' => ['/products']
        ],
        [
            'label' => 'Заказы', 
            'url' => ['/orders']
        ],
        '<li class="nav-item">'
            . Html::beginForm(['/site/logout'])
                . Html::submitButton(
                    Yii::t('app', 'Выйти') . ' (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'nav-link btn btn-link logout']
                )
            . Html::endForm()
        . '</li>'
    ];
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.3.1/css/all.css">
    </head>
    <body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header id="header">
    <?php
        NavBar::begin([
            'brandLabel' => Html::img('/images/logo.png') . Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
        ]);
        echo Nav::widget([
            'options' => [
                'class' => 'navbar-nav'
            ],
            'items' => $menu
        ]);
        NavBar::end();
    ?>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
        <div class="container">
            <?php if (!empty($this->params['breadcrumbs'])) { ?>
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
            <?php } ?>
            
            <?= NotificationFlash::widget([
                    'options' => [
                        "closeButton" => true,
                        "debug" => false,
                        "newestOnTop" => false,
                        "progressBar" => false,
                        "positionClass" => NotificationFlash::POSITION_BOTTOM_RIGHT,
                        "preventDuplicates" => true,
                        "onclick" => null,
                        "showDuration" => "300",
                        "hideDuration" => "1000",
                        "timeOut" => "5000",
                        "extendedTimeOut" => "1000",
                        "showEasing" => "swing",
                        "hideEasing" => "linear",
                        "showMethod" => "fadeIn",
                        "hideMethod" => "fadeOut",
                        'escapeHtml' => false,
                    ]
                ]) 
            ?>
            
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="mt-auto py-3 bg-light">
        <div class="container">
            <div class="row text-muted">
                <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
                <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
