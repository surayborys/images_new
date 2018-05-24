<?php

namespace frontend\modules\post\models\forms;

use yii\base\Model;
use Yii;
use frontend\models\User;
use frontend\models\Post;
use Intervention\Image\ImageManager;

/**
 * Form model for adding a new post
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class PostForm extends Model{
    
    const MAX_DESCRIPTION_LENGTH = 1500;
    
    public $picture;
    public $description;
    
    private $user;
    
    /**
     * to save the frontend\models\User Object in the private property $user
     * to use the self::EVENT_AFTER_VALIDATE event for resizing post picture
     * @param User $user
     */
    public function __construct(User $user) {
        $this->user = $user;
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'resizePicture']);
    }
    
    public function rules() {
        return[
            [['picture'], 'file',
                'skipOnEmpty' => TRUE,
                'extensions' => ['jpg', 'png'],
                'checkExtensionByMimeType' => true,
                'maxSize' => $this->getMaxFileSize()],
            [['description'], 'string',
                'max' => self::MAX_DESCRIPTION_LENGTH],
        ];
    }
    
    /**
     *  to set a new post's attributes and save it to the "post" table using the frontend\models\Post model
     */
    public function save(){
        
        if($this->validate()){
            $post = new Post();
            $post->user_id = $this->user->getId();
            $post->filename = Yii::$app->storage->saveUploadedFile($this->picture);
            $post->created_at = time();
            $post->description = $this->description;
            return $post->save(false);
        }
    }

    /**
     * to grab max file size from the Yii params
     * @return integer
     */
    private function getMaxFileSize(){
        return Yii::$app->params['maxFileSize'];
    }
    
    /**
     * handles picture resizing, if it's size is larger than setted params 
     * <p>use InterventionImage library  http://image.intervention.io/</p>
     * @return mixed
     */
    public function resizePicture(){
        
        if($this->picture->error){
            /*the UploadedFile object has the error property. If $error contains '1'
              it means that an error has been occured, and we have to stop script execution*/
            return;
        }
        $maxHeight = Yii::$app->params['postPicture']['maxHeight'];
        $maxWidth = Yii::$app->params['postPicture']['maxWidth'];
        
        $manager = new ImageManager(array('driver' => 'imagick'));
       
        $image = $manager->make($this->picture->tempName);
        
        //the third argument is optional and use callback function constraint
        $image->resize($maxWidth, $maxHeight, function ($constraint) {
            //save aspect ration
            $constraint->aspectRatio();
            //don't resize image, if it's height and width is smaller, than $maxWidth, $maxHeight
            $constraint->upsize();
        })->save();
   }
    
}
