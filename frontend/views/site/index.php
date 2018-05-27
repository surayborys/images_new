<?php

/* @var $this yii\web\View */
/* @var $currentUser frontend\models\User*/
/* @var $feedItems[] frontend\model\Feed*/
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
use Yii;

$this->title = 'My Yii Application';
?>
<div class="site-index">
        <div class="row text-center" id="feedItemsRow">
        <?php if($feedItems):?>
            <?php foreach($feedItems as $feedItem): ?>
            <?php/*@var $feedItemfrontend\model\Feed*/?>
                
                <div class="col-md-12">
                    <img src="<?php echo $feedItem->author_picture; ?>" width="30" height="30">
                    
                    <a href="<?php echo Url::to(['/user/profile/view', 'nickname' => ($feedItem->author_nickname) ? $feedItem->author_nickname : $feedItem->author_id]); ?>">
                         <?php echo Html::encode($feedItem->author_name); ?>
                    </a>
                </div>
                <a href="<?=Url::to(['/post/default/view', 'id' => $feedItem->post_id])?>">
                    <img src="<?php echo Yii::$app->storage->getFile($feedItem->post_filename); ?>" style="max-width:75%" /> 
                </a>
                <div class="col-md-12">
                    <?php echo HtmlPurifier::process($feedItem->post_description); ?>
                </div>                

                <div class="col-md-12">
                    <?php echo Yii::$app->formatter->asDatetime($feedItem->post_created_at); ?>
                </div>
                <div class="col-md-12">
                    <?php if(is_object($feedItem->getPostEntity())):?>
                    Likes <span id="likes-number-<?=$feedItem->post_id?>">(<?=$feedItem->countLikesOfThePost();?>)</span>
                    <a href="#" class="btn btn-primary button-like" id="like<?=$feedItem->post_id?>" data-id="<?php echo $feedItem->post_id;?>" style="display:<?php echo ($currentUser->isFavourite($feedItem->getPostEntity()))?'none':'';?>">
                        Like&nbsp;&nbsp;
                        <span class="glyphicon glyphicon-thumbs-up"></span>
                    </a>
                

                    <a href="#" class="btn btn-primary button-unlike" id="unlike<?=$feedItem->post_id?>" data-id="<?php echo $feedItem->post_id;?>" style="display:<?php echo ($currentUser->isFavourite($feedItem->getPostEntity()))?'':'none';?>">
                        Unlike&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
                    </a>
                    <span>Comments: <?=$feedItem->getNumberOfComments();?></span>
                    <?php endif;?>
                </div>
                <div class="col-md-12"><hr/></div>  
            <?php endforeach;?>
        <?php else: ?>
            <p class="text-center">Nobody posted yet</p>
        <?php endif;?>
        </div>
</div>

<?php $this->registerJsFile('@web/js/likeAjaxFeed.js', [
    'depends' => JqueryAsset::className(),
]); ?>
