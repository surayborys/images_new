<?php

/**
 * @var $this yii\web\View 
 * @var $post frontend\models\Post 
 */

use Yii;
use yii\web\JqueryAsset;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('post', 'Post page');


const CURRENT_USER_GUEST_NO_ID = 'guest_no_id';

$current_user_id = (Yii::$app->user->isGuest) ? CURRENT_USER_GUEST_NO_ID : Yii::$app->user->identity->id;
?>

<div class="page-posts no-padding">
    <div class="row">
        <div class="page page-post col-sm-12 col-xs-12 post-82">
            <div class="blog-posts blog-posts-large">
                <div class="row">
<!----------------------------------------------POST--------------------------------------------------------------->
                    <article class="post col-sm-12 col-xs-12">                                            
                        <div class="post-meta">
                            <div class="post-title">
                                <img src="<?php echo $post->user->getPicture();?>" class="author-image" />
                                <div class="author-name">
                                    <a href="<?php echo Url::to(['/user/profile/view', 'nickname'=>$post->user->getNickname()])?>">
                                        <?php echo Html::encode($post->user->username);?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="post-type-image">
                            <a href="#">
                                <img src="<?php echo Yii::$app->storage->getFile($post->filename); ?>" alt="post">
                            </a>
                        </div>
                        <br>
                        <?php if($post->description):?>
                        <div class="post-description">
                            <p><?php echo HtmlPurifier::process($post->description);?></p>
                        <?php endif;?>
                        <div class="post-bottom">
                            <div class="post-likes">
                                <a href="#" class="btn btn-secondary"><i class="fa fa-lg fa-heart-o"></i></a>
                                <span id="likes-number"><?=$post->countLikes();?></span>
                            </div>
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
                            &nbsp;&nbsp;
                            <a href="#" class="btn btn-default button-like" id="button-like" data-id="<?php echo $post->id;?>" style="display: <?=$likeDisplay?>">
                                <?= Yii::t('post', 'Like')?>&nbsp;&nbsp;
                                <span class="glyphicon glyphicon-thumbs-up"></span>
                            </a>


                            <a href="#" class="btn btn-default button-like" id="button-unlike" data-id="<?php echo $post->id;?>" style="display: <?=$unlikeDisplay?>">
                                <?= Yii::t('post', 'Unlike')?>&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
                            </a>
                            <div class="post-comments">
                                <a>
                                    <span id="number-of-comments"><?=$post->getNumberOfComments()?></span>&nbsp;<?=($post->getNumberOfComments()==1) ? Yii::t('post', 'comment'): Yii::t('post', 'comments')?>
                                </a>

                            </div>
                            <div class="post-date">
                                <span><?php echo Yii::$app->formatter->asDatetime($post->created_at); ?></span>    
                            </div>
                            <div class="post-report">
                                <button type="button" class="btn btn-info button-comment" id="button-comment" data-toggle="modal" data-target="#myModal1">
                                    <?= Yii::t('post', 'Comment post')?>&nbsp;&nbsp;<span class="glyphicon glyphicon-comment"></span>
                                </button>   
                            </div>  
                            <div class="post-report">
                                <?php if(!Yii::$app->user->isGuest && $post->isReported(Yii::$app->user->identity)):?>
                                <span id="post-reported-text"><?= Yii::t('post', 'Post has been already reported...')?></span>
                                <?php else:?>
                                <span id="reported-ajax"></span>
                                <button class="btn btn-default" id="btn-report-post" data-id="<?=$post->id?>">
                                    <?= Yii::t('post', 'Report post')?>
                                    <i class="fa fa-cog fa-spin fa-fw icon-preloader" id="icon-preloader" style="display:none"></i>
                                </button>
                                <?php endif;?>
                            </div>
                        </div>
                    </article>
