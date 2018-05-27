<?php

namespace frontend\models\events;

use frontend\models\User;
use frontend\models\Post;
use yii\base\Event;


/**
 * PostCreatedEvent
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class PostCreatedEvent extends Event {
   
    /**
     *
     * @var User
     */
    public $user;
    
    /**
     *
     * @var Post;
     */
    public $post;
    
    public function getUser() : User
    {
        return $this->user;
    }
    
    public function getPost() : Post
    {
        return $this->post;
    }
}
