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
}
