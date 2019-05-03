<?php include_once "includes/functions.php"; ?>
<form action="upload.php" id="upload-user-form" method="post" enctype="multipart/form-data">

    <div class="row" id="personal-info-row">
        <!-- *********** SUBMISSION CONTROLS **************  -->
        <div id="submission-controls" class="col-xs-3">
<?php 
            if(!empty($cust_id)){
                echo "<p>Customer# {$cust_id}</p>";

                // This is an existing customer
                $submit_type = 'Save All';
                $profile_path = PROFILE_PATH . "/" . get_profile_pic($cust_id);
            } else {
                // This is a new customer
                $submit_type = 'Register';
                $profile_path = PROFILE_PATH . "/" . DEFAULT_IMAGE;
                $cust_id = -1;
            }
?>          
            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="upload" value="<?php echo $submit_type; ?>"><br>
            </div>
            <div class="form-group">
                <input type="button" class="btn" id="btn-cancel" value="Cancel Changes"><br>
            </div>

<?php      if($cust_id >= 0){
                // Customer exists in DB, so display delete button
?>
                <div class="form-group">
                    <input type="button" class="btn" id="btn-delete-user" value="Delete User"><br>
                </div>
<?php
            }
?>
       </div><!-- submission-controls -->
       <!-- *********** END: SUBMISSION CONTROLS **************  -->
       <!-- ************* BEGIN: PERSONAL INFO ********************* -->
        <div id="personal-info" class="col-xs-6">
            <div class="form-group">
                <input type="hidden" class="form-control" id="cust_id" name='cust_id' value="<?php echo $cust_id; ?>">
                <input type="text" class="form-control" name="email" value="<?php echo $email; ?>" placeholder="Email">
                <input type="text" class="form-control" name="first" value="<?php echo $first; ?>" placeholder="First Name">
                <input type="text" class="form-control" name="last" value="<?php echo $last; ?>" placeholder="Last Name">
                <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>" placeholder="Primary Phone">
            </div>
        </div><!-- personal-info -->
        <!-- ************* PROFILE PIC ********************* -->
        <div class="col-xs-3" id="profile-info">
            <img id='profile-pic' src='<?php echo "{$profile_path}"; ?>' width="200" height="200">
            <div class="form-group">
                <input type="file" class="form-control" name="profile_pic" accept="image/*" onchange="document.getElementById('profile-pic').src = window.URL.createObjectURL(this.files[0])">
            </div>
<?php       
            if($cust_id >= 0 && has_profile_pic($cust_id)){
?>
                <div class="form-group">
                    <input type="button" class="btn" id="btn-delete-profile-pic" value="Delete Pic">
                </div>
<?php
            }
?>
        </div><!-- profile-info -->
        <div class="col-xs-2"></div>
    </div><!-- row: personal-info -->
    <!-- ************* END: PERSONAL INFO ROW ********************* -->
    <!-- ************* ADDRESSES ********************* -->
    <div class="row" id='addresses-row'>
<?php
    for($i = 0; $i < MAX_ADDRESSES; $i++){
?>
        <div class="col-xs-4" id="address-" . <?php echo $i; ?>>
            <h4>Address <?php echo $i+1; ?>:</h4>
            <div class="form-group">
                <input rel="<?php echo $i; ?>" type="button" class="btn btn-clear-addr" value="Clear Address">
            </div>
            <input type="hidden" class="form-control" name='<?php echo "add{$i}_id"; ?>' value="<?php echo $add[$i]['id']; ?>">
            <input type="text" class="form-control" id='<?php echo "add{$i}_street_line1"; ?>' name='<?php echo "add{$i}_street_line1"; ?>' value="<?php echo $add[$i]['street_line1']; ?>" placeholder="Street Line 1">
            <input type="text" class="form-control" id='<?php echo "add{$i}_street_line2"; ?>' name='<?php echo "add{$i}_street_line2"; ?>' value="<?php echo $add[$i]['street_line2']; ?>" placeholder="Street Line 2">
            <input type="text" class="form-control" id='<?php echo "add{$i}_city"; ?>' name='<?php echo "add{$i}_city"; ?>' value="<?php echo $add[$i]['city']; ?>" placeholder="City">
            <select id='<?php echo "add{$i}_state"; ?>' name='<?php echo "add{$i}_state"; ?>'>

                <option value="">State</option><!------ initial value                -->
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
            <input type="text" class="form-control" id='<?php echo "add{$i}_zip"; ?>' name='<?php echo "add{$i}_zip"; ?>' value="<?php echo $add[$i]['zip']; ?>" placeholder="ZIP">
        </div>
<?php
    } // end for-loop
