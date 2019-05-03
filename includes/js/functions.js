/* Functions shared across pages */

function show_registered_users(){
    $.get('includes/show_registered_users.php', function(data){
        $('#registered-users-list').html(data);
        $('#registered-users-list').show();
    });
}

function notifyUser(response){
    var notificationTextDurationSecs = 10;

    $('#notification-bar').text(response);
    $('#notification-bar').show();
    setTimeout(function(text){
        $('#notification-bar').hide();
    }, notificationTextDurationSecs * 1000); // 1 sec = 1000 milliseconds
}