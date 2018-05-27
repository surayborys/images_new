<?php

namespace frontend\models;

use frontend\models\User;
use frontend\models\Comment;
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
    
    /**
     * to remove the current user's like from the post $this
     * 
     * @param frontend/models/User $user
     * @return boolean
     */
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
    
    /**
     * add the comment for the post $this to the "comment" table 
     * 
     * @param frontend/models/User $user
     * @param string $text
     * @return boolean|array of objects 'frontend/model/Comment'
     */
    public function comment(User $user, string $text)
    {
        $comment = new Comment();
        
        $comment->user_id = $user->id;
        $comment->post_id = $this->id;
        $comment->text = $text;
        
        if($comment->validate() && $comment->save()) {
            $redis = Yii::$app->redis;
            $key = 'post:'. $this->id.':comments';
            $redis->incr($key);
            
            return true;
        }
        
        return false;  
    }
    
    /**
     *  declare linked data for the Post $this
     *  <p>Post has many comments (frontend/models/Comment) </p>
     */
    public function getComments() 
    {
        return $this->hasMany(Comment::className(), [
            'post_id' => 'id',
        ]);
    }
    
    /**
     * to build an array of current post $this comments
     * 
     * @return array
     */
    public function prepareCommentsAsArray()
    {
        $comments = $this->comments; //$post has many comments
        
        /*build an array to be transfered as a result*/
        $preparedComments = [];
        foreach ($comments as $comment) {
            $id = $comment->id;
            $preparedComments[$id]['user_id'] = $comment->user_id;
            $preparedComments[$id]['post_id'] = $comment->post_id;
            $preparedComments[$id]['text'] = $comment->text;
            $preparedComments[$id]['authorname'] = $comment->user->username; //comment has one user
            $preparedComments[$id]['authorpicture'] = $comment->user->getPicture();
            $preparedComments[$id]['id'] = $comment->id;
            $preparedComments[$id]['updated_at'] = $comment->updated_at;
        }
        
        return $preparedComments;
    }
    
    public function getId(){
        return $this->getPrimaryKey();
    }
}
