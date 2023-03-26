<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \yii2mod\user\models\LoginForm */

$this->title = Yii::t('yii2mod.user', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="static-page">
    <div class="row justify-content-center">
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <h1><?php echo Html::encode($this->title); ?></h1>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?php echo $form->field($model, 'email'); ?>
            <?php echo $form->field($model, 'password')->passwordInput(); ?>
            <?php echo $form->field($model, 'rememberMe')->checkbox(); ?>
            <div style="color:#999;margin:1em 0">
                <?php echo Html::a(Yii::t('yii2mod.user', 'Forgot your password?'), ['site/request-password-reset']); ?>
            </div>
            <div class="form-group text-center">
                <?php echo Html::submitButton(Yii::t('yii2mod.user', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']); ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
