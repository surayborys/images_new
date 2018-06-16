<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use Yii;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'id',
                'format' => 'html',
                'value' => function($data) {
                    return Html::a($data->id, [Url::to('/complaints/manage/view'), 'id' => $data->id]);
                }
            ],
            'user_id',
            [
                'format' => 'html',
                'value' => function($data) {
                    return Html::img(Yii::$app->storage->getFile($data->filename),[
                        'width' => '300px',
                    ]);
                }
            ],
            'description:ntext',
            'created_at:datetime',
            'complaints',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}&nbsp&nbsp{aprove}&nbsp&nbsp{delete}',
                'buttons' => [
                    'aprove' => function($url, $data) {
                        return Html::a('<span class = "glyphicon glyphicon-ok"></span>', ['aprove', 'id'=>$data->id]);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
