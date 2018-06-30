//to call click on input name=PostForm[picture] after the click on element with id = file-input-div happened
(function($, undefined){
        $(function(){
            var $chooseFileDiv = $('#file-input-div');
            var $fileInputHidden = $('input[name="PostForm[picture]"]');
                    
            $chooseFileDiv.click(function(){
                $fileInputHidden.click();
            });
        });
    })(jQuery);

