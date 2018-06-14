//handles post report procedure via AJAX
(function($, undefined){
    var $buttonReport = $('#btn-report-post');
    var $iconPreloader = $('#icon-preloader');
    
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
                $('#reported-ajax').text('Post has been already reported...');
            });
            
            event.stopImmediatePropagation();
            return false;
        });
    });
})(jQuery);


