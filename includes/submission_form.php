<?php include_once "includes/functions.php"; ?>
<?php
    global $MAX_ADDRESSES;
    $states = array("AK", "CA", "TX");
?>
<form action="upload.php" id="upload-user-form" method="post">

    <?php if(!empty($cust_id)){
        echo "<p>Customer# {$cust_id}</p>";

        // This is an existing customer
        $submit_type = 'Save';
    } else {
        // This is a new customer
        $submit_type = 'Register';
    }
    ?>

    <div class="row" id="personal-info-row">
        <div class="col-xs-2"></div>
        <div id="personal-info" class="col-xs-6">
            <div class="form-group">
                <input type="text" class="form-control" name="email" value="<?php echo $email; ?>" placeholder="Email">
                <input type="text" class="form-control" name="first" value="<?php echo $first; ?>" placeholder="First Name">
                <input type="text" class="form-control" name="last" value="<?php echo $last; ?>" placeholder="Last Name">
                <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>" placeholder="Primary Phone">
            </div>
        </div>
        <div class="col-xs-2" id="profile-info">
            <img src="" width="200">
            <div class="form-group">
                <input type="file" class="form-control" id="profile_pic">
            </div>
        </div>
        <div class="col-xs-2"></div>
    </div>
    <div class="row" id='addresses-row'>
<?php
    for($i = 0; $i < $MAX_ADDRESSES; $i++){
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
            foreach($states as $state){
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
        <div class="form-group col-xs-2">
            <input type="button" class="btn" id="btn-cancel" value="Cancel">
        </div>
        <div class="col-xs-8"></div>
    </div>

</form>

<script>
    var notificationTextDurationSecs = 300;

    function notifyUser(response){
        $('#notification-bar').text(response);
        $('#notification-bar').show();
        setTimeout(function(text){
            $('#notification-bar').hide();
        }, notificationTextDurationSecs * 1000); // 1 sec = 1000 milliseconds
    }

    function resetLogin(){
        $('#upload-user-form').hide();
        $('#upload-user-form')[0].reset();
        $('#login-user-form')[0].reset();
        $('#login-user-form').show();
    }

    function thankYou(){
        alert("Thank you. Your information has been successfully submitted.");
    }

    // CANCEL button
    $('#btn-cancel').on('click', function(){
        if(confirm("Are you sure you wish to cancel.\nALL changes made in the form WILL BE LOST!")){
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