<!----------------------------------------------/POST--------------------------------------------------------------->
<!----------------------------------------------COMMENTS------------------------------------------------------------>


                    <div class="col-sm-12 col-xs-12">
                        <?php 
                            $styleOfDeleteButton=(Yii::$app->user->isGuest||Yii::$app->user->identity->id!=$post->user->getId())?'display:none':'';
                        ?>
                        <div id="comment-section" data-style-delete="<?=$styleOfDeleteButton?>" 
                             data-post-author="<?php echo $post->user->getId();?>" data-crntuser-id="<?=$current_user_id?>">

                            <div class="comments-post">
                                <div class="row">
                                    <ul class="comment-list" id="comment-list-ul">

                                        <?php foreach ($post->comments as $comment):?>
                                            <!-- comment item -->
                                            <li class="comment" id="comment<?php echo $comment->id;?>">
                                                <div class="comment-user-image">
                                                    <img src="<?php echo $comment->user->getPicture();?>" class="comment-image">
                                                </div>
                                                <div class="comment-info">
                                                    <h4 class="author">
                                                        <a href="<?php echo Url::to(['/user/profile/view', 'nickname' => $comment->user->getNickname()]); ?>">
                                                        <?= Html::encode($comment->user->username)?>
                                                        </a> 
                                                        <span><?php echo Yii::$app->formatter->asDatetime($comment->updated_at); ?></span>
                                                    </h4>
                                                    <p id="comment-text-<?=$comment->id?>"><?=HtmlPurifier::process($comment->text);?></p>
                                                    
                                                    <?php if($current_user_id != CURRENT_USER_GUEST_NO_ID && $comment->user_id == $current_user_id):?>
                                                        <a data-post-id="<?=$post->id?>" data-comment-id="<?=$comment->id?>" class="comment-edit-btn">
                                                                <!-- Button trigger modal -->
                                                                <button type="button" class="btn btn-default button-edit" id="button-edit" data-toggle="modal" data-target="#myModal2">
                                                                    Edit&nbsp;&nbsp;<span class="glyphicon glyphicon-edit"></span>
                                                                </button>
                                                        </a>&nbsp;
                                                    <?php endif;?>
                                                    <a style="<?=$styleOfDeleteButton?>" data-post-id="<?=$post->id?>" data-comment-id="<?=$comment->id?>" class="comment-delete-btn">
                                                        <button class="btn btn-default">Delete</button>
                                                    </a>
                                                </div>
                                            </li>
                                            <!-- comment item -->
                                        <?php endforeach;?>
                                    </ul>
                                </div>

                            </div>  
                        </div>
                    </div>
<!---------------------------------------------/COMMENTS------------------------------------------------------------>

                </div>
            </div>
        </div>
    </div>
</div>





 <!----------------------------------------------MODAL COMMENTS--------------------------------------------------->

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
                    <input type="hidden" name="postId" value="<?php echo $post->id?>">
                    <hr><hr>
                    <button type="button" class="btn btn-default" id="submit-button" data-dismiss="modal">Add comment</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Modal comment -->
<!-- Modal edit comment -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">Edit</h4>
            </div>
            <div class="modal-body">
                <form method="post" id="ajax-edit-form">
                    <label for="commentTextarea">Type your comment</label>
                    <textarea class="form-control" id="commentEditTextarea" rows="4" name="text"></textarea>
                    <input type="hidden" name="postId" value="<?php echo $post->id?>">
                    <input id="comment-id-input" type="hidden" name="commentId" value="">
                    <hr><hr>
                    <button type="button" class="btn btn-default" id="submit-button-edit" data-dismiss="modal">Edit comment</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
 <!----------------------------------------------/MODAL COMMENTS----------------------------------------------------->

 <!----------------------------------------------REGISTER JS FILES--------------------------------------------------->


<?php $this->registerJsFile('@web/js/likeAjax.js', [
    'depends' => JqueryAsset::className(),
]); ?>

<?php $this->registerJsFile('@web/js/commentAjax.js', [
    'depends' => JqueryAsset::className(),
]); ?>
 
 <?php $this->registerJsFile('@web/js/reportPost.js', [
    'depends' => JqueryAsset::className(),
]); ?>
 <!---------------------------------------------/REGISTER JS FILES--------------------------------------------------->
