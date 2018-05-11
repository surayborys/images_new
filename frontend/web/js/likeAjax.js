//handles like mechanism via AJAX

(function($, undefined){
    //$ == jQuery
     var $buttonLike = $('#button-like');
     var $buttonUnlike = $('#button-unlike');
     
    $(function(){ //to handle click on the Like button
        
        $buttonLike.click(function(event){
            var params = {
                'id':$buttonLike.attr('data-id')
            };
            //send data to the server
            $.post('/post/default/like', params, function(data){
                $numberOfLikes = data.numberOfLikes;
                $('#likes-number').text('('+$numberOfLikes+')');
                $buttonUnlike.show();
                $buttonLike.hide();
            });
            
            event.stopImmediatePropagation();
            return false;
        });
    });
    $(function(){

        $buttonUnlike.click(function(event){ //to handle click on the Unike button
            var params = {
                'id':$buttonUnlike.attr('data-id')
            };
            
            $.post('/post/default/unlike', params, function(data){
                $numberOfLikes = data.numberOfLikes;
                $('#likes-number').text('('+$numberOfLikes+')');
                $buttonUnlike.hide();
                $buttonLike.show();
            });
             
            event.stopImmediatePropagation();
            return false;
        });

        
    });
})(jQuery);

