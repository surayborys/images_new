<?php

/* @var $this yii\web\View */
/* @var $currentUser frontend\models\User*/
/* @var $feedItems[] frontend\model\Feed*/
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
use Yii;

$this->title = 'News Feed';
?>

    <div class="container full">
        <div class="page-posts no-padding">                    
            <div class="row">                        
                <div class="page page-post col-sm-12 col-xs-12">
                    <div class="blog-posts blog-posts-large">

                        <div class="row" id="feedItemsRow">
                            <?php if($feedItems):?>
                                <?php foreach($feedItems as $feedItem): ?>
                                <?php/*@var $feedItemfrontend\model\Feed*/?>

                            <!-- feed item -->
                            <article class="post col-sm-12 col-xs-12">                                            
                                <div class="post-meta">
                                    <div class="post-title">
                                        <img src="<?php echo $feedItem->author_picture; ?>" class="author-image" />
                                        <div class="author-name">
                                            <a href="<?php echo Url::to(['/user/profile/view', 'nickname' => ($feedItem->author_nickname) ? $feedItem->author_nickname : $feedItem->author_id]); ?>">
                                                <?php echo Html::encode($feedItem->author_name); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="post-type-image">
                                    <a href="<?=Url::to(['/post/default/view', 'id' => $feedItem->post_id])?>">
                                        <img src="<?php echo Yii::$app->storage->getFile($feedItem->post_filename); ?>" alt="">
                                    </a>
                                </div>
                                <?php if($feedItem->post_description):?>
                                <div class="post-description">
                                    <p><?php echo HtmlPurifier::process($feedItem->post_description); ?></p>
                                </div>
                                <?php endif;?>
                                <br>
                                <div class="post-bottom">
                                    <div class="post-likes">
                                        <i class="fa fa-lg fa-heart-o"></i>&nbsp;
                                        <span id="likes-number-<?=$feedItem->post_id?>"><?=$feedItem->countLikesOfThePost();?></span>&nbsp;
                                        <a href="#" class="btn btn-default button-like" id="like<?=$feedItem->post_id?>" data-id="<?php echo $feedItem->post_id;?>" style="display:<?php echo ($currentUser->isFavourite($feedItem->getPostEntity()))?'none':'';?>">
                                            <span class="glyphicon glyphicon-thumbs-up"></span>
                                        </a>
                                        <a href="#" class="btn btn-default button-unlike" id="unlike<?=$feedItem->post_id?>" data-id="<?php echo $feedItem->post_id;?>" style="display:<?php echo ($currentUser->isFavourite($feedItem->getPostEntity()))?'':'none';?>">
                                            <span class="glyphicon glyphicon-thumbs-down"></span>
                                        </a>
                                    </div>
                                    <div class="post-comments">
                                        <a href="<?=Url::to(['/post/default/view', 'id' => $feedItem->post_id])?>"><?=$feedItem->getNumberOfComments();?> Comments</a>

                                    </div>
                                    <div class="post-date">
                                        <span><?php echo Yii::$app->formatter->asDatetime($feedItem->post_created_at); ?></span>    
                                    </div>
                                </div>
                            </article>
                            <!-- feed item -->
                                <?php endforeach;?>
                            <?php else: ?>
                                <p class="text-center">Nobody posted yet</p>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php $this->registerJsFile('@web/js/likeAjaxFeed.js', [
    'depends' => JqueryAsset::className(),
]); ?>
