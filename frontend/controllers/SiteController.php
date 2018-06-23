<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use frontend\models\User;
use frontend\models\Feed;
use yii\web\Cookie;


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
    
    /**
     * handles language selection
     * <p>write selected language to a cookie and add this cookie to the server resopnse</p>
     * 
     * @return mixed
     */
    public function actionLanguage()
    {
        $language = Yii::$app->request->post('lang');
        $supportedLanguages = Yii::$app->params['supportedLanguages'];
        
        //check if the language, selected by user, is supported in our application
        if(!in_array($language, $supportedLanguages)){
            Yii::$app->session->setFlash('danger', 'Unsupported language');
            return $this->redirect(Yii::$app->request->referrer);
        }
        
        Yii::$app->language = $language;
        
        //write selected language to COOKIES
        $langCookie = new Cookie([
            'name' => 'lang',
            'value' => $language,
            'expire' => time() + 60*60*24*30, //30 days
        ]);
        
        //add langCookie to the server's response
        Yii::$app->response->cookies->add($langCookie);
        
        return $this->goHome();
    }
}
