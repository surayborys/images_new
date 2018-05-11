<?php

namespace frontend\models;

use frontend\models\User;
use Yii;


/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string $description
 * @property int $created_at
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'filename' => 'Filename',
            'description' => 'Description',
            'created_at' => 'Created At',
        ];
    }
    
    /**
     * get an author of the post
     * @return $user frontend\models\User Object;
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id',
        ]);
    }
    
    /**
     * to add the current user's like to the post $this
     * @param frontend/models/User $user
     * @return boolean
     */
    public function like(User $user)
    {
        $redisKeyForLikes = 'post:' . $this->id . ':likes'; //the set will store user ids
        $redisKeyForUserLikedPosts = 'user:' . $user->id . ':likes'; //the set will store post ids
        
        //initialize redis connection
        $redis = Yii::$app->redis;
        
        $redis->sadd($redisKeyForLikes, $user->id);
        $redis->sadd($redisKeyForUserLikedPosts, $this->id);
        
        return true;
    }
    
    /**
     * to count a number of likes of the post
     * @return integer
     */
    public function countLikes()
    {
        //initialize redis connection
        $redis = Yii::$app->redis;
        
        $redisKeyForLikes = 'post:' . $this->id . ':likes'; 
        return $redis->scard($redisKeyForLikes);
        
    }
    
    public function unlike(User $user)
    {
        $redisKeyForLikes = 'post:' . $this->id . ':likes'; //the set will store user ids
        $redisKeyForUserLikedPosts = 'user:' . $user->id . ':likes'; //the set will store post ids
        
        //initialize redis connection
        $redis = Yii::$app->redis;
        
        $redis->srem($redisKeyForLikes, $user->id);
        $redis->srem($redisKeyForUserLikedPosts, $this->id);
        
        return true;
    }
}
