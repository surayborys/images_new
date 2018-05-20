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
        
        <!--comment button-->
        <h2 class="text-center">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary button-comment" id="button-comment" data-toggle="modal" data-target="#myModal1">
                Comment&nbsp;&nbsp;<span class="glyphicon glyphicon-comment"></span>
            </button>
        </h2>
        <!--/comment button-->
    </div>
    <br>
    <hr>
        <?php echo $post->description; ?>
</div>

<!-- Modal comment -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Comment</h4>
            </div>
            <div class="modal-body">
                <form method="post" id="ajax-form">
                    <label for="commentTextarea">Type your comment</label>
                    <textarea class="form-control" id="commentTextarea" rows="4" name="text"></textarea>
                    <input type="hidden" name="postId" value="<?php echo $post->id?>"
                    <hr><hr>
                    <button type="button" class="btn btn-default" id="submit-button" data-dismiss="modal">Add comment</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Modal comment -->

<!--comments-->
<?php 
    $styleOfDeleteButton=(Yii::$app->user->isGuest||Yii::$app->user->identity->id!=$post->user->getId())?'display:none':'';
?>
<div id="comment-section" data-style-delete="<?=$styleOfDeleteButton?>" 
     data-post-author="<?php echo $post->user->getId();?>">
    
    <?php foreach ($post->comments as $comment):?>
    <hr>
    <pre id="comment<?php echo $comment->id;?>">
    <img src="<?php echo $comment->user->getPicture();?>" alt="user picture" class="comment-image">
    <b><?=$comment->user->username?></b> at <?php echo $comment->updated_at?><br><?=$comment->text?> 
    <a style="<?=$styleOfDeleteButton?>" data-post-id="<?=$post->id?>" data-comment-id="<?=$comment->id?>" class="comment-delete-btn">
        <button class="btn btn-default">Delete</button>
    </a>
    </pre>
    <?php endforeach;?>
</div>
<!--/comments-->

<?php $this->registerJsFile('@web/js/likeAjax.js', [
    'depends' => JqueryAsset::className(),
]); ?>

<?php $this->registerJsFile('@web/js/commentAjax.js', [
    'depends' => JqueryAsset::className(),
]); ?>