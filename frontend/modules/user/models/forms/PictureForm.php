<?php

namespace frontend\modules\user\models\forms;

use Intervention\Image\ImageManager;
use yii\base\Model;
use Yii;

/**
 * This is the model for the uploaded picture
 * @property $picture
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class PictureForm extends Model
{
    /*switch on event handler*/
    public function __construct() {
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'resizePicture']);
    }

    public $picture;

    public function rules()
    {
        return [
            [['picture'], 'file',
                'extensions' => ['jpg'],
                'checkExtensionByMimeType' => true,
                'maxSize' => Yii::$app->params['maxFileSize'],
            ],
        ];
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
        $maxHeight = Yii::$app->params['profilePicture']['maxHeight'];
        $maxWidth = Yii::$app->params['profilePicture']['maxWidth'];
        
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
