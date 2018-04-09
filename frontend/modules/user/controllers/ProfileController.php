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
     * @param integer $nickname
     * @return mixed
     */
    public function actionView($nickname)
    {
        $user = User::find()->where(['nickname' => $nickname])->orWhere(['id' => $nickname])->one();
        
        return $this->render('profile', [
            'user' => $user,
        ]);
    }
    
    /**public function actionGenerate()
    {
        $faker = \Faker\Factory::create();
        
        for ($i=0;$i<1000;$i++) {
            $user = new User([
                'username' => $faker->name,
                'email' => $faker->email,
                'about' => $faker->text(200),
                'nickname' => $faker->regexify('[A-Za-z0-9_]{5,15}'),
                'auth_key' => \Yii::$app->security->generateRandomString(),
                'password_hash' => \Yii::$app->security->generateRandomString(),
                'created_at' => $time = time(),
                'updated_at' => $time,
            ]);
            
            $user->save(false);
        }
    }*/
}

