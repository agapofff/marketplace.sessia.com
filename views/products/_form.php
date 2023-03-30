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

            <?= $form
                    ->field($model, 'sessia_product_id')
                    ->textInput([
                        'maxlength' => true
                    ])
            ?>
            
            <?= $form
                    ->field($model, 'marketplace_product_id')
                    ->textInput([
                        'maxlength' => true
                    ])
            ?>
            
            <div class="text-center pt-2">
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
