//handles like mechanism via AJAX

(function($, undefined){
    //$ == jQuery
     var $feedsRow = $('#feedItemsRow');
     
    $(function(){ 
        
        $feedsRow.on('click', '.button-like', function(event){ //to handle click on the Like button
            var $postId = this.dataset.id;
            var params = {
                'id':$postId
            };
            //send data to the server
            $.post('/post/default/like', params, function(data){
                $numberOfLikes = data.numberOfLikes;
                $('#likes-number-'+$postId).text($numberOfLikes);
                $('#like'+$postId).hide();
                $('#unlike'+$postId).show();
            });
            
            event.stopImmediatePropagation();
            return false;
        });
    });
    $(function(){

       $feedsRow.on('click', '.button-unlike', function(event){ //to handle click on the Unike button
            var $postId = this.dataset.id;
            var params = {
                'id':$postId
            };
            
            $.post('/post/default/unlike', params, function(data){
                $numberOfLikes = data.numberOfLikes;
                $('#likes-number-'+$postId).text($numberOfLikes);
                $('#unlike'+$postId).hide();
                $('#like'+$postId).show();
            });
             
            event.stopImmediatePropagation();
            return false;
        });

        
    });
})(jQuery);

