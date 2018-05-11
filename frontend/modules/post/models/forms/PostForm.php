<?php

namespace frontend\modules\post\models\forms;

use yii\base\Model;
use Yii;
use frontend\models\User;
use frontend\models\Post;

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
     * @param User $user
     */
    public function __construct(User $user) {
        $this->user = $user;
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
    
}
