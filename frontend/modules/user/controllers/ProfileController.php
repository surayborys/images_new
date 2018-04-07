<?php

namespace frontend\modules\user\controllers;

use yii\web\Controller;
use frontend\models\User;



/**
 * Controller for the user's profile
 */
class ProfileController extends Controller
{
    /**
     * to view user's profile
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $user = User::find()->where(['id' => $id])->one();
        $username = $user->username;
        
        return $this->render('profile', [
            'username' => $username,
        ]);
    }
}

