//handles comment adding via AJAX
(function($, undefined){
    //$ == jQuery
    $(function(){
        $('#submit-button').click(function(){
            $.ajax({
                url:     '/post/default/comment', 
                type:     "POST", 
                dataType: "html", 
                data: $('#ajax-form').serialize(),  
                success: function(response) { 
                    var result = JSON.parse(response);

                    var $commentSection = $('div#comment-section');
                    var $styleOfDeleteButton = $commentSection.attr('data-style-delete');
                    $commentSection.empty();
                   
                    var $comments = result.comments;
                    
                    for(var index in $comments){
                       $commentSection.append(
                            '<hr><pre id="comment' + $comments[index].id + '">'
                                +'<img src="' + $comments[index].authorpicture + '" alt="user picture" style="width:40px; height:40px; border-radius: 50%">'
                                +'<b>  '+$comments[index].authorname+' at '+$comments[index].updated_at+'</b><br><br>'
                                +$comments[index].text+
                                '<br><a class="comment-delete-btn" data-post-id="'+$comments[index].post_id+'" data-comment-id="'+ $comments[index].id+'" style="'+$styleOfDeleteButton+'">'+
                                    '<button class="btn btn-default">Delete</button>'+
                                '</a>'+
                            '</pre>'
                        );
                    }
                    
                    console.log($comments);
                    $('#ajax-form')[0].reset();
                }
            });
        });
    });
    $(function(){
        //to delete comment
        $('#comment-section').on('click','.comment-delete-btn', function(ev){
        /*use parent element with id = comment-section to work with dynamically generated childs with the .comment-delete-btn class */            
             var params = {
                'postId':this.dataset.postId,
                'commentId':this.dataset.commentId
            };
            
            $.post('/post/default/delete-comment', params, function(data){
                
                var deletedComment = $('#comment'+params.commentId);
                deletedComment.hide();
                deletedComment.next().hide();
        
            return false;
            });
        });
    });
})(jQuery);

