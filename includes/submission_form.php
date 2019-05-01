<?php include_once "includes/functions.php"; ?>
<form action="upload.php" id="upload-user-form" method="post">

    <?php if(!empty($cust_id)){
        echo "<p>Customer# {$cust_id}</p>";

        // This is an existing customer
        $submit_type = 'Save';
        $profile_path = PROFILE_PATH . "/" . get_cust_profile($cust_id);
    } else {
        // This is a new customer
        $submit_type = 'Register';
        $profile_path = PROFILE_PATH . "/" . DEFAULT_IMAGE;
        $cust_id = -1;
    }
    ?>

    <div class="row" id="personal-info-row">
        <div class="col-xs-2"></div>
        <div id="personal-info" class="col-xs-6">
            <div class="form-group">
                <input type="hidden" class="form-control" id="cust_id" name='cust_id' value="<?php echo $cust_id; ?>">
                <input type="text" class="form-control" name="email" value="<?php echo $email; ?>" placeholder="Email">
                <input type="text" class="form-control" name="first" value="<?php echo $first; ?>" placeholder="First Name">
                <input type="text" class="form-control" name="last" value="<?php echo $last; ?>" placeholder="Last Name">
                <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>" placeholder="Primary Phone">
            </div>
        </div>
        <div class="col-xs-2" id="profile-info">
            <img src='<?php echo "{$profile_path}"; ?>' width="200">
            <div class="form-group">
                <input type="file" class="form-control" id="profile_pic">
            </div>
        </div>
        <div class="col-xs-2"></div>
    </div>
    <div class="row" id='addresses-row'>
<?php
    for($i = 0; $i < MAX_ADDRESSES; $i++){
?>
        <div class="col-xs-4" id="address-" . <?php echo $i; ?>>
            <h3>Address <?php echo $i+1; ?>:</h3>
            <input type="hidden" class="form-control" name='<?php echo "add{$i}_id"; ?>' value="<?php echo $add[$i]['id']; ?>">
            <input type="text" class="form-control" name='<?php echo "add{$i}_street_line1"; ?>' value="<?php echo $add[$i]['street_line1']; ?>" placeholder="Street Line 1">
            <input type="text" class="form-control" name='<?php echo "add{$i}_street_line2"; ?>' value="<?php echo $add[$i]['street_line2']; ?>" placeholder="Street Line 2">
            <input type="text" class="form-control" name='<?php echo "add{$i}_city"; ?>' value="<?php echo $add[$i]['city']; ?>" placeholder="City">
            <select name='<?php echo "add{$i}_state"; ?>'>
                <option value="none">State</option>
<?php
            foreach(STATES as $state){
                if ($state == $add[$i]['state']){
                    echo "<option value='$state' selected>$state</option>";
                } else {
                    echo "<option value='$state'>$state</option>";
                }
            }
?>
            </select>
            <input type="text" class="form-control" name='<?php echo "add{$i}_zip"; ?>' value="<?php echo $add[$i]['zip']; ?>" placeholder="ZIP">
        </div>
<?php
    } // end for-loop
?>
        <!-- div class="col-xs-1"></div -->
    </div> <!-- row -->
    <br>
    <div class="row" id="submission-form-buttons">
        <div class="form-group col-xs-2">
            <input type="submit" class="btn btn-primary" name="upload" value="<?php echo $submit_type; ?>">
        </div>
<?php   if($cust_id >= 0){
        // Customer exists in DB, so display delete button
?>
            <div class="form-group col-xs-2">
                <input type="button" class="btn" id="btn-delete" value="Delete">
            </div>
<?php
        }
?>
        <div class="form-group col-xs-2">
            <input type="button" class="btn" id="btn-cancel" value="Cancel">
        </div>
        <div class="col-xs-6"></div>
    </div>

</form>

<script>
    var notificationTextDurationSecs = 300;

    <?php include "includes/js/functions.js"; ?>

    function resetLogin(){
        $('#upload-user-form').hide();
        $('#upload-user-form')[0].reset();
        $('#login-user-form')[0].reset();
        $('#login-user-form').show();
        show_registered_users();
    }

    function thankYou(){
        alert("Thank you. Your information has been successfully submitted.");
    }


    // DELETE button
    $('#btn-delete').on('click', function(){
        if(confirm("Are you sure you wish to DELETE this user?\nAll changes in the form AND any data already saved in the database WILL BE LOST!\nThis data cannot be recovered once deleted.")){
            cust_id = $('#cust_id').val();
            if(cust_id >= 0){

                // Customer exists in DB, so purge from DB.
                $.post("delete.php", {cust_id: cust_id}, function(response){
                    notifyUser("Customer# " + cust_id + " deleted.");
                });
            } else {
                notifyUser("Form data not submitted.");
            }
            resetLogin();
        }
    });


    // CANCEL button
    $('#btn-cancel').on('click', function(){
        if(confirm("Are you sure you wish to cancel changes?\nOnly CHANGES made in the form WILL BE LOST!\nAny previously saved data will remain untouched.")){
            notifyUser("Form data not submitted.");
            resetLogin();
        }
    });

    // SUBMIT button
    $('#upload-user-form').submit(function(evt){
        evt.preventDefault();
    
        var url = $(this).attr('action');
        var data = $(this).serialize();

        // upload.php
        $.post(url, data, function(response){
            notifyUser(response);
            resetLogin();
            thankYou();
        }).fail(function(){
            alert("There was a problem uploading your information.\nWe are sorry for the inconvenience.");
        });

    });
</script>

