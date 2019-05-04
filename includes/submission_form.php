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
                $profile_path = get_profile_pic($cust_id);
            } else {
                // This is a new customer
                $submit_type = 'Register';
                $profile_path = get_profile_pic_default();
                $cust_id = -1;
            }
?>          
            <div class="form-group">
                <input type="submit" id="submit" class="btn btn-primary" name="upload" value="<?php echo $submit_type; ?>"><br>

                <!-- The below serves as a dummy button when validation fails to indicate to user that submission is not available. -->
                <input type="button" id="disabled-submit" class="btn" name="disabled-upload" value="<?php echo $submit_type; ?>"><br>
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
                <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Email">
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
                                echo "<tr id='doc-" . $doc['id'] . "'>";
                                echo "  <td><a rel='{$doc['id']}' class='link-view-doc' target='_blank' href='" . $doc['path'] . "'>{$doc['filename']}</a></td>";
                                echo "  <td>{$doc['datetime']}</td>";
                                echo "  <td><a rel='{$doc['id']}' class='link-del-doc' href='javascript:void(0)'>Delete</a></td>";
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
    $(document).ready(function(){
        <?php include "includes/js/functions.js"; ?>

        // Initial state of the submit buttons
        // For purpose, see "validate" function(s) below.
        $('input#submit').show();
        $('input#disabled-submit').hide();

        function resetLogin(){
            $('#upload-user-form').hide();
            $('#upload-user-form')[0].reset();
            $('#login-user-form')[0].reset();
            $('#login-user-form').show();
            show_registered_users();
        }

        // POST-SUBMISSION
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
                var addr_num = $(this).attr('rel');
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
                var cust_id = $('#cust_id').val();
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
            
            if(confirm("Are you sure you wish to DELETE your profile pic?\nThis change WILL be saved immediately.")){
                var cust_id = $('#cust_id').val();
                if(cust_id >= 0){
                    // Customer exists in DB, so purge from DB.
                    $.post("delete.php", {cust_id: cust_id, action: 'delete-profile-pic'}, function(status){
                        if(status.localeCompare("1")){
                            var profile_path = '<?php echo get_profile_pic_default(); ?>';
                                $('#profile-pic').attr('src', profile_path);
                        } else {
                            notifyUser("Error deleting profile pic: " + status);
                        }
                    });
                }
            }
        });

        // DELETE DOC link
        $('.link-del-doc').on('click', function(){
            var doc_id = $(this).attr('rel');
            var filename = $(this).closest('td').prev('td').prev('td').text();
            var is_S3 = "<?php echo is_S3(); ?>";
            if(is_S3.localeCompare("")){

                // S3 env
                alert("Deletion of document '" + filename + "'\nnot yet implemented.\nClick any button to close this window.");

            } else {

                // non-S3 env
                if(confirm("Delete document '" + filename + "' immediately?\nClick OK to confirm.\n\nWARNING:This cannot be undone.")){
                    // Purge doc from DB and from filesystem
                    $.post("delete.php", {doc_id: doc_id, action: 'delete-document'}, function(status){
                        if(status.localeCompare("1")){
                            // Successfully deleted document
                            var profile_path = '<?php echo get_profile_pic_default(); ?>';
                                                    $('tr#doc-' + doc_id).hide();
                            notifyUser("Successfuly deleted document '" + filename + "'");
                        } else {
                            notifyUser("Error deleting document '" + filename + "': " + status);
                        }
                    });
                }
            }
        });


        // VALIDATE EMAIL not in use
        $('input#email').keyup(function(){
            var email = $('#email').val();
            var cust_id = $('#cust_id').val();
            $.post("validate.php", {validate: 'email', email: email, cust_id: cust_id}, function(status){
                if(status.localeCompare("1")){
                    // email is available
                    $('input#submit').show();
                    $('input#disabled-submit').hide();
                    notifyUser("TEST STATUS OK: " + status);  // TODO: this is only a test
                } else {
                    // email address already in use
                    $('input#submit').hide();
                    $('input#disabled-submit').show();
                    notifyUser("ERROR: " + status);
                }
            });
        });

        // // VIEW DOC link
        // $('.link-view-doc').on('click', function(){
        //     var doc_id = $(this).attr('rel');
        //     var filename = $(this).text();
        //     alert("Viewing of document '" + filename + "'\nnot yet implemented.\nClick any button to close this window.");
        //     // if(confirm()){
        //            //... 
        //     // }
        // });

        // SUBMIT button
        $('#upload-user-form').submit(function(evt){
            evt.preventDefault();
            notifyUser("Saving data...");
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
    });
</script>

