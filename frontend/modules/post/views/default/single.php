<?php

/**
 * @var $this yii\web\View 
 * @var $post frontend\models\Post 
 */

use Yii;
use yii\web\JqueryAsset;


?>

<div class="text-center">
    <?php echo $post->user->username ?? 'Unknown user'; ?>
    <hr>
    <img src="<?php echo Yii::$app->storage->getFile($post->filename); ?>" alt="single post image"  style="max-width: 75%"/>
    <br>
    Likes <span id="likes-number">(<?=$post->countLikes();?>)</span>
    <hr>
    <div class="col-md-12">
        
        <?php /*describe Like-Unlike buttons default displaying options*/
            if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isFavourite($post)) {
                $unlikeDisplay = 'none';
                $likeDisplay = '';
            } 
            else {
                $unlikeDisplay = '';
                $likeDisplay = 'none';
            }
        ?>
        
        <a href="#" class="btn btn-primary button-like" id="button-like" data-id="<?php echo $post->id;?>" style="display: <?=$likeDisplay?>">
            Like&nbsp;&nbsp;
            <span class="glyphicon glyphicon-thumbs-up"></span>
        </a>
        
        
        <a href="#" class="btn btn-primary button-like" id="button-unlike" data-id="<?php echo $post->id;?>" style="display: <?=$unlikeDisplay?>">
            Unlike&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
        </a>
        
    </div>
    <br>
    <hr>
        <?php echo $post->description; ?>
</div>

<?php $this->registerJsFile('@web/js/likeAjax.js', [
    'depends' => JqueryAsset::className(),
]); ?>