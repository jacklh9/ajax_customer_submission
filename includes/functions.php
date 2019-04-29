<script>

$(document).ready(function(){

    var notificationTextDurationSecs = 3;
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
            $('#form-container').html(response);
        });

    });

    /* ************* UPLOAD FORM ******************* */
    $('#btn-cancel').on('click', function(){
        console.log("inside btn-cancel");
        if(confirm("Are you sure you wish to cancel.\nALL changes made in the form WILL BE LOST!")){
            resetLogin();
        }
    });

    function notifyUser(response){
        $('#notification-bar').text(response);
        $('#notification-bar').show();
        setTimeout(function(text){
            $('#notification-bar').hide();
        }, notificationTextDurationSecs * 1000); // 1 sec = 1000 milliseconds
    }

    function resetLogin(){
        $('#upload-user-form').hide();
        $('#upload-user-form')[0].reset();
        $('#login-user-form')[0].reset();
        $('#login-user-form').show();
    }

});

</script>