<?php

/* @var $this yii\web\View */
/* @var $users frontend\models\User::find()->all(); */
use yii\helpers\Url;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>IMAGES</h1>
    </div>

    <div class="body-content">

        <div class="row">
            <?php foreach ($users as $user):?>
            <a href="<?php echo(Url::to(['/user/profile/view', 'nickname'=>$user->getNickname()])); ?>"><?=$user->username;?></a>
            <hr>
            <?php endforeach; ?>
        </div>

    </div>
</div>
