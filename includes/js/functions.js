/* Functions shared across pages */

/* 
    CONSTANTS
 */

NOTIFICATIONS_DURATION = 30;  // seconds before user notifications are removed from view
MAX_EMAIL_LEN = 255;

function notifyUser(response){
    var notificationTextDurationSecs = NOTIFICATIONS_DURATION;

    $('#notification-bar').text(response);
    $('#notification-bar').show();
    setTimeout(function(text){
        notifyUserHide();
    }, notificationTextDurationSecs * 1000); // 1 sec = 1000 milliseconds
}

function show_registered_users(){
    var notice = "Getting list of registered test users... Please Wait!";
    $('#registered-users-list').html(notice);
    $('#registered-users-list').show();

    $.get('includes/show_registered_users.php', function(data){
        $('#registered-users-list').html(data);
        $('#registered-users-list').show();
    })
    .fail(function(responseText) {
        var notice = "ERROR: Get request failed: " + responseText;
        $('#registered-users-list').html(notice);
        $('#registered-users-list').show();;
    });
}

function notifyUserHide(){
    $('#notification-bar').hide();
}