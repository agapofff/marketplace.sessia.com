<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MarketplaceProducts */

$this->title = Yii::t('back', 'Изменить');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('back', 'Товары'), 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row justify-content-center">
    <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
        <br>
        <?= $this->render('_form', [
                'model' => $model,
            ]) 
        ?>
    </div>
</div>
