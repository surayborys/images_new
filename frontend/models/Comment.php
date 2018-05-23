<?php

namespace frontend\models;

use Yii;
use frontend\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;


/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $text
 * @property int $created_at
 * @property int $updated_at
 */
class Comment extends \yii\db\ActiveRecord
{
    const MAX_COMMENT_LENGTH = 2000;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }
    
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'user_id'], 'required'],
            [['post_id', 'user_id'], 'integer'],
            [['text'], 'string', 'max' => self::MAX_COMMENT_LENGTH],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'user_id' => 'User ID',
            'text' => 'Text',
        ];
    }
    
    /**
     * to get comment's author frontend\models\User from the 'user' table
     * @return mixed
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id',
        ]);
    }
}