?>
    </div> <!-- row -->
    <br>
    <!-- ************************ DOCUMENTS **************************************************************** -->
    <div class="row" id="documents-container">
        <div class="col-xs-12">
<?php
            if(!empty($cust_id)){
?>
                <h4>Documents:</h4>
                <input type="file" class="form-control" name="documents[]" accept="application/pdf" multiple>
                <br>
                <div id="documents-list">
                    <table id="documents-table" class='table table-hover table-bordered table-striped'>
                        <thead class='thead-dark'>
                            <tr>
                                <td>Filename</td>
                                <td>Date Uploaded</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
<?php
                            $docs = get_documents($cust_id);
                            foreach($docs as $doc){
                                echo "<tr>";
                                echo "  <td>{$doc['filename']}</td>";
                                echo "  <td>{$doc['datetime']}</td>";
                                echo "  <td><a rel='{$doc['id']}' href='javascript:void(0)'>Delete</a></td>";
                                echo "</tr>";
                            }
?>
                        </tbody>
                    </table>
                </div><!-- documents-list -->
<?php
            } // end if-!empty-cust_id
?>
        </div><!-- col-xs-12 -->
    </div><!-- documents-container -->
</form><!-- ********************************* END FORM ************************************ -->

<script>
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

    // CANCEL button
    $('#btn-cancel').on('click', function(){
        if(confirm("Are you sure you wish to cancel changes?\nOnly CHANGES made in the form WILL BE LOST!\nAny previously saved data will remain untouched.")){
            notifyUser("Form data not submitted.");
            resetLogin();
        }
    });

    // CLEAR ADDRESS button
    $('.btn-clear-addr').on('click', function(){
        if(confirm("Delete address?")){
            addr_num = $(this).attr('rel');
            $('#add' + addr_num + '_street_line1').val("");
            $('#add' + addr_num + '_street_line2').val("");
            $('#add' + addr_num + '_city').val("");
            $('#add' + addr_num + '_state').val("");
            $('#add' + addr_num + '_zip').val("");
            $('#add' + addr_num + '_state').prop('selectedIndex',0);
        }
    });

    // DELETE USER button
    $('#btn-delete-user').on('click', function(){
        if(confirm("Are you sure you wish to DELETE this user?\nAll changes in the form AND any data already saved in the database WILL BE LOST!\nThis data cannot be recovered once deleted.")){
            cust_id = $('#cust_id').val();
            if(cust_id >= 0){

                // Customer exists in DB, so purge from DB.
                $.post("delete.php", {cust_id: cust_id, action: 'delete-user'}, function(status){
                    if(status.localeCompare("1")){
                        notifyUser("Customer# " + cust_id + " deleted.");
                    } else {
                        notifyUser("Error attempting to delete customer# " + cust_id + ": " + status);
                    }
                    resetLogin();
                });
            } else {
                notifyUser("Form data not submitted.");
                resetLogin();
            }
        }
    });

    // DELETE PROFILE PIC button
    $('#btn-delete-profile-pic').on('click', function(){
        if(confirm("Are you sure you wish to DELETE your profile pic?\nThis change WILL be saved.")){
            cust_id = $('#cust_id').val();
            if(cust_id >= 0){
                // Customer exists in DB, so purge from DB.
                $.post("delete.php", {cust_id: cust_id, action: 'delete-profile-pic'}, function(status){
                    if(status.localeCompare("1")){
                        profile_path = '<?php echo PROFILE_PATH . "/" . DEFAULT_IMAGE; ?>'
                        $('#profile-pic').attr('src', profile_path);
                    } else {
                        notifyUser("Error deleting profile pic: " + status);
                    }
                });
            }
        }
    });

    // SUBMIT button
    $('#upload-user-form').submit(function(evt){
        evt.preventDefault();
        var url = $(this).attr('action');
        var formData = new FormData(this);
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (response) {
                notifyUser(response);
                resetLogin();
                thankYou();
            },
            cache: false,
            contentType: false,
            processData: false
        }).fail(function(){
            alert("There was a problem uploading your information.\nWe are sorry for the inconvenience.");
        });


    });
</script>

