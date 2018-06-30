<?php

namespace frontend\models\forms;

use yii\base\Model;
use frontend\models\UserSearch;
use Yii;

/**
 * handles search by keyword using the frontend\UserSearch model
 * 
 * @property string $keyword
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class SearchForm extends Model{
    
    public $username;
    
    //set validation rules
    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'trim'],
            [
                'username',
                'string',
                'min' => Yii::$app->params['minUsernameLength'],
                'max' => Yii::$app->params['maxUsernameLength'],
            ]
        ];
    }
    
    
    /**
     * 
     * @return array|bool(FALSE)
     */
    public function search() 
    {
        //use frontend\UserSearch model for searching
        if($this->validate()) {
            $searchModel = new UserSearch();
            return $searchModel->searchOnUsernameIndex($this->username);
        }
    }
}
