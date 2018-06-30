<?php

/* @var $this yii\web\View */
/* @var $model frontend\models\forms\SearchForm */
/* @var $results = array() */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('search', 'Search');

?>
<div class="site-singup">
    
    <div class="row">
        <div class="col-lg-5">
        <?php $form = ActiveForm::begin(['method'=>'get'])?>
            <?= $form->field($model, 'username')->label(Yii::t('search', 'Username'))?>
            <div class="form-group">
            <?= Html::submitButton(Yii::t('search','Search').'&nbsp;<i class="fa fa-search"></i>', ['class' => 'btn btn-default'])?>
            </div>
        <?php ActiveForm::end()?>
        </div>    
    </div>
    
    <hr>
    <div style="margin-bottom:300px; margin-top:100px">
    <div class="col-md-12 col-sm-12">
    <?php if(is_array($results)):?>
        <div class="row">
        <?php foreach ($results as $user):?> 
        <div class="col-md-3 col-sm-12 text-center">
            <img src="<?= $user['picture'] ?>" style="width: 100px; height: 100px;  border-radius: 50%; display: inline">
            <br>
            <a href="<?= Url::to(['/user/profile/view', 'nickname' => $user['nickname']])?>">
            <?= $user['username']?>
            </a> 
        </div>
        <?php endforeach;?> 
        </div>
    <?php else: {echo "<br>Nothing has been found yet";}?>
    <?php endif;?>
    </div>
    </div>
</div>