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
use frontend\models\Comment;

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
        $this->checkAccess();
        
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
     * handles report procedure
     * 
     * @return mixed|array(JSON)
     */
    public function actionReport()
    {
        $this->checkAccess();
        
        $postId = Yii::$app->request->post('id');
        /* @var post frontend/models/Post */
        $post = $this->findPostById($postId);
        
        /* @var currentUser frontend/models/User  */
        $currentUser = Yii::$app->user->identity;
        
        $post->report($currentUser);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return [
            'success' => true,
        ];
        
    }

    /**
     * handles like procedure
     * 
     * @return mixed|array(JSON)
     */
    public function actionLike(){
        
        $this->checkAccess();
        
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
    
    /**
     * handles unlike procedure
     * 
     * @return mixed|array(JSON)
     */
    public function actionUnlike(){
        
        $this->checkAccess();
        
        //get data, sended with method "POST" from JavaScript
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
    
    /**
     * handles adding comment to the post
     * 
     * @return boolean|JSON
     */
    public function actionComment()
    {
        $this->checkAccess(); 
        //get data, sended by JavaScript with the "POST" method
        $text = Yii::$app->request->post('text');
        $postId = Yii::$app->request->post('postId');
        /* @var post frontend/models/Post */
        $post = $this->findPostById($postId);
        /* @var currentUser frontend/models/User  */
        $currentUser = Yii::$app->user->identity;
        
        if($post->comment($currentUser, $text)){
            $comments = $post->prepareCommentsAsArray();
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return [
            'success' => true,
            'numberOfComments' => $post->getNumberOfComments(),
            'comments' => $comments,
        ];
    }
    
    /**
     * handles deleting comment 
     * 
     * @return mixed
     */
    public function actionDeleteComment()
    {
        /*get params from the Javascript (transfered by the POST method)*/
        $postId = Yii::$app->request->post('postId');
        $commentId = Yii::$app->request->post('commentId');
        /* @var post frontend/models/Post */
        $post = $this->findPostById($postId);
        
        $this->checkAccess();
        
        if($post->user->id != Yii::$app->user->identity->id) {
            
            Yii::$app->session->setFlash('danger', 'non-permitted action!');
            return $this->redirect(['/']);
        }
        
        /* @var post frontend/models/Comment */
        $comment = $this->findCommentById($commentId);
        $postComments = $post->comments;
        $isCommentOfTransferedPost = false;
        foreach ($postComments as $postComment) {
            if($postComment->id == $comment->id){
                $isCommentOfTransferedPost = true;
            }
        }
              
        
        if($isCommentOfTransferedPost){
            if($comment->delete()){
                $redis = Yii::$app->redis;
                $key = 'post:'.$postId.':comments';
                $redis->decr($key);
                return true;
            }
        } else {
            return $this->redirect(['/']);
        }     
    }
    
    /**
     * handles comment edition 
     * 
     * @return boolean|JSON
     */
    public function actionEditComment()
    {
        $this->checkAccess();
        //get data, sended by JavaScript with the "POST" method
        $text = Yii::$app->request->post('text');
        $postId = Yii::$app->request->post('postId');
        $commentId = Yii::$app->request->post('commentId');
        
        /* @var post frontend/models/Post */
        $post = $this->findPostById($postId);
        /* @var post frontend/models/Post */
        $comment = $this->findCommentById($commentId);
        /* @var currentUser frontend/models/User  */
        $currentUser = Yii::$app->user->identity;
        
        if($comment->user_id != $currentUser->id) {
            Yii::$app->session->setFlash('danger', 'non-permitted action!');
            return $this->redirect(['/']);
        }
        
        $comment->text = $text;
        if($comment->validate()&& $comment->update()){
            $comments = $post->prepareCommentsAsArray();
        } else { return false; }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return [ 'success' => true,
                 'numberOfComments' => count($comments),
                 'comments' => $comments, ];
    }
    
    /**
     * find a record in the 'comment' table by it's id
     * 
     * @param integer $id
     * @return $post frontend\models\Comment Object;
     * @throws NotFoundHttpException
     */
    private function findCommentById($id)
    {
        if($comment = Comment::findOne($id)){
           return $comment;
        }
        
        throw new NotFoundHttpException;
    }
    
    /**
     * redirect to login page, if the user is not logged in
     * @return mixed
     */
    private function checkAccess()
    {
        if(Yii::$app->user->isGuest) {
             
            Yii::$app->session->setFlash('danger', 'Please, login to continue');
            return $this->redirect(['/user/default/login']);
        }
    }
    
}
