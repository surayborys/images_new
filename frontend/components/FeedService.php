<?php

namespace frontend\components;

use yii\base\Component;
use yii\base\Event;
use frontend\models\Feed;
use Yii;

/**
 * Description of FeedService
 *
 * @author Borys Suray <surayborys@gmail.com>
 */
class FeedService extends Component {
    
    public function addToFeeds(Event $event){
        
        $currentUser = $event->getUser();
        $post = $event->getPost();
        
        $followers = $currentUser->getFollowersList();
        
        foreach ($followers as $follower) {
            $feed = new Feed();
            
            $feed->user_id = $follower['id'];
            $feed->author_id = $currentUser->getId();
            $feed->author_name = $currentUser->username;
            $feed->author_nickname = $currentUser->getNickname();
            $feed->author_picture = $currentUser->getPicture();
            $feed->post_id = $post->getId();
            $feed->post_filename = $post->filename;
            $feed->post_description = $post->description;
            $feed->post_created_at = $post->created_at;
            
            $feed->save();            
        }
        
        
    }
}
