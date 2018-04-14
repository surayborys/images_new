<?php

namespace frontend\modules\user\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\User;
use yii\helpers\Url;



/**
 * Controller for the user's profile
 */
class ProfileController extends Controller
{
    /**
     * to view user's profile
     * @param integer $nickname
     * @return mixed
     */
    public function actionView($nickname)
    {
        $user = User::find()->where(['nickname' => $nickname])->orWhere(['id' => $nickname])->one();
        $currentUser = Yii::$app->user->identity;
        
        $followers = $user->getFollowersList();
        $subscriptions = $user->getSubscriptionsList();
      
        
        return $this->render('profile', [
            'user' => $user,
            'followers' => $followers,
            'subscriptions' => $subscriptions,
            'currentUser' => $currentUser,
        ]);
    }
    
    /**
     * handles subscription procedure
     */
    public function actionFollow($id)
    {
        if(\Yii::$app->user->isGuest) {
            return $this->redirect(Url::to('/user/default/login')); 
        }
        
        /*@var $loggedUser frontend\models\User */
        $loggedUser = Yii::$app->user->identity;
        /*@var $userToFollow frontend\models\User*/
        $userToFollow = User::find()->where(['id' => $id])->one();
        
        if(!$loggedUser || !$userToFollow){
            return $this->redirect(Url::to(['/user/profile/view', 'nickname'=>$userToFollow->getNickname()]));
        }
        
        $result = $loggedUser->follow($userToFollow);
        return $this->redirect(Url::to(['/user/profile/view', 'nickname'=>$loggedUser->getNickname()]));
    }
    
    /**
     * handles unsubscribing procedure
     */
    public function actionUnsubscribe($id)
    {
        if(\Yii::$app->user->isGuest) {
            return $this->redirect(Url::to('/user/default/login')); 
        }
        
        /*@var $loggedUser frontend\models\User */
        $loggedUser = Yii::$app->user->identity;
        /*@var $userToFollow frontend\models\User*/
        $userToUnsubscribe = User::find()->where(['id' => $id])->one();
        
        if(!$loggedUser || !$userToUnsubscribe){
            return $this->redirect(Url::to(['/user/profile/view', 'nickname'=>$userToFollow->getNickname()]));
        }
        
        $result = $loggedUser->unsubscribe($userToUnsubscribe);
        return $this->redirect(Url::to(['/user/profile/view', 'nickname'=>$userToUnsubscribe->getNickname()]));
    }
}

