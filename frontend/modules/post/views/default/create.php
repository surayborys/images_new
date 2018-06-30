<?php

/**
 * @var $this yii\web\View 
 * @var $model frontend\modules\post\models\forms\PostForm 
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use Yii;
use yii\web\JqueryAsset;

$this->title = Yii::t('addPost', 'Add post');
?>
<h1><?=Yii::t('addPost', 'Add a new post')?></h1>

<?php $form = ActiveForm::begin();?>
<div class="btn btn-info" id="file-input-div"><?= Yii::t('addPost', 'Choose picture')?></div>
    <?php echo $form->field($model, 'picture')->fileInput(['style'=>'display:none', 'id'=>'file-input-hidden'])->label('')?>
    <?php echo $form->field($model, 'description')->textarea()->label(Yii::t('addPost', 'Description'))?>
    <?php echo Html::submitButton(Yii::t('addPost','Create'));?>
<?php ActiveForm::end();?>
<br>

<?php $this->registerJsFile('@web/js/fileInput.js', [
    'depends' => JqueryAsset::className(),
]); ?>