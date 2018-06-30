<?php
/* @var $this yii\web\View */
/* @var $editProfileForm  frontend\modules\user\models\EditProfileForm */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php 
    $this->title = Yii::t('profileEdit','Edit');
    $username = $editProfileForm->username;
    $this->params['breadcrumbs'][] = [
        'template' => "<li>{link}</li>\n",   
        'label' => $username,
        'url' => [Url::to(['/user/profile/view', 'nickname'=> Yii::$app->user->identity->getNickname()])],
    ];                             
    $this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<?php $form = ActiveForm::begin(); ?>
<?php echo $form->field($editProfileForm, 'username')->label(Yii::t('profileEdit','Your name'));?>
<?php echo $form->field($editProfileForm, 'nickname')->hint(Yii::t('profileEdit','Type your nickname to get personalized link to your profile page'))->label(Yii::t('profileEdit', 'Nickname'));?>
<?php echo $form->field($editProfileForm, 'type')->dropDownList([1=>'public', 2=>'private'])->hint(Yii::t('profileEdit','The private type makes your profile unvisible for guest users'))->label(Yii::t('profileEdit', 'Profile type'));?>
<?php echo $form->field($editProfileForm, 'about')->textarea()->hint(Yii::t('profileEdit','Tell everybody few words about yourself'))->label(Yii::t('profileEdit', 'About'));?>
<?php echo Html::submitButton(Yii::t('profileEdit','edit'), ['class' => 'btn btn-info']);?>
<?php ActiveForm::end();?>

<br/>