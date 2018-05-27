<?php

namespace frontend\models;

use Yii;
use frontend\models\Post;


/**
 * This is the model class for table "feed".
 *
 * @property int $id
 * @property int $user_id
 * @property int $author_id
 * @property string $author_nickname
 * @property string $author_name
 * @property string $author_picture
 * @property int $post_id
 * @property string $post_filename
 * @property string $post_description
 * @property string $post_created_at
 */
class Feed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feed';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'author_id' => 'Author ID',
            'author_nickname' => 'Author Nickname',
            'author_name' => 'Author Name',
            'author_picture' => 'Author Picture',
            'post_id' => 'Post ID',
            'post_filename' => 'Post Filename',
            'post_description' => 'Post Description',
            'post_created_at' => 'Post Created At',
        ];
    }
    
    /**
     * count number of likes of the post
     * @return integer
     */
    public function countLikesOfThePost()
    {
        return ($post = $this->getPostEntity())?$post->countLikes():false;
    }
    
    /**
     * find frontend\models\Post entity for the current Feed $this
     * @return Post $post|boolean
     */
    public function getPostEntity()
    {
        if($postEntity = Post::findOne(['id' => $this->post_id])) {
            return $postEntity;
        }
        
        return false;
    }
    
    /**
     * count number of comments of the post using redis set with the key 'post:post_id:comments'
     * @return integer
     */
    public function getNumberOfComments()
    {
        $redis = Yii::$app->redis;
        $key = 'post:'. $this->post_id .':comments';
        
        return ($redis->get($key)>0)?$redis->get($key):0;
    }
    
}
