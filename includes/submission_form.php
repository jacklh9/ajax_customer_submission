<form action="upload.php" id="upload-user-form" method="post">

    <?php if(!empty($cust_id)){
        echo "<p>Customer# {$cust_id}</p>";
    }
    ?>

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
            <input type="text" class="form-control" name="add1_street_line1" value="<?php echo $add1_street_line1; ?>" placeholder="Street Line 1">
            <input type="text" class="form-control" name="add1_street_line2" value="<?php echo $add1_street_line2; ?>" placeholder="Street Line 2">
            <input type="text" class="form-control" name="add1_city" value="<?php echo $add1_city; ?>" placeholder="City">
            <select name="add1_state">
                <option value="">State</option>
                <option value="AK">Alaska</option>
                <option value="CA">California</option>
                <option value="TX">Texas</option>
            </select>
            <input type="text" class="form-control" name="add1_zip" value="<?php echo $add1_zip; ?>" placeholder="ZIP">
        </div>
        <div class="col-xs-3" id="address-2">
            <input type="text" class="form-control" name="add2_street_line1" value="<?php echo $add2_street_line1; ?>" placeholder="Street Line 1">
            <input type="text" class="form-control" name="add2_street_line2" value="<?php echo $add2_street_line2; ?>" placeholder="Street Line 2">
            <input type="text" class="form-control" name="add2_city" value="<?php echo $add2_city; ?>" placeholder="City">
            <select name="add2_state">
                <option value="">State</option>
                <option value="AK">Alaska</option>
                <option value="CA">California</option>
                <option value="TX">Texas</option>
            </select>
            <input type="text" class="form-control" name="add2_zip" value="<?php echo $add2_zip; ?>" placeholder="ZIP">
        </div>
        <div class="col-xs-3" id="address-3">
            <input type="text" class="form-control" name="add3_street_line1" value="<?php echo $add3_street_line1; ?>" placeholder="Street Line 1">
            <input type="text" class="form-control" name="add3_street_line2" value="<?php echo $add3_street_line2; ?>" placeholder="Street Line 2">
            <input type="text" class="form-control" name="add3_city" value="<?php echo $add3_city; ?>" placeholder="City">
            <select name="add3_state">
                <option value="">State</option>
                <option value="AK">Alaska</option>
                <option value="CA">California</option>
                <option value="TX">Texas</option>
            </select>
            <input type="text" class="form-control" name="add3_zip" value="<?php echo $add3_zip; ?>" placeholder="ZIP">
        </div>
    </div>

    <div class="form-group">
        <input type="button" class="btn button btn-cancel" value="Cancel">
    </div>


    <div class="form-group">
        <input type="submit" class="btn btn-primary button" name="upload" value="Submit">
    </div>

</form>
