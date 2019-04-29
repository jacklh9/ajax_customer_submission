<form action="upload.php" id="upload-user-form" method="post">

    <p>Customer# $cust_id</p>

    <div class="row" id="personal-info-row">
        <div class="col-xs-3"></div>
        <div id="personal-info" class="col-xs-6">
            <div class="form-group">
                <input type="text" class="form-control" name="email" value="<?php echo $email; ?>" placeholder="Email">
                <input type="text" class="form-control" name="first" value="<?php echo $first; ?>" placeholder="First Name">
                <input type="text" class="form-control" name="last" value="<?php echo $last; ?>" placeholder="Last Name">
                <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>" placeholder="Primary Phone">
            </div>
        </div>
        <div class="col-xs-3"></div>
    </div>
    <div class="row" id='addresses-row'>
        <div class="col-xs-3" id="address-1">
            <input type="text" class="form-control" name="street_line1" value="<?php echo $street_line1; ?>" placeholder="Street Line 1">
            <input type="text" class="form-control" name="street_line2" value="<?php echo $street_line2; ?>" placeholder="Street Line 2">
            <input type="text" class="form-control" name="city" value="<?php echo $city; ?>" placeholder="City">
            <select name="state">
                <option value="">State</option>
                <option value="AK">Alaska</option>
                <option value="CA">California</option>
                <option value="TX">Texas</option>
            </select>
            <input type="text" class="form-control" name="zip" value="<?php echo $city; ?>" placeholder="ZIP">
        </div>
        <div class="col-xs-3" id="address-2">
        address 2
        </div>
        <div class="col-xs-3" id="address-3">
        address 3
        </div>
    </div>

    <div class="form-group">
        <input type="button" class="btn button btn-cancel" value="Cancel">
    </div>


    <div class="form-group">
        <input type="submit" class="btn btn-primary button" name="upload" value="Submit">
    </div>

</form>
