$(document).ready(function(){
    $('.btn-start-backup').on('click', function(){
        var $progressBar = $(this).parent().find('.progress-bar');
        for(i=0;i<=100;i++){
            $($progressBar).css('width', i + '%');
            $($progressBar).text(i + '%')
        }
        $(this).parent().find('.btn-backupnow').removeClass('disabled');
    });
}); 