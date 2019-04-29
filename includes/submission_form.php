<?php
    global $MAX_ADDRESSES;
    $states = array("AK", "CA", "TX");
?>
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
<?php
    for($i = 0; $i < $MAX_ADDRESSES; $i++){
?>
        <div class="col-xs-4" id="address-" . <?php echo $i; ?>>
            <h3>Address <?php echo $i+1; ?>:</h3>
            <input type="text" class="form-control" name="add" . <?php echo $i; ?> . "_street_line1" value="<?php echo $add[$i]['street_line1']; ?>" placeholder="Street Line 1">
            <input type="text" class="form-control" name="add" . <?php echo $i; ?> . "_street_line2" value="<?php echo $add[$i]['street_line2']; ?>" placeholder="Street Line 2">
            <input type="text" class="form-control" name="add" . <?php echo $i; ?> . "_city" value="<?php echo $add[$i]['city']; ?>" placeholder="City">
            <select name="add" . <?php echo $i; ?> . "_state">
                <option value="none">State</option>
<?php
            foreach($states as $state){
                if ($state == $add[$i]['state']){
                    echo "<option value='$state' selected>$state</option>";
                } else {
                    echo "<option value='$state'>$state</option>";
                }
            }
?>
            </select>
            <input type="text" class="form-control" name="add" . <?php echo $i; ?> . "_zip" value="<?php echo $add[$i]['zip']; ?>" placeholder="ZIP">
        </div>
<?php
    } // end for-loop
?>
        <!-- div class="col-xs-1"></div -->
    </div> <!-- row -->
    <br>
    <div class="row" id="submission-form-buttons">
        <div class="form-group col-xs-1">
            <input type="submit" class="btn btn-primary" name="upload" value="Save">
        </div>
        <div class="form-group col-xs-1">
            <input type="button" class="btn" id="btn-cancel" value="Cancel">
        </div>
        <div class="col-xs-10"></div>
    </div>

</form>
