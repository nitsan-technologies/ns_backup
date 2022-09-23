define([
    'jquery',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/NsBackup/Main',
    'TYPO3/CMS/NsBackup/Datatables',
    'TYPO3/CMS/Backend/jquery.clearable'
], function ($, Model) {

    // Dashboard Start Manual Backup
    $("#backupnow-form").submit(function (e) {
        if (!$('#backupName').val()) {
            $(".backupName-error").show();
            return false;
        }
        $(".backupName-error").hide();
        $(".backup-Loading").show();

        //disable the submit button
        setTimeout(function() {
            $(".btn-start-backup").attr("disabled", true);
        },500);

        /* var $progressBar = $(this).parent().find('.progress-bar');
        for (i = 0; i <= 100; i++) {
            $($progressBar).css('width', i + '%');
            $($progressBar).text(i + '%')
        }
        $(this).parent().find('.btn-backupnow').removeClass('disabled'); */
    });

    $('.ns-backup-datatable').DataTable({
        "language": {
            "lengthMenu": "Display _MENU_ records per page",
            "zeroRecords": "Nothing found - sorry",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)"
        },
        //"order": [[3, "desc"]],
        language: {
            paginate: {
                previous: '<<',
                next:     '>>'
            }
        }
    });

    $('.ns-backup-table-wrap .dataTables_length select,\
    .ns-backup-table-wrap .dataTables_filter input').addClass('form-control');

    $('.check').on('click', function () {
        if($(this).prop("checked") == true){
            $(this).val(1);
        }
        else {
            $(this).val(0);
        }
    });

    $('.btn-global-submit').on('click', function () {
        isError = 0;
        /* if($('.no-data-server-error').length > 0) {
            return false;
        } */
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
            $(".cleanupQuantity-error").hide();
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

    // Server/Cloud start
    $('.server-cloud-option').on('change', function () {
        serverOptions('#configureNewServerModal');
    });

    $('.servernewmodel').on('click', function () {
        setTimeout(function (e) {
            serverOptions('#configureNewServerModal');
        }, 190);
    });

    $('.servernewmodel').on('click', function () {
        $(".error").hide();
    });

    $('.servercloud-submit').on('click', function () {
        var flag = 1;
        var flag = validateServerCloud('#configureNewServerModal');
        if (flag == 1) {
            return true;
        }
        return false;
    });

    $('.delete-servercloud').on('click', function () {
        var title = $(this).data('title');
        var id = $(this).data('id');
        var msg = $(this).data('msg');
        $("#nsBackupDeleteservercloudModal .server-title").html(title);
        $("#nsBackupDeleteservercloudModal .delete-msg").html(msg);
        $("#nsBackupDeleteservercloudModal .delete-server-id").val(id);
        $("#nsBackupDeleteservercloudModal .deletetype").val('single');
        $("#nsBackupDeleteservercloudModal .delete-server-cloud-del").removeAttr("disabled");
    });

    $(".ns-backup-select-all #check-All").change(function() {
        if (this.checked) {
            $(".checkSingle").each(function() {
                this.checked=true;
            });
        } else {
            $(".checkSingle").each(function() {
                this.checked=false;
            });
        }
    });

    $('.delete-selected').on('click', function () {
        var title = $(this).data('title');
        var msg = $(this).data('msg');
        $("#nsBackupDeleteservercloudModal .server-title").html(title);
        $("#nsBackupDeleteservercloudModal .delete-msg").html(msg);
        $("#nsBackupDeleteservercloudModal .deletetype").val('multiple');
        $("#nsBackupDeleteservercloudModal .delete-server-cloud-del").removeAttr("disabled");
    });

    $(document).on('click', '.delete-server-cloud-del',function (e) {
        var id = [];
        var deleteUrl = $("#deleteservercloud").attr('action');
        var deletetype = $(".deletetype").val();
        if(deletetype=='multiple') {
            $('.checkSingle:checkbox:checked').each(function(i){
                id[i] = $(this).val();
            });
        } else {
            id[0] = $('.delete-server-id').val();
        }
        $.ajax({
            url: deleteUrl,
            data:{uids:id},
            success:function () {
                window.location.reload();
            }
        });
        e.preventDefault();
    });

    $(document).on('click', '.edit-server-cloud',function (e) {
        var id = $(this).data('id');
        var editUrl = $(".getserverdata").attr('href');
        $.ajax({
            url: editUrl,
            data:{uid:id},
            success:function (data) {
                $(".edit-servercloud-data").html(data);
                $('.server-cloud-option').on('change', function () {
                    serverOptions('#configureEditServerModal');
                });
                setTimeout(function (e) {
                    serverOptions('#configureEditServerModal');
                }, 190);
                $('.servercloud-submit').on('click', function () {
                    var flag = 1;
                    var flag = validateServerCloud('#configureEditServerModal');
                    if (flag == 1) {
                        return true;
                    }
                    return false;
                });
            }
        });
        e.preventDefault();
    });
    // Server/Cloud End

    // Schedule Backup Start
    singlecheckvalidation();

    $('.schedule-backup-submit').on('click', function () {
        var flag = 1;
        var flag = validateSchedulebackup('#createNewscheduleBackup');
        if (flag == 1) {
            return true;
        }
        return false;
    });

    $('.delete-schedule').on('click', function () {
        var title = $(this).data('title');
        var id = $(this).data('id');
        var msg = $(this).data('msg');
        $("#nsBackupDeletescheduleModal .schedule-title").html(title);
        $("#nsBackupDeletescheduleModal .delete-msg").html(msg);
        $("#nsBackupDeletescheduleModal .delete-schedule-id").val(id);
        $("#nsBackupDeletescheduleModal .deletetype").val('single');
        $("#nsBackupDeletescheduleModal .delete-schedule-backup-del").removeAttr("disabled");
    });

    $('.delete-selected-schedulebackup').on('click', function () {
        var title = $(this).data('title');
        var msg = $(this).data('msg');
        $("#nsBackupDeletescheduleModal .schedule-title").html(title);
        $("#nsBackupDeletescheduleModal .delete-msg").html(msg);
        $("#nsBackupDeletescheduleModal .deletetype").val('multiple');
        $("#nsBackupDeletescheduleModal .delete-schedule-backup-del").removeAttr("disabled");
    });

    $(document).on('click', '.delete-schedule-backup-del',function (e) {
        var id = [];
        var deleteUrl = $("#deleteschedulebackup").attr('action');
        var deletetype = $("#nsBackupDeletescheduleModal .deletetype").val();
        if(deletetype=='multiple') {
            $('.checkSingle:checkbox:checked').each(function(i){
                id[i] = $(this).val();
            });
        } else {
            id[0] = $('.delete-schedule-id').val();
        }
        $.ajax({
            url: deleteUrl,
            data:{uids:id},
            success:function () {
                window.location.reload();
            }
        });
        e.preventDefault();
    });

    $(document).on('click', '.edit-schedule-backup',function (e) {
        var id = $(this).data('id');
        var editUrl = $(".getscheduledata").attr('href');
        $.ajax({
            url: editUrl,
            data:{uid:id},
            success:function (data) {
                $(".edit-schedule-backup-model").html(data);
                singlecheckvalidation();
                $('.schedule-backup-submit').on('click', function () {
                    var flag = 1;
                    var flag = validateSchedulebackup('#createEditcheduleBackup');
                    if (flag == 1) {
                        return true;
                    }
                    return false;
                });
            }
        });
        e.preventDefault();
    });
    // Schedule Backup End

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
    $(document).on('click', '.delete-backup-backup-del',function (e) {
        var id = [];
        var deleteUrl = $("#deletebackupbackup").attr('action');
        var deletetype = $("#nsBackupDeletebackupModal .deletetype").val();
        if(deletetype=='multiple') {
            $('.checkSingle:checkbox:checked').each(function(i){
                id[i] = $(this).val();
            });
        } else {
            id[0] = $('.delete-backup-id').val();
        }
        $.ajax({
            url: deleteUrl,
            data:{uids:id},
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

function singlecheckvalidation() {
    $(".schedulebackup input:checkbox").on('click', function() {
        // in the handler, 'this' refers to the box clicked on
        var $box = $(this);
        if ($box.is(":checked")) {
            // the name of the box is retrieved using the .attr() method
            // as it is assumed and expected to be immutable
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            // the checked state of the group/box on the other hand will change
            // and the current value is retrieved using .prop() method
            $(group).prop("checked", false);
            $box.prop("checked", true);
        } else {
            $box.prop("checked", false);
        }
    });
}

function serverOptions(id) {
    var serverOption = $(id+ " .server-cloud-option").val();
    $(id + " .server-cloud-option").parents('.configure-new-server-form').find('.server-cloud-apikey-box-wrap').show();
    $(id + ' .configure-new-server-form .server-cloud-apikey-box').removeClass('active');
    $(id + ' .server-cloud-apikey-box-wrapper').css('min-height', $(id + ' .configure-new-server-form .server-cloud-apikey-box[data-nsbackup-server="' + serverOption + '"]').outerHeight());
    $(id + ' .configure-new-server-form .server-cloud-apikey-box[data-nsbackup-server="' + serverOption + '"]').addClass('active');
}

function validateSchedulebackup(id) {
    var flag = 1;
    if (!$(id +' .title').val()) {
        $(id + " .schedulebackup-title-error").show();
        var flag = 0;
    } else {
        $(id + " .schedulebackup-title-error").hide();
        var flag = 1;
    }

    if (!$(id +' .selectScheduleType').val()) {
        $(id + " .schedulebackup-selectScheduleType-error").show();
        var flag = 0;
    } else {
        $(id + " .schedulebackup-selectScheduleType-error").hide();
        var flag = 1;
    }

    if ($(".backup-type-check:checked").length < 1) {
        $(id + " .schedulebackup-backuptype-error").show();
        var flag = 0;
    }
    else {
        $(id + " .schedulebackup-backuptype-error").hide();
        var flag = 1;
    }

    if (!$(id +' .startrun').val()) {
        $(id + " .schedulebackup-startrun-error").show();
        var flag = 0;
    } else {
        $(id + " .schedulebackup-startrun-error").hide();
        var flag = 1;
    }

    if (!$(id +' .endrun').val()) {
        $(id + " .schedulebackup-endrun-error").show();
        var flag = 0;
    } else {
        $(id + " .schedulebackup-endrun-error").hide();
        var flag = 1;
    }

    if (flag == 1) {
        return 1;
    }
    return 0;
}

function validateServerCloud(id) {
    var flag = 1;
    if (!$(id +' .title').val()) {
        $(id + " .servercloud-title-error").show();
        var flag = 0;
        return false;
    } else {
        $(id + " .servercloud-title-error").hide();
    }
    if ($(id + ' .server-cloud-option').val()=='amazons3') {
        var flag = validateAmazons3(id);
    }
    if ($(id + ' .server-cloud-option').val()=='googledrive') {
        var flag = validateGoogledrive(id);
    }
    if ($(id + ' .server-cloud-option').val()=='sftp') {
        var flag = validateSftp(id);
    }
    if ($(id + ' .server-cloud-option').val()=='dropbox') {
        var flag = validateDropbox(id);
    }
    if ($(id + ' .server-cloud-option').val()=='rsync') {
        var flag = validateRsync(id);
    }
    if (flag == 1) {
        return 1;
    }
    return 0;
}

// Validate Email field
function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test($email);
}

// Validate Amazones3
function validateAmazons3(id) {
    var flag = 1;
    if (!$(id + ' .amazons3-key').val()) {
        $(id + " .amazons3-key-error").show();
        var flag = 0;
    } else {
        $(id + " .amazons3-key-error").hide();
    }

    if (!$(id + ' .amazons3-secret').val()) {
        $(id + " .amazons3-secret-error").show();
        var flag = 0;
    } else {
        $(id + " .amazons3-secret-error").hide();
    }

    if (!$(id + ' .amazons3-bucket').val()) {
        $(id + " .amazons3-bucket-error").show();
        var flag = 0;
    } else {
        $(id + " .amazons3-bucket-error").hide();
    }

    if (!$(id + ' .amazons3-region').val()) {
        $(id + " .amazons3-region-error").show();
        var flag = 0;
    } else {
        $(id + " .amazons3-region-error").hide();
    }

    if (!$(id + ' .amazons3-path').val()) {
        $(id + " .amazons3-path-error").show();
        var flag = 0;
    } else {
        $(id + " .amazons3-path-error").hide();
    }
    return flag;
}

// validate googledrive
function validateGoogledrive(id) {
    var flag = 1;
    if (!$(id + ' .googledrive-secret').val()) {
        $(id + " .googledrive-secret-error").show();
        var flag = 0;
    } else {
        $(id + " .googledrive-secret-error").hide();
    }

    if (!$(id + ' .googledrive-access').val()) {
        $(id + " .googledrive-access-error").show();
        var flag = 0;
    } else {
        $(id + " .googledrive-access-error").hide();
    }

    if (!$(id + ' .googledrive-parentId').val()) {
        $(id + " .googledrive-parentId-error").show();
        var flag = 0;
    } else {
        $(id + " .googledrive-parentId-error").hide();
    }
    return flag;
}

// validate sftp
function validateSftp(id) {
    var flag = 1;
    if (!$(id + ' .sftp-host').val()) {
        $(id + " .sftp-host-error").show();
        var flag = 0;
    } else {
        $(id + " .sftp-host-error").hide();
    }

    if (!$(id + ' .sftp-port').val()) {
        $(id + " .sftp-port-error").show();
        var flag = 0;
    } else {
        $(id + " .sftp-port-error").hide();
    }

    if (!$(id + ' .sftp-user').val()) {
        $(id + " .sftp-user-error").show();
        var flag = 0;
    } else {
        $(id + " .sftp-user-error").hide();
    }

    if (!$(id + ' .sftp-password').val()) {
        $(id + " .sftp-password-error").show();
        var flag = 0;
    } else {
        $(id + " .sftp-password-error").hide();
    }

    if (!$(id + ' .sftp-path').val()) {
        $(id + " .sftp-path-error").show();
        var flag = 0;
    } else {
        $(id + " .sftp-path-error").hide();
    }

    /* if (!$(id + ' .sftp-passive').val()) {
        $(id + " .sftp-passive-error").show();
        var flag = 0;
    } else {
        $(id + " .sftp-passive-error").hide();
    } */
    return flag;
}

// validate dropbox
function validateDropbox(id) {
    var flag = 1;
    if (!$(id + ' .dropbox-token').val()) {
        $(id + " .dropbox-token-error").show();
        var flag = 0;
    } else {
        $(id + " .dropbox-token-error").hide();
    }

    if (!$(id + ' .dropbox-path').val()) {
        $(id + " .dropbox-path-error").show();
        var flag = 0;
    } else {
        $(id + " .dropbox-path-error").hide();
    }
    return flag;
}

// validate sftp
function validateRsync(id) {
    var flag = 1;
    if (!$(id + ' .rsync-host').val()) {
        $(id + " .rsync-host-error").show();
        var flag = 0;
    } else {
        $(id + " .rsync-host-error").hide();
    }

    if (!$(id + ' .rsync-user').val()) {
        $(id + " .rsync-user-error").show();
        var flag = 0;
    } else {
        $(id + " .rsync-user-error").hide();
    }

    if (!$(id + ' .rsync-path').val()) {
        $(id + " .rsync-path-error").show();
        var flag = 0;
    } else {
        $(id + " .rsync-path-error").hide();
    }

    return flag;
}
