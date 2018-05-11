<?php

namespace frontend\modules\post\controllers;

use yii\web\Controller;
use yii\web\UploadedFile;
use frontend\modules\post\models\forms\PostForm;
use Yii;
use yii\helpers\Url;
use frontend\models\Post;
use yii\web\NotFoundHttpException;
use yii\web\Response;
/**
 * Default controller for the `post` module
 */
class DefaultController extends Controller
{
    /**
     * action handles creatong a new post
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect(Url::to('/user/default/login')); 
        }
        
        $currentUser = Yii::$app->user->identity;
        $model = new PostForm($currentUser);
        
        if($model->load(Yii::$app->request->post())){
            
            $model->picture = UploadedFile::getInstance($model, 'picture');
            
            if($model->save()){
                Yii::$app->session->setFlash('success', 'A new post has been created');
            }
            return $this->redirect(Url::to(['/user/profile/view', 'nickname'=>$currentUser->getNickname()]));
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    /**
     * handles rendring a single post object to the 'single' view
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('single', [
            'post' => $this->findPostById($id),
        ]);
    }
    
    /**
     * find a record in the 'post' table by it's id
     * 
     * @param integer $id
     * @return $post frontend\models\Post Object;
     * @throws NotFoundHttpException
     */
    private function findPostById($id)
    {
        if($post = Post::findOne($id)){
           return $post;
        }
        
        throw new NotFoundHttpException;
    }
    
    /**
     * handles like procedure
     * 
     * @return mixed|array(JSON)
     */
    public function actionLike(){
        
        if(Yii::$app->user->isGuest) {
             
            Yii::$app->session->setFlash('danger', 'Please, login to LIKE the post');
            return $this->redirect(['/user/default/login']);
        }
        
        $postId = Yii::$app->request->post('id');
        /* @var post frontend/models/Post */
        $post = $this->findPostById($postId);
        
        /* @var currentUser frontend/models/User  */
        $currentUser = Yii::$app->user->identity;
        
        $post->like($currentUser); 
        $numberOfLikes = $post->countLikes();
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return [
            'success' => true,
            'numberOfLikes' => $numberOfLikes,
        ];
    }
    
    public function actionUnlike(){
        
        if(Yii::$app->user->isGuest) {
             
            Yii::$app->session->setFlash('danger', 'Please, login to UNLIKE the post');
            return $this->redirect(['/user/default/login']);
        }
        
        $postId = Yii::$app->request->post('id');
        /* @var post frontend/models/Post */
        $post = $this->findPostById($postId);
        
        /* @var currentUser frontend/models/User  */
        $currentUser = Yii::$app->user->identity;
        
        $post->unlike($currentUser); 
        $numberOfLikes = $post->countLikes();
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return [
            'success' => true,
            'numberOfLikes' => $numberOfLikes,
        ];
    }
}
