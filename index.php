<?php include_once "includes/db.php"; ?>
<?php include_once "includes/functions.php"; ?>
<?php include "includes/header.php"; ?>
<br>
<div class="row">
    <div id="notification-bar" class="bg-success col-xs-12"></div><!-- ************ User Notifications ************** -->
</div>
<div class="row">
    <div id="form-container">
        <div class="row" id="login-form-row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6" id='login-form-col'>
                <!-- br -->
                <?php include "includes/login_form.php"; ?><!-- initial login form -->
            </div><!-- login-form-col -->
            <div class="col-xs-3"></div>
        </div> <!-- login-form-row -->
        <div class="row" id="submission-form-row"></div> <!-- submission-form-row -->
    </div><!-- form-container -->
</div><!-- row -->

<script>
    <?php include_once "./includes/js/index.js"; ?>
</script>

<?php include "includes/footer.php"; ?>
