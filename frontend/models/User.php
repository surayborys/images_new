<?php
namespace frontend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use frontend\models\Post;
use frontend\models\Feed;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property text $about
 * @property integer $type
 * @property string $nickname
 * @property string $picture
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    
    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     *  Get user's nickname or user's id if the user haven't set nickname yet
     *  @return mixed
     */
    public function getNickname()
    {
        return ($this->nickname) ? $this->nickname : $this->getId();
    }
    
    /**
     * handles following procedure
     * @param IdentityInterface $user
     * @return bool
     */
    public function follow(IdentityInterface $user)
    {
        //forbid user to follow by himself
        if(intval($this->id) == intval($user->id)) {
            return false;
        }
        
        $redisKeyForSetUserSubscriptions = 'user:'. intval($this->id).':subscriptions';
        $redisKeyForSetUserFollowers = 'user:'.intval($user->id).':followers';
        
        //initialize redis connection
        $redis = Yii::$app->redis;
        
        $addSubscription = $redis->sadd($redisKeyForSetUserSubscriptions, intval($user->id));
        $addFollower = $redis->sadd($redisKeyForSetUserFollowers, intval($this->id)); 
        
        $checkIfSubscriptionAdded = $redis->sismember($redisKeyForSetUserSubscriptions, intval($user->id));
        $checkIfFollowerAdded = $redis->sismember($redisKeyForSetUserFollowers, intval($this->id));
        
        //if there're no subscription - delete follower
        if(!$checkIfSubscriptionAdded) {
            $redis->srem($redisKeyForSetUserFollowers, intval($this->id));
            return false;
        }
        
        //if the follower hasn't been added - delete subscription
        if(!$checkIfFollowerAdded) {
            $redis->srem($redisKeyForSetUserSubscriptions, intval($user->id));
            return false;
        }
        
        return true;
    }
    
    /**
     * to get the subscriptions list of current user
     * @return array
     */
    public function getSubscriptionsList()
    {
        //initialize redis connection
        $redis = Yii::$app->redis;
        $redisKeyForSetUserSubscriptions = 'user:'. intval($this->id).':subscriptions';
        
        $subscriptionsIds = $redis->smembers($redisKeyForSetUserSubscriptions);
        
        return static::find()->select('id, username, nickname')->where(['id' => $subscriptionsIds])->orderBy('username')->asArray()->all();
    }
    
    /**
     * to get the followers list of current user
     * @return array
     */
    public function getFollowersList()
    {
        //initialize redis connection
        $redis = Yii::$app->redis;
        $redisKeyForSetUserFollowers = 'user:'.intval($this->id).':followers';
        
        $followersIds =  $redis->smembers($redisKeyForSetUserFollowers);
        
        return static::find()->select('id, username, nickname')->where(['id' => $followersIds])->orderBy('username')->asArray()->all();
    }
    
    /**
     * to count the number of user's followers
     * @return int
     */
    public function countFollowers()
    {
        //initialize redis connection
        $redis = Yii::$app->redis;
        $redisKeyForSetUserFollowers = 'user:'.intval($this->id).':followers';
        
        return $redis->scard($redisKeyForSetUserFollowers);
    }
    
    /**
     * to count the number of user's subscriptions
     * @return int
     */
    public function countSubscriptions()
    {
        //initialize redis connection
        $redis = Yii::$app->redis;
        $redisKeyForSetUserSubscriptions = 'user:'.intval($this->id).':subscriptions';
        
        return $redis->scard($redisKeyForSetUserSubscriptions);
    }
    
    /**
     * handles unsupscription procedure
     * @param IdentityInterface $user
     * @return bool
     */
    public function unsubscribe(IdentityInterface $user)
    {
        $redisKeyForSetUserSubscriptions = 'user:'. intval($this->id).':subscriptions';
        $redisKeyForSetUserFollowers = 'user:'.intval($user->id).':followers';
        
        //initialize redis connection
        $redis = Yii::$app->redis;
        
        $removeSubscription = $redis->srem($redisKeyForSetUserSubscriptions, intval($user->id));
        $removeFollower = $redis->srem($redisKeyForSetUserFollowers, intval($this->id)); 
                
        return true;
    }
    
    /**
     * to get mutual subscriptions of current user($this) with the given user's($user) followers
     * 
     * @param IdentityInterface $user
     * @return mixed
     */
    public function getMutualSubscriptionsTo(IdentityInterface $user){
        
        //current user subscriptions
        $key1 = 'user:' . intval($this->id) . ':subscriptions';
        //given user followers
        $key2 = 'user:' . intval($user->id) . ':followers';
        
        //initialize redis connection
        $redis = Yii::$app->redis;
        
        //get mutual values from the sets with keys $key1, $key2
        $ids = $redis->sinter($key1, $key2);
        
        return static::find()->select('id, username, nickname')->where(['id' => $ids])->orderBy('username')->asArray()->all();
    }
    
    /**
     * to check if the current user($this) follows by the given user($user)
     * 
     * @param IdentityInterface $user
     * @return bool
     */
    public function checkIfFollowsBy(IdentityInterface $user){
        
        //current user's subscriptions
        $key1 = 'user:' . intval($this->id) . ':subscriptions';
        
        //initialize redis connection
        $redis = Yii::$app->redis;
        
        return $redis->sismember($key1, $user->id);
    }
    
    /**
     * to get user's profile picture or use default profile picture
     * @return string
     */
    public function getPicture()
    {
        return ($this->picture) ? Yii::$app->storage->getFile($this->picture) : Yii::$app->params['defaultProfileImage'];
    }
    
    /**
     * to check if the user has already liked the post
     * 
     * @param frontend\models\Post $post
     * @return bool
     */
    public function isFavourite(Post $post){
         
        //initialize redis connection
        $redis = Yii::$app->redis;
        
        //current user's liked posts
        $redisKeyForUserLikedPosts = 'user:' . $this->id . ':likes'; //the set stores post ids
        
        return $redis->sismember($redisKeyForUserLikedPosts, $post->id);
        
    }
    
    
    /**
     * @param integer $limit limits a number of posts for displaying
     *  
     * declare linked data for the User $this int the 'feed' table
     *  <p>User has many feeds (frontend\models\Feed) </p>
     */
    public function getFeeds(int $limit) 
    {
        $order = ['post_created_at' => SORT_DESC];
        return $this->hasMany(Feed::className(), ['user_id' => 'id',])->orderBy($order)->limit($limit)->all();
    }
    
    /**
     * get linked data for the User $this in the 'post' table
     * <p>User has many posts (frontend\models\Post) </p>
     * 
     * @return array[] frontend\models\Post
     */
    public function getPosts()
    {
        $order = ['created_at' => SORT_DESC];
        return $this->hasMany(Post::className(), ['user_id' => 'id'])->orderBy($order)->all();
    }
}
