<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use frontend\models\User;
use frontend\models\Feed;


/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest) {
             
            Yii::$app->session->setFlash('danger', 'Please, login to continue');
            return $this->redirect(['/user/default/login']);
        }
        
        $limit = Yii::$app->params['feedLimit'];
        
        /**
         * @var User $currentUser
         */
        $currentUser = Yii::$app->user->identity;
        
        /**
         * @var Feed $feedItems[] 
         */
        $feedItems = $currentUser->getFeeds($limit);
        
        return $this->render('index', [
            'currentUser' => $currentUser,
            'feedItems' => $feedItems,
        ]);
    }
}
