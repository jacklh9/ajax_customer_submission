<?php
    $first = "";
    $last = "";
?>

<form action="upload.php" id="upload-user-form" method="post">


    <div class="row" id="personal-info-row">
        <div class="col-xs-3"></div>
        <div id="personal-info" class="col-xs-6">

            <div class="form-group">
                <input type="text" class="form-control" name="email" value="<?php echo $email; ?>" placeholder="Email">
                <input type="text" class="form-control" name="first" value="<?php echo $first; ?>" placeholder="First Name">
                <input type="text" class="form-control" name="last" value="<?php echo $last; ?>" placeholder="Last Name">
            </div>

        </div>
        <div class="col-xs-3"></div>
    </div>
    <div class="row" id='addresses-row'>
        <div class="col-xs-3" id="address-1">
        address 1
        </div>
        <div class="col-xs-3" id="address-2">
        address 2
        </div>
        <div class="col-xs-3" id="address-3">
        address 3
        </div>
    </div>

    <div class="form-group">
        <input type="button" class="btn button" name="cancel" value="Cancel">
    </div>


    <div class="form-group">
        <input type="submit" class="btn btn-primary button" name="upload" value="Submit">
    </div>

</form>

