<?php include_once "includes/db.php"; ?>
<?php include "includes/functions.php"; ?>
<?php include "includes/header.php"; ?>

<div class="row">
    <div id="notification-bar" class="bg-success"></div>
</div>
<div class="row">
    <div id="form-container">
        <div class="row" id="login-form-row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6" id='login-form-col'>
                <br>
                <?php include "includes/login_form.php"; ?>
            </div><!-- login-form-col -->
            <div class="col-xs-3"></div>
        </div> <!-- login-form-row -->
        <div class="row" id="submission-form-row"></div> <!-- submission-form-row -->
    </div><!-- form-container -->
</div><!-- row -->

<script>

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


});

</script>

<?php include "includes/footer.php"; ?>
