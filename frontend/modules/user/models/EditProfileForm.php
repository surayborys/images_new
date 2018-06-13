<?php

namespace frontend\modules\user\models;

use yii\base\Model;
use yii\web\IdentityInterface;

/**
 * Edit Profile Form
 * 
 * @property string $username
 * @property string $nickname
 * @property int $type
 * @property string $about 
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class EditProfileForm extends Model {
    
    const SCENARIO_PROFILE_UPDATE = 'profile_update';
    
    public $username;
    public $nickname;
    public $type;
    public $about;
    
    private $userId;
    
    public function __construct($id) {
        $this->userId = $id;
    }


    /**
     * describes scenario @const SCENARIO_PROFILE_UPDATE
     * @return array 
     */
    public function scenarios() {
        return [
            self::SCENARIO_PROFILE_UPDATE => [
                'username', 'nickname','about', 'type',
            ],
        ];        
    }
    
    /**
     * sets validation rules
     * @return array
     */
    public function rules() {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            
            ['nickname', 'trim'],
            ['nickname', 'string', 'min' => 2, 'max' => 20],
            ['nickname', 'unique', 'targetClass' => '\frontend\models\User', 'filter' => ['!=', 'id', $this->userId],  'message' => 'This nickname has already been taken.'],
            
            ['about', 'string'],
            
            ['type', 'integer', 'min' => 1, 'max' => 2],
        ];
    }
    
    /**
     * to save edited user's profile data to the table "user"
     * @param IdentityInterface $user
     * @return bool
     */
    public function update(IdentityInterface $user) {
         
       
        $user->username = $this->username;
        $user->nickname = ($this->nickname) ? $this->nickname : '';
        $user->about = ($this->about) ? $this->about : '';
        $user->type = $this->type;
        
        return ($user->save()) ? true : false;
    }
}
