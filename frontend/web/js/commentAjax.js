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
                    prepareCommentsOutput(response);                                  
                    $('#ajax-form')[0].reset();
                }
            });
        });
    });
    $(function(){
        //to delete comment
        $('#comment-section').on('click','.comment-delete-btn', function(){
        /*use parent element with id = comment-section to work with dynamically generated childs with the .comment-delete-btn class */            
             var params = {
                'postId':this.dataset.postId,
                'commentId':this.dataset.commentId
            };
            
            $.post('/post/default/delete-comment', params, function(){
                
                var deletedComment = $('#comment'+params.commentId);
                deletedComment.hide();
                var numOfComments = $('#number-of-comments').text();
                $('#number-of-comments').empty().append(numOfComments-1);
        
            return false;
            });
        });
    });
    $(function(){
        //to call update comment modal window with filled inputs
        $('#comment-section').on('click', '.comment-edit-btn', function(){
        /*use parent element with id = comment-section to work with dynamically generated childs with the .comment-update-btn class */     
                              
            //fill the edit form (#commentEditTextarea) with the params data
            $commentTextarea = $('#commentEditTextarea');
            $commentTextarea.html($('#comment-text-'+this.dataset.commentId)['0'].innerHTML);
            $('#comment-id-input')['0'].value = this.dataset.commentId;
                        
            $('#submit-button-edit').click(function(){
             
                $.ajax({
                    url:     '/post/default/edit-comment', 
                    type:     "POST", 
                    dataType: "html", 
                    data: $('#ajax-edit-form').serialize(),  
                    success: function(response) { 
                        prepareCommentsOutput(response);
                        $('#ajax-edit-form')[0].reset();
                    }
                });
            });
        });
    });
    
    function prepareCommentsOutput(response){
        var result = JSON.parse(response);

        var $commentSection = $('div#comment-section');
        var $styleOfDeleteButton = $commentSection.attr('data-style-delete');
        var $currentUserId = $commentSection.attr('data-crntuser-id');
        $('#comment-list-ul').empty();

        var $comments = result.comments;

        for(var index in $comments){
           var $editButton;
           
           if($comments[index].user_id == $currentUserId) {
               $editButton = '<a data-post-id="'+$comments[index].post_id+'" data-comment-id="'+$comments[index].id+'" class="comment-edit-btn">'+
                                '<button type="button" class="btn btn-default button-edit" id="button-edit" data-toggle="modal" data-target="#myModal2">'+
                                    'Edit&nbsp;&nbsp;<span class="glyphicon glyphicon-edit"></span>'+
                                '</button>'+
                              '</a>&nbsp;&nbsp;&nbsp;';
           } else {$editButton='';}
           

           $('#comment-list-ul').append(
                '<li class="comment" id="comment' + $comments[index].id + '">'
                    +'<div class="comment-user-image">'
                        +'<img src="' + $comments[index].authorpicture + '" alt="user picture" class="comment-image">'
                    +'</div>'
                    +'<div class="comment-info">'
                        +'<h4 class="author"><a href="/profile/'+$comments[index].authornickname+'">'+$comments[index].authorname+'</a> <span>'+$comments[index].updated_at+'</span></h4>'
                        +'<p id="comment-text-'+$comments[index].id +'">'+$comments[index].text+'</p>'+
                            $editButton+
                            '<a class="comment-delete-btn" data-post-id="'+$comments[index].post_id+'" data-comment-id="'+ $comments[index].id+'" style="'+$styleOfDeleteButton+'">'+
                                '<button class="btn btn-default">Delete</button>'+
                            '</a>'
                    +'</div>'        
               +'</li>'
            );
        }
               
        $('#number-of-comments').empty().append(result.numberOfComments);
    }
})(jQuery);

