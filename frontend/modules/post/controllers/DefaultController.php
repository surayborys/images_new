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
    
    /**
     * handles unlike procedure
     * 
     * @return mixed|array(JSON)
     */
    public function actionUnlike(){
        
        if(Yii::$app->user->isGuest) {
             
            Yii::$app->session->setFlash('danger', 'Please, login to UNLIKE the post');
            return $this->redirect(['/user/default/login']);
        }
        
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
        if(Yii::$app->user->isGuest) {
             
            Yii::$app->session->setFlash('danger', 'Please, login to COMMENT the post');
            return $this->redirect(['/user/default/login']);
        } 
        //get data, sended by JavaScript with the "POST" method
        $text = Yii::$app->request->post('text');
        $postId = Yii::$app->request->post('postId');
        /* @var post frontend/models/Post */
        $post = $this->findPostById($postId);
        /* @var currentUser frontend/models/User  */
        $currentUser = Yii::$app->user->identity;
        
        if(!$comments = $post->comment($currentUser, $text)){
            return false;
        }
        
        /*build an array to be transfered as a result*/
        $authorizedComments = [];
        foreach ($comments as $comment) {
            $id = $comment->id;
            $authorizedComments[$id]['user_id'] = $comment->user_id;
            $authorizedComments[$id]['post_id'] = $comment->post_id;
            $authorizedComments[$id]['text'] = $comment->text;
            $authorizedComments[$id]['authorname'] = $comment->user->username; //comment has one user
            $authorizedComments[$id]['authorpicture'] = $comment->user->getPicture();
            $authorizedComments[$id]['id'] = $comment->id;
            $authorizedComments[$id]['updated_at'] = $comment->updated_at;
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return [
            'success' => true,
            'numberOfComments' => count($comments),
            'comments' => $authorizedComments,
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
        
        if(Yii::$app->user->isGuest) {
             
            Yii::$app->session->setFlash('danger', 'Please, login to DELETE the post');
            return $this->redirect(['/user/default/login']);
        } 
        
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
        return ($isCommentOfTransferedPost) ? $comment->delete() : $this->redirect(['/']);       
        
    }
    
    private function findCommentById($id)
    {
        if($comment = Comment::findOne($id)){
           return $comment;
        }
        
        throw new NotFoundHttpException;
    }
    
}
