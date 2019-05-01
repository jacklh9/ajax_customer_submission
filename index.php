<?php include_once "includes/db.php"; ?>
<?php include "includes/header.php"; ?>

<div class="row">
    <div id="notification-bar" class="bg-success"></div>
</div>
<div class="row">
    <div id="form-container">
        <div class="row" id="login-form-row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6">
                <br>
                <?php include "includes/login_form.php"; ?>
            </div>
            <div class="col-xs-3"></div>
        </div> <!-- login-form-row -->
        <div class="row" id="submission-form-row">
        </div> <!-- submission-form-row -->
    </div>
</div>

<script>

$(document).ready(function(){

    var notificationTextDurationSecs = 3;

    $.get('includes/show_registered_users.php', function(data){
        $('#registered_users').html(data);
    });

    $('#form-container').hide();
    $('#notification-bar').hide();
    $('#login-user-form').show();
    $('#upload-user-form').hide();
    $('#form-container').show();

    $('#login-user-form').submit(function(evt){
        evt.preventDefault();
    
        var url = $(this).attr('action');
        var data = $(this).serialize();

        $.post(url, data, function(response){
            $('#submission-form-row').html(response);
            $('#login-user-form').hide();
        });

    });


    /* ************* UPLOAD FORM ******************* */
    

    function notifyUser(response){
        $('#notification-bar').text(response);
        $('#notification-bar').show();
        setTimeout(function(text){
            $('#notification-bar').hide();
        }, notificationTextDurationSecs * 1000); // 1 sec = 1000 milliseconds
    }

    

});

</script>

<?php include "includes/footer.php"; ?>
