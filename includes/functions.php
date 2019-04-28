<?php
include_once "db.php";



?>

<script>

$(document).ready(function(){

    var notificationTextDurationSecs = 3;
    $('#notification-bar').hide();

    $('#login-user-form').submit(function(evt){
        evt.preventDefault();
    
        var url = $(this).attr('action');
        var data = $(this).serialize();

        $.post(url, data, function(response){
            console.log(response);
            $('#form-container').html(response);
        });

    });

    function notifyUser(response){
        $('#notification-bar').text(response);
        $('#notification-bar').show();
        setTimeout(function(text){
            $('#notification-bar').hide();
        }, notificationTextDurationSecs * 1000); // 1 sec = 1000 milliseconds
    }

});

</script>