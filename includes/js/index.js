$(document).ready(function(){

    <?php include_once "includes/js/functions.js"; ?>

    /* 

        ************* LOGIN FORM ******************* 

     */

    reset_page();

    function reset_page(){
        $('#form-container').hide();
        $('#notification-bar').hide();
        $('#login-user-form')[0].reset();
        $('#login-user-form').show();
        $('#upload-user-form').hide();
        $('#form-container').show();
        show_registered_users();
    }

    // Login | Register button on login page
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

    // Reset button on login page
    $('#reset-email-login-input').on('click', function(){
        reset_page();
    });

});


























