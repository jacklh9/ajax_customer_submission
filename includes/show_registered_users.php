<?php include_once "db.php"; ?>
<?php include "functions.php"; ?>

    <h4>Registered User Emails</h4>
    <p><small class="form-text">(FOR TESTING PURPOSES ONLY)</small></p>
    <p>Click to pre-populate login</P>

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