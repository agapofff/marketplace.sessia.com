<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MarketplaceOrdersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="marketplace-orders-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'marketplace_id') ?>

    <?= $form->field($model, 'marketplace_order_id') ?>

    <?= $form->field($model, 'sessia_order_id') ?>

    <?= $form->field($model, 'request') ?>

    <?php // echo $form->field($model, 'response') ?>

    <?php // echo $form->field($model, 'sum') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('back', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('back', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
