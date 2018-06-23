//handles post report procedure via AJAX
(function($, undefined){
    var $buttonReport = $('#btn-report-post');
    var $iconPreloader = $('#icon-preloader');
    var $language = $('#language');
    
    $(function(){
        $buttonReport.click(function(event){
            //to handle click on the report post button
            $iconPreloader.show();
            var params = {
                'id':$buttonReport.attr('data-id')
            };
            
            ////send data to the server
            $.post('/post/default/report', params, function(data){
                $buttonReport.hide();
                $iconPreloader.hide();
                if ($language.attr('data-lang') == 'ru-RU') {
                    var $reportedText = 'Вы уже пожаловались на этот пост...';
                } else {
                    $reportedText = 'Post has been already reported...';
                }
                $('#reported-ajax').text($reportedText);
            });
            
            event.stopImmediatePropagation();
            return false;
        });
    });
})(jQuery);


