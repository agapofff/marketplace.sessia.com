<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Pages */
/* @var $form yii\widgets\ActiveForm */
?>

<?php Pjax::begin(); ?>

    <div class="marketplace-products-form">

        <?php $form = ActiveForm::begin(); ?>
        
            <?= $form
                    ->field($model, 'marketplace_id')
                    ->dropDownList(array_keys(Yii::$app->params['marketplace']))
                    ->label('Маркетплейс');
            ?>
            <br>
            
            <?= $form
                    ->field($model, 'store_id')
                    ->textInput([
                        'maxlength' => true
                    ])
            ?>
            <br>

            <?= $form
                    ->field($model, 'sessia_product_id')
                    ->textInput([
                        'maxlength' => true
                    ])
            ?>
            <br>
            
            <?= $form
                    ->field($model, 'marketplace_product_id')
                    ->textInput([
                        'maxlength' => true
                    ])
            ?>
            <br>
            
            <?= $form
                    ->field($model, 'marketplace_product_id_2')
                    ->textInput([
                        'maxlength' => true
                    ])
            ?>
            <br>

            <div class="text-center">
                <?= Html::submitButton(Html::tag('i', '', [
                        'class' => 'fas fa-save'
                    ]) . '&nbsp;' . Yii::t('app', 'Сохранить'), [
                        'class' => 'btn btn-success'
                    ]) 
                ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php Pjax::end() ?>
