<?php include_once "includes/db.php"; ?>
<?php include "includes/header.php"; ?>
<?php include "includes/functions.php"; ?>

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


<?php include "includes/footer.php"; ?>
