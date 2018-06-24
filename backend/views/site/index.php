<?php

use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>ADMIN PANEL</h1>

        <p class="lead">Manage Images.</p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-6 text-center">
                <h2>Complaints</h2>

                <p>Check user posts, which have complaints</p>

                <p><a class="btn btn-default" href="<?php echo Url::to('/complaints/manage/index')?>">Manage&raquo;</a></p>
            </div>
            <div class="col-lg-6 text-center">
                <h2>Users</h2>

                <p>Manage users</p>

                <p><a class="btn btn-default" href="<?php echo Url::to('/user/manage/index')?>">Manage&raquo;</a></p>
            </div>
        </div>

    </div>
</div>

