<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\modules\user\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('login','Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('login', 'Please fill out the following fields to login:')?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true])->label(Yii::t('login', 'Email')) ?>

                <?= $form->field($model, 'password')->passwordInput()->label(Yii::t('login', 'Password')) ?>

                <?= $form->field($model, 'rememberMe')->checkbox()->label(Yii::t('login', 'Remember Me')) ?>

                <div style="color:#999;margin:1em 0">
                    <?= Yii::t('login', 'If you forgot your password you can')?>&nbsp;<?= Html::a(Yii::t('login', 'reset it'), ['/user/default/request-password-reset']) ?>.
                </div>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('login', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
            
            <h3 class="text-primary">
                <?= Yii::t('login', 'Login with')?>:
            </h3>
            <?= yii\authclient\widgets\AuthChoice::widget([
                 'baseAuthUrl' => ['/user/default/auth'],
                 'popupMode' => false,
            ]) ?>
            
        </div>
    </div>
</div>
