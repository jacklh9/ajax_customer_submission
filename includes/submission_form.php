<?php include_once "includes/functions.php"; ?>
<?php include_once "s3.php"; ?>
<form action="upload.php" id="upload-user-form" method="post" enctype="multipart/form-data">

    <div class="row" id="personal-info-row">
        <!-- *********** SUBMISSION CONTROLS **************  -->
        <div id="submission-controls" class="col-xs-2">
            <div class="form-group" id="submit-group">
<?php 
            if(empty($cust_id)){

                // This is a new customer
                $cust_id = -1;
                echo "<label for='upload'>New Customer Registration</label>";
                $submit_type = 'Register';

            } else {

                // This is an existing customer
                echo "<label for='upload'>Customer# {$cust_id}</label>";
                $submit_type = 'Save All';

            }
            $profile_pic = get_profile_pic_url($cust_id);
?>          
                <br>
                <br>
                <input type="submit" id="submit" class="btn btn-primary" name="upload" value="<?php echo $submit_type; ?>">

                <!-- The below serves as a dummy button when validation fails to indicate to user that submission is not available. -->
                <input type="button" id="disabled-submit" class="btn" name="disabled-upload" value="<?php echo $submit_type; ?>"><br>
            </div><!-- submit-group -->

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
       <div id="between-submission-controls-and-personal-info" class="col-xs-1"></div>
       <!-- ************* BEGIN: PERSONAL INFO ********************* -->
        <div id="personal-info" class="col-xs-5">
            <div class="form-group">
                <br><br>
                <input type="hidden" class="form-control" id="cust_id" name='cust_id' value="<?php echo $cust_id; ?>">
                <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Email">
                <input type="text" class="form-control" name="first" value="<?php echo $first; ?>" placeholder="First Name">
                <input type="text" class="form-control" name="last" value="<?php echo $last; ?>" placeholder="Last Name">
                <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>" placeholder="Primary Phone">
            </div>
        </div><!-- ************* END: PERSONAL INFO ********************* -->
        <div id="between-personal-info-and-profile-pic" class="col-xs-1"></div>
        <!-- ************* PROFILE PIC ********************* -->
        <div class="col-xs-3 text-center" id="profile-info">
            <br><br>
            <img id='profile-pic' src='<?php echo "{$profile_pic}"; ?>' class="image">
            <div class="form-group">
                <input type="file" class="form-control" name="profile_pic" accept="image/*" onchange="document.getElementById('profile-pic').src = window.URL.createObjectURL(this.files[0])">
                <label for="profile_pic" class="form-control">Update Profile Photo</label>
                <p><small class="form-text text-muted"><?php echo get_max_pic_size_in_MB() . " max"; ?></small></p>
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
        </div><!-- ************* END: PROFILE PIC ********************* -->
    </div><!-- row: personal-info -->
    <!-- ************* END: PERSONAL INFO ROW ********************* -->
    <!-- ************* ADDRESSES ********************* -->
    <div class="row" id='addresses-row'>
<?php
    for($i = 0; $i < MAX_ADDRESSES; $i++){
?>
        <div class="col-xs-4" id="address-" . <?php echo $i; ?>>
            <div class="form-group">
                <label for='<?php echo "clear_add{$i}"; ?>'>Address <?php echo $i+1; ?>:</label><br>
                <input rel="<?php echo $i; ?>" type="button" class="btn btn-clear-addr" value="Reset Address" name='<?php echo "clear_add{$i}"; ?>'>
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
            <div class="form-group">
            <br><br>
            <label for="document[]">Documents:</label><br>
<?php
                if(is_S3()){
?>
                    [Amazon S3 storage]<br>
<?php
                } else {
?>
                    [local server filesystem storage]<br><br>
<?php
                }
?>
                    <input type="file" class="form-control" name="documents[]" accept="application/pdf" multiple>
                    <p><small class="form-text text-muted">Add PDF (<?php echo get_max_doc_size_in_MB() . " max"; ?>)</small></p>
                    <p>NOTE: Click links to download to your Downloads directory or right-click and "Save-As" to rename.</p>
                </div>
                <br>
                <div id="documents-list">
                    <table id="documents-table" class='table table-hover table-bordered table-striped'>
                        <thead class='thead-dark'>
                            <tr>
                                <td>Original Filename</td>
                                <td>Date Uploaded</td>
                                <td>File Size</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
<?php
                            $docs = get_documents($cust_id);
                            foreach($docs as $doc){
                                echo "<tr id='doc-" . $doc['id'] . "'>";
                                echo "  <td><a rel='{$doc['id']}' class='link-view-doc' target='_blank' href='" . $doc['tmp_url'] . "'>{$doc['filename']}</a></td>";
                                echo "  <td>{$doc['datetime']}</td>";
                                echo "  <td>TBD</td>";
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
    <?php include_once "./includes/js/submission_form.js"; ?>
</script>

