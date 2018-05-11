<?php

/**
 * @var $this yii\web\View 
 * @var $model frontend\modules\post\models\forms\PostForm 
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1>Add a new post</h1>

<?php $form = ActiveForm::begin();?>
    <?php echo $form->field($model, 'picture')->fileInput();?>
    <?php echo $form->field($model, 'description')->textarea();?>
    <?php echo Html::submitButton('Create');?>
<?php ActiveForm::end();?>