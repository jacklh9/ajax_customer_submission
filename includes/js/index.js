$(document).ready(function(){

    <?php include "includes/js/functions.js"; ?>

    /* ************* LOGIN FORM ******************* */

    $('#form-container').hide();
    $('#notification-bar').hide();
    $('#login-user-form').show();
    $('#upload-user-form').hide();
    $('#form-container').show();
    show_registered_users();

    $('#login-user-form').submit(function(evt){
        evt.preventDefault();
    
        var url = $(this).attr('action');
        var data = $(this).serialize();

        $.post(url, data, function(response){
            $('#submission-form-row').html(response);
            $('#login-user-form').hide();
            $('#notification-bar').hide();
            $('#registered-users-list').hide();
        });

    });

    $('#reset-login-form').on('click', function(){
        // $('#login-user-form')[0].reset();
        resetLogin();
    });

});


























