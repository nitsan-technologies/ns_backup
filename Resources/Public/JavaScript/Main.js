define([
    'jquery',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/NsBackup/Main',
    'TYPO3/CMS/NsBackup/Datatables'
], function ($, Model) {

    if ($("#siteurl").val() && $("#siteurl").val()!=''){
        var mysiteUrl=$("#siteurl").val();
        var currentUrl = window.location.origin;
        if(mysiteUrl.endsWith('/')){
            currentUrl = currentUrl+'/';
        }
        if (mysiteUrl.localeCompare(currentUrl)){
            document.getElementById('yoursiteUrlMsg').classList.remove('d-none')
        }else{
            document.getElementById('yoursiteUrlMsg').classList.add('d-none')
        }
    }
    // Dashboard Start Manual Backup
    $("#backupnow-form").submit(function (e) {
        if (!$('#backupName').val()) {
            $(".backupName-error").show();
            return false;
        }
        $(".backupName-error").hide();
        $(".backup-Loading").show();
        $("body").css('opacity', '0.5').css('pointer-events', 'none');
        //disable the submit button
        setTimeout(function() {
            $(".btn-start-backup").attr("disabled", true);
        },500);

    });

    $('.ns-backup-datatable').DataTable({
        "language": {
            "lengthMenu": "Display _MENU_ records per page",
            "zeroRecords": "Nothing found - sorry",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)"
        },
        language: {
            paginate: {
                previous: '<<',
                next:     '>>'
            }
        }
    });

    $('.ns-backup-table-wrap .dataTables_length select,\
    .ns-backup-table-wrap .dataTables_filter input').addClass('form-control');
    $('.btn-global-submit').on('click', function () {
        isError = 0;
        if (!$('#emails').val()) {
            $(".email-error").show();
            isError = 1;
        } else {
            if (!validateEmail($('#emails').val())) {
                $(".email-error").show();
                isError = 1;
            } else {
                $(".email-error").hide();
            }
        }

        if (!$('#emailSubject').val()) {
            $(".emailSubject-error").show();
            isError = 1;
        } else {
            $(".emailSubject-error").hide()
        }

        if (!$('#cleanupQuantity').val()) {
            $(".cleanupQuantity-error").show();
            isError = 1;
        } else {
            if ($('#cleanupQuantity').val()<1 || $('#cleanupQuantity').val()>500){
                $(".cleanupQuantity-error").show();
                isError = 1;
            }else{
                $(".cleanupQuantity-error").hide();

            }
        }

        if (!$('#php').val()) {
            $(".php-error").show();
            isError = 1;
        } else {
            $(".php-error").hide();
        }

        if (!$('#root').val()) {
            $(".root-error").show();
            isError = 1;
        } else {
            $(".root-error").hide();
        }

        if (!$('#siteurl').val()) {
            $(".siteurl-error").show();
            isError = 1;
        } else {
            $(".siteurl-error").hide();
        }

        if(isError == 1) {
            return false;
        }
        else {
            return true;
        }
    });

    // Remove Backup Data
    $('.delete-backup').on('click', function () {
        var title = $(this).data('title');
        var id = $(this).data('id');
        var msg = $(this).data('msg');
        $("#nsBackupDeletebackupModal .backup-title").html(title);
        $("#nsBackupDeletebackupModal .delete-msg").html(msg);
        $("#nsBackupDeletebackupModal .delete-backup-id").val(id);
        $("#nsBackupDeletebackupModal .deletetype").val('single');
        $("#nsBackupDeletebackupModal .delete-backup-backup-del").removeAttr("disabled");
    });

    $('.paginate_button').on('click', function () {
        console.log("hello")
        var title = $('.delete-backup').data('title');
        var id = $('.delete-backup').data('id');
        var msg = $('.delete-backup').data('msg');
        $("#nsBackupDeletebackupModal .backup-title").html(title);
        $("#nsBackupDeletebackupModal .delete-msg").html(msg);
        $("#nsBackupDeletebackupModal .delete-backup-id").val(id);
        $("#nsBackupDeletebackupModal .deletetype").val('single');
        $("#nsBackupDeletebackupModal .delete-backup-backup-del").removeAttr("disabled");
        $("#nsBackupDeletescheduleModal .delete-schedule-backup-del").removeAttr("disabled");
    });

    $(document).on('click', '.delete-backup-backup-del',function (e) {
        var deleteUrl = $("#deletebackupbackup").attr('action');
        var id = $('.delete-backup-id').val();

        $.ajax({
            url: deleteUrl,
            data:{uid:id},
            success:function () {
                window.location = window.location.href;
            }
        });
        e.preventDefault();
    });

    // Display Log
    $(document).on('click', '.btn-log-show',function (e) {
        var id = $(this).data('id');
        $('.log-container').html('');
        $('.log-container').html($('#logDiv_'+id).html());
    });

    // Code Highlight
    hljs.initHighlightingOnLoad();
});

// Validate Email field
function validateEmail(emails) {
    if (emails.endsWith(",") || emails.startsWith(",")){
        return false;
    }
    const emailList = emails.split(',');
    // Validate each email in the list
    for (let email of emailList) {
        if (!singleEmailvalidate(email.trim())) {
            return false;
        }
    }
    return true;

}
function singleEmailvalidate($email){

    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,})?$/;
    return emailReg.test($email);
}

