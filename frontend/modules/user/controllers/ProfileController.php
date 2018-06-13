<?php

namespace frontend\modules\user\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\User;
use frontend\modules\user\models\EditProfileForm;
use yii\helpers\Url;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;
use frontend\modules\user\models\forms\PictureForm;
use yii\web\UploadedFile;
use yii\web\Response;




/**
 * Controller for the user's (frontend\models\User) profile
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
        $user = $this->getUserByNickname($nickname);
        $currentUser = Yii::$app->user->identity;
        
        $followers = $user->getFollowersList();
        $subscriptions = $user->getSubscriptionsList();
        
        $pictureModel = new PictureForm();
        
        
        return $this->render('profile', [
            'user' => $user,
            'followers' => $followers,
            'subscriptions' => $subscriptions,
            'currentUser' => $currentUser,
            'pictureModel' => $pictureModel,
            
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
        $userToFollow = $this->getUserById($id);
        
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
        $userToUnsubscribe = $this->getUserById($id);
        
        if(!$loggedUser || !$userToUnsubscribe){
            return $this->redirect(Url::to(['/user/profile/view', 'nickname'=>$userToFollow->getNickname()]));
        }
        
        $result = $loggedUser->unsubscribe($userToUnsubscribe);
        return $this->redirect(Url::to(['/user/profile/view', 'nickname'=>$userToUnsubscribe->getNickname()]));
    }
    
    /**
     * <b>to edit user's with id = $id profile and save changes to the 'user' table</b>
     * 
     * <p>gives access only for logged users. allows to edit only logged user's own profile</p>
     * 
     * @param int $id
     * @return mixed
     */
    public function actionEdit($id){
               
        if(!$this->checkAccessForProfileEdition($id)) {
            Yii::$app->session->setFlash('error', 'unexpected identifier.');
            return $this->redirect(Url::to(['/site/index']));
        }
        
        $userData = User::find()->select('username, nickname, type, about')->where(['id'=>intval($id)])->one();
        
        /*@var IdentityInterface $user*/
        $user = $this->getUserById(intval($id));
        $editProfileForm = new EditProfileForm($id);
        $editProfileForm->scenario = EditProfileForm::SCENARIO_PROFILE_UPDATE;
        
        if($editProfileForm->load(Yii::$app->request->post()) && $editProfileForm->validate()) {
            if($editProfileForm->update($user)) {                
               Yii::$app->session->setFlash('success', 'Profile data successfully updated.');
            } else {
                Yii::$app->session->setFlash('error', 'Error while trying to update profile. Please, try again later');
            }
            return $this->redirect(Url::to(['/user/profile/view', 'nickname'=> $user->getNickname()]));
        }
        
        $editProfileForm->username = $userData->username;
        $editProfileForm->nickname = $userData->nickname;
        $editProfileForm->about = $userData->about;
        $editProfileForm->type = $userData->type;
        
        
       if($editProfileForm->validate()){
            return $this->render('edit', [
                'editProfileForm' => $editProfileForm,
            ]);
        }else{
            Yii::$app->session->setFlash('error', 'Enable to load profile data. We\'re working to fix the problem as soon as posible');
            return $this->actionMailToAdmin($user);
        }        
    }
    
    /**
     * to send email to admin with a validation error description
     * 
     * @param IdentityInterface $user
     * @return mixed
     */
    private function actionMailToAdmin(IdentityInterface $user) {
        
        if(!$this->checkAccessForProfileEdition($user->id)) {
            Yii::$app->session->setFlash('error', 'unexpected identifier.');
            return $this->redirect(Url::to(['/site/index']));
        }
        
        $adminEmail = Yii::$app->params['adminEmail'];  
        
        $result = Yii::$app->mailer->compose()
                ->setFrom('boryssuray@gmail.com')
                ->setTo($adminEmail)
                ->setSubject('ERROR VALIDATING PROFILE')
                ->setTextBody('CHECK DATABASE RECORD IN THE USER TABLE. USER ID  = ' . $user->id)
                ->setHtmlBody('<b>CHECK DATABASE RECORD IN THE USER TABLE. USER ID = </b>' . $user->id)
                ->send();
        
        return $this->redirect(Url::to(['/user/profile/view', 'nickname'=> $user->getNickname()]));
    }
    
    /**
     * check, if the logged user Yii::$app->user->identity and the user with id = $id, whose profile is trying to be changed, are identical
     * @param integer $id
     * @return bool
     */
    private function checkAccessForProfileEdition($id){
        if(\Yii::$app->user->isGuest || intval(\Yii::$app->user->identity->id) !== intval($id)){
            return false;
        }
        return true;
    }
    
    /**
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function getUserById($id){
        if ($user =  User::find()->where(['id' => $id])->one()){
            return $user;
        }        
        throw new NotFoundHttpException();
    }
    
    /**
     * @param string $nickname
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function getUserByNickname($nickname){
        if ($user =  User::find()->where(['nickname' => $nickname])->orWhere(['id' => $nickname])->one()){
            return $user;
        }
        throw new NotFoundHttpException();
    }
    
    /**
     * upload profile picture and attach it to user
     * @return array
     */
    public function actionUploadPicture(){
        
        //set server responce format to JSON
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new PictureForm();
        $model->picture = UploadedFile::getInstance($model, 'picture');
        
        if($model->validate()) {
            
            //attach picture to the user
            $user = Yii::$app->user->identity;
            $user->picture = Yii::$app->storage->saveUploadedFile($model->picture);
            
            if($user->save(false, ['picture'])) {
                return [
                    'success' => true,
                    'pictureUri' => Yii::$app->storage->getFile($user->picture),
                ];
            }
        } 
        
        return [
            'success' => false,
            'errors' => $model->getErrors(),
        ];
    }
    
    /**
     * unset user's profile picture
     * @param int $id
     * @return mixed
     */
    public function actionUnsetPicture($id)
    {
        if($this->checkAccessForProfileEdition($id)){
            $user = $this->getUserById($id);
            $fileToDelete = $user->picture;
            $user->picture = null;
            if($user->save(false, ['picture']) && Yii::$app->storage->deleteFile($fileToDelete)){
                Yii::$app->session->setFlash('success', 'Profile image has been successfully unseted.');
            }
            
            return $this->redirect(Url::to(['/user/profile/view', 'nickname'=> $user->getNickname()]));
        }
        Yii::$app->session->setFlash('error', 'Enable to unset image');
        return $this->redirect(Url::to('site/index'));        
    }
}

