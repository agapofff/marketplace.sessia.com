<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \yii2mod\user\models\PasswordResetRequestForm */

$this->title = Yii::t('yii2mod.user', 'Recover Password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <div class="row justify-content-center">
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <h1><?php echo Html::encode($this->title) ?></h1>
            <p><?php echo Yii::t('yii2mod.user', 'Please fill out your email. A link to reset password will be sent there.'); ?></p>
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
            <?php echo $form->field($model, 'email'); ?>
            <div class="form-group">
                <?php echo Html::submitButton(Yii::t('yii2mod.user', 'Send'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
