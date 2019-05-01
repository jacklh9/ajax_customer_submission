<?php include_once "db.php"; ?>
<?php include "functions.php"; ?>

    <h4>Registered User Emails</h4>
    (TESTING PURPOSES ONLY)
    <ul>

<?php
    get_registered_users();
?>

    </ul>

<script>

$(document).ready(function(){
    $('.email').on('click',function(){
        $('#email').val($(this).text());
    });
});
</script>