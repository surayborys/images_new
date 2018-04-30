<?php

/* @var $this yii\web\View */
/* @var $user frontend\models\User */
/* @var $followers */
/* @var $subscriptions */
/* @var $currentUser frontend\models\User */
/* @var $pictureModel frontend\modules\user\models\forms\PictureForm */


use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use dosamigos\fileupload\FileUpload;

?>

<!--Username, Select picture, Unset picture Edit profile -->
<h1 class="text-center text-primary"><?php echo Html::encode($user->username);?> </h1>
<div  class="text-center">
    <img src="<?php echo $user->getPicture(); ?>" id="profile-picture" alt="select your profile image" style="border-radius: 50%; width: 175px; height: 175px">
    <?php if(!Yii::$app->user->isGuest && $currentUser->getId() == $user->getId()):?>
        <a href="<?php echo(Url::to(['/user/profile/edit', 'id'=>$user->getId()]))?>">
            <button class="btn btn-danger btn-sm" style="width: 120px">Edit profile</button>
        </a>
        <!--Display the UNSET PICTURE button only if the profile picture is setted-->
        <?php if($user->picture):?>
        <a href="<?php echo(Url::to(['/user/profile/unset-picture', 'id'=>$user->getId()]))?>">
            <button class="btn btn-danger btn-sm" style="width: 120px">Unset picture</button>
        </a>
        <?php endif;?>
        <!--/PICTURE UNSET BUTTON-->
        <!--FILE UPLOAD BUTTON-->
        <?= FileUpload::widget([
            'model' => $pictureModel,
            'attribute' => 'picture',
            'url' => ['/user/profile/upload-picture'], // your url, this is just for demo purposes,
            'options' => ['accept' => 'image/*'],
            // Also, you can specify jQuery-File-Upload events
            // see: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#processing-callback-options
            'clientEvents' => [
                'fileuploaddone' => 'function(e, data) {
                    if(data.result.success){
                        $("#profile-image-success").show();
                        $("#profile-image-fail").hide();
                        $("#profile-picture").attr("src", data.result.pictureUri);
                    }else{
                        $("#profile-image-success").hide();
                        $("#profile-image-fail").html(data.result.errors.picture).show();
                    }
                }',
            ],
        ]); ?>
        <!--/FILE UPLOAD BUTTON-->
    <?php endif;?>
</div>
<div class="alert alert-success" id="profile-image-success" style="display: none">Profile succesfully updated</div>
<div class="alert alert-danger" id="profile-image-fail" style="display: none"></div>
<p><?php echo HtmlPurifier::process($user->about);?></p>

<!--/Username-->

<!--SUBSCRIBE AND UNSUBSCRIBE BUTTONS-->
<!--Dont show the subscribe and unsubscribe buttons on the current user's page-->
<?php if(Yii::$app->user->isGuest || $currentUser->getId() != $user->getId()):?>
<h2 class="text-center">
    <!--Show only subscribe button for guest or if the current user(Yii::$app->user->identity)
    hasn't yet followed by user $user -->
    <?php if(Yii::$app->user->isGuest || $currentUser->checkIfFollowsBy($user) == false):?>
        <a href="<?php echo(Url::to(['/user/profile/follow', 'id'=>$user->getId()]))?>">
            <button class="btn btn-primary" style="width: 120px">Subscribe</button>
        </a>
    <!--Show only unsubscribe button if the current user has followed by the user $user-->
    <?php elseif($currentUser->checkIfFollowsBy($user) == true):?> 
    <a href="<?php echo(Url::to(['/user/profile/unsubscribe', 'id'=>$user->getId()]))?>">
        <button class="btn btn-primary" style="width: 120px">Unsubscribe</button>
    </a> 
    <?php endif;?>
</h2>
<?php endif; ?>
<hr>
<!--/SUBSCRIBE AND UNSUBSCRIBE BUTTONS-->

<!--Show the 'FRIENS, WHO ARE ALSO FOLLOWING' block only for logged users if they are-->
<div>
    <?php if(!Yii::$app->user->isGuest && count($mutualFollowers = $currentUser->getMutualSubscriptionsTo($user))>0): ?>
    <h5>Friends, who're also following:</h5>
        <?php foreach ($mutualFollowers as $mutualFollower):?>
            <a href="<?php echo Url::to(['/user/profile/view', 'nickname' => ($mutualFollower['nickname']) ? $mutualFollower['nickname'] : ($mutualFollower['id'])]);?>">
                                <?php echo $mutualFollower['username'] ;?>
            </a><br>
        <?php endforeach;?>
    <?php endif;?>
    <hr>
</div>
<!-- /FRIENS, WHO ARE ALSO FOLLOWING-->

<h2 class="text-center">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal1">
        Subscriptions: <?php echo $user->countSubscriptions(); ?>
    </button>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal2">
        Followers: <?php echo $user->countFollowers(); ?>
    </button>
</h2>


<!-- Modal subscriptions -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Subscriptions</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php foreach ($subscriptions as $userItem): ?>
                        <h5 class="text-center">
                            <a href="<?php echo Url::to(['/user/profile/view', 'nickname' => ($userItem['nickname']) ? $userItem['nickname'] : ($userItem['id'])]);?>">
                                <?php echo $userItem['username'] ;?>
                            </a>
                        </h5>              
                    <?php endforeach;?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal subscriptions -->

<!-- Modal followers -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Followers</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php foreach ($followers as $userItem): ?>
                        <h5 class="text-center">
                            <a href="<?php echo Url::to(['/user/profile/view', 'nickname' => ($userItem['nickname']) ? $userItem['nickname'] : ($userItem['id'])]);?>">
                                <?php echo $userItem['username'] ;?>
                            </a>
                        </h5>              
                    <?php endforeach;?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal followers -->