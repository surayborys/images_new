<?php
/* @var $this yii\web\View */
/* @var $editProfileForm  frontend\modules\user\models\EditProfileForm */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php 
    $this->title = 'Edit';
    $username = $editProfileForm->username;
    $this->params['breadcrumbs'][] = [
        'template' => "<li>{link}</li>\n",   
        'label' => $username,
        'url' => [Url::to(['/user/profile/view', 'nickname'=> Yii::$app->user->identity->getNickname()])],
    ];                             
    $this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<?php $form = ActiveForm::begin(); ?>
<?php echo $form->field($editProfileForm, 'username')->label('Your name');?>
<?php echo $form->field($editProfileForm, 'nickname')->hint('Type your nickname to get personalized link to your profile page');?>
<?php echo $form->field($editProfileForm, 'type')->dropDownList([1=>'public', 2=>'private'])->hint('The private type makes your profile unvisible for guest users')->label('Profile type    ');?>
<?php echo $form->field($editProfileForm, 'about')->textarea()->hint('Tell everybody few words about yourself');?>
<?php echo $form->field($editProfileForm, 'picture')->label('Your profile photo')->fileInput();?>
<?php echo Html::submitButton('edit', ['class' => 'btn btn-danger']);?>
<?php ActiveForm::end();

