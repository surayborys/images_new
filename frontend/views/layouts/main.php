<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\assets\FontAwesomeAsset;
use common\widgets\Alert;
use yii\helpers\Url;

AppAsset::register($this);
FontAwesomeAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<?php $this->beginBody() ?>
<body class="home-page">

    <div class="wrapper">
        <!------------------------------------------HEADER-------------------------------------->
        <header>                
            <div class="header-top">
                <div class="container">
                    <div class="col-md-4 col-sm-4 col-md-offset-4 col-sm-offset-4 brand-logo">
                        <h1>
                            <a href="<?=Url::to(['/site/index'])?>">
                                <img src="/images/logo.png" alt="logo" id="images-logo">
                            </a>
                        </h1>
                    </div>			
                    <div class="col-md-4 col-sm-4 navicons-topbar">
                        <ul>
                            <li class="blog-search">
                                <a href="#" title="Search"><i class="fa fa-search"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <div class="header-main-nav">
                <div class="container">
                    <div class="main-nav-wrapper">
                        <nav class="main-menu">
                            <?php 
                                
                                if (!Yii::$app->user->isGuest){
                                    $menuItems = [
                                        ['label' => 'Newsfeed', 'url' => ['/site/index']],
                                        ['label' => 'My page', 'url' => Url::to(['/user/profile/view', 'nickname'=> Yii::$app->user->identity->getNickname()])],
                                        ['label' => 'Create post', 'url' => Url::to(['/post/default/create'])]
                                    ];
                                }
                                if (Yii::$app->user->isGuest) {
                                    $menuItems[] = ['label' => 'Signup', 'url' => ['/user/default/signup']];
                                    $menuItems[] = ['label' => 'Login', 'url' => ['/user/default/login']];
                                } else {
                                    $menuItems[] = '<li>'
                                        . Html::beginForm(['/user/default/logout'], 'post')
                                        . Html::submitButton(
                                            'Logout (' . Yii::$app->user->identity->username . ')<i class="fa fa-sign-out"></i>',
                                            ['class' => 'btn btn-link logout']
                                        )
                                        . Html::endForm()
                                        . '</li>';
                                }
                                echo Nav::widget([
                                    'options' => ['class' => 'menu navbar-nav navbar-right'],
                                    'items' => $menuItems,
                                ]);
                            ?>
                            <!--ul class="menu">
                                <li>
                                    <a href="#">Newsfeed</a>                                        
                                </li>
                                <li>
                                    <a href="#">My page</a>
                                </li>
                                <li>
                                    <a href="#">Logout <i class="fa fa-sign-out"></i></a>
                                </li>
                            </ul-->
                        </nav>				
                    </div>
                </div>
            </div>

        </header> 
        <!-----------------------------------------/HEADER-------------------------------------->
    
        <!------------------------------------------MAIN CONTENT-------------------------------->
        <div class="container full">
            <?=Alert::widget();?>
            <?=$content;?>
        </div>
        <div class="push"></div>
    </div>
        <!-----------------------------------------/MAIN CONTENT-------------------------------->
        <!------------------------------------------FOOTER-------------------------------------->
    <footer>                
        <div class="footer">
            <div class="back-to-top-page">
                <a class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
            </div>
            <p class="text">Images | 2018</p>
        </div>
    </footer>
        <!-----------------------------------------/FOOTER-------------------------------------->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
