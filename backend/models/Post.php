<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string $description
 * @property int $created_at
 * @property int $complaints
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
     *  use the EVENT_BEFORE_DELETE event for deleting post picture, likes, comments and complaints
     */
    public function init() {
        $this->on(self::EVENT_BEFORE_DELETE, [$this, 'clearGarbage']);
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
            'complaints' => 'Complaints',
        ];
    }
    
    /**
     * set to zero 'complaints' conter in the "post" table and remove the corresponding key from current post $this from redis
     * 
     * @return boolean
     */
    public function aprove()
    {
        $redis = new Yii::$app->redis;
        
        $key = 'postcomplains:'.$this->id;
        
        if($redis->del($key)) {
            $this->complaints = 0;
            $this->update(false);
            return true;
        }
        
        return false;
    }
    
    /**
     * clear linked data for deleting post: likes, comments, complains from redis and remove post picture file
     * 
     * @return boolean
     */
    public function clearGarbage()
    {
        $redis = new Yii::$app->redis;
        Yii::$app->storage->deleteFile($this->filename);
        
        $keys = [];
        $keys['key_for_complaints'] = 'postcomplains:'.$this->id;
        $keys['key_for_likes'] = 'post:' . $this->id . ':likes';
        $keys['key_for_comments'] = 'post:'. $this->id.':comments';
        
        foreach ($keys as $key)
        {
            $redis->del($key);
        }
        
        return true;        
    }
}
