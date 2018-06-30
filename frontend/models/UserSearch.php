<?php

namespace frontend\models;

use Yii;
use yii\helpers\Html;

/**
 * Search from the "user" table by keyword
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class UserSearch {
    
    /**
     * execute search 
     * 
     * @param string $keyword
     * @return array|bool(FALSE)
     */
    public function simpleSearch($keyword)
    {
        $encodedKeyword = Html::encode($keyword);
        
        $sql = "SELECT id, nickname, username, picture FROM user WHERE username LIKE '%$encodedKeyword%'";
        
        $users =  Yii::$app->db->createCommand($sql)->queryAll();
        
        return $this->prepareResultArray($users);
    }
    
    /**
     * execute search using index on usernamr field
     * 
     * @param string $keyword
     * @return array|bool(FALSE)
     */
    public function searchOnUsernameIndex($keyword)
    {
        $encodedKeyword = Html::encode($keyword);
        
        $sql = "SELECT id, nickname, username, picture FROM user WHERE MATCH(username) AGAINST ('$encodedKeyword')";
        
        $users =  Yii::$app->db->createCommand($sql)->queryAll();
        
        return $this->prepareResultArray($users);
    }
    
    /**
     * bring the result to a convenient form 
     * 
     * @param array $users
     * @return array|bool(FALSE)
     */
    private function prepareResultArray($users) 
    {
        if(is_array($users) && !empty($users)) {
            $preparedArray = [];
            
            foreach ($users as $user) {
                $preparedArray[$user['id']]['id'] = $user['id'];
                $preparedArray[$user['id']]['nickname'] = (isset($user['nickname']) && !empty($user['nickname'])) ? $user['nickname'] : $user['id'];
                $preparedArray[$user['id']]['picture'] = (isset($user['picture']) && !empty($user['picture'])) ? Yii::$app->storage->getFile($user['picture']) : Yii::$app->params['defaultProfileImage'];
                $preparedArray[$user['id']]['username'] = $user['username'];
            }
            
            return $preparedArray;
        }
        
        return false;
    }
}
