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
                <br><br><!-- new-lines between "Customer#/New Registration" label and submit/register button -->
                <input type="submit" id="submit" class="btn btn-primary" name="upload" value="<?php echo $submit_type; ?>">

                <!-- The below serves as a dummy button when validation fails to indicate to user that submission is not available. -->
                <input type="button" id="submit-disabled" class="btn" name="submit-disabled" value="<?php echo $submit_type; ?>">

                <!-- The below serves as a dummy butten while waiting for a previous submit to complete -->
                <input type="button" id="submit-disabled-while-processing" class="btn" name="submit-disabled-while-processing" value="Processing">
            </div><!-- submit-group -->

            <div class="form-group">
                <input type="button" class="btn btn-default" id="btn-cancel" value="Cancel Changes"><br>
            </div>

<?php      if($cust_id >= 0){
                // Customer exists in DB, so display delete button
?>
                <br>
                <div class="form-group">
                    <label for="btn-delete-user">Danger Zone:</label>
                    <p><small class="form-text text-muted">WARNING: ALL data in the form AND in the database and saved files will be lost permanently</small></p>
                    <input type="button" class="btn btn-danger" id="btn-delete-user" value="Delete User"><br>
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
        <!-- ************* PROFILE PIC PREVIEW ********************* -->
        <div class="col-xs-3 text-center" id="profile-info">
            <br><br><img id='profile-pic' src='<?php echo "{$profile_pic}"; ?>' class="image-bounded img-rounded"><br><br>
            <div class="form-group">
                <label for="profile_pic">Change Profile Photo:</label><br>

                <!-- ********************* PROFILE PIC SELECTOR ************************************  -->
                <input id="add-profile-pic" type="file" class="form-control" name="profile_pic" accept="image/*">
                <p><small class="form-text text-muted">Update Profile Photo (<?php echo get_max_pic_size_in_MB() . " max"; ?>)</small></p>
            </div>
<?php       
            if($cust_id >= 0 && has_profile_pic($cust_id)){
?>
                <div class="form-group">
                    <input type="button" class="btn btn-xs btn-warning" id="btn-delete-profile-pic" value="Delete Pic">
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
                <input rel="<?php echo $i; ?>" type="button" class="btn btn-xs btn-warning btn-clear-addr" value="Reset Address" name='<?php echo "clear_add{$i}"; ?>'>
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
    <!-- ************************ UPLOAD USER DOCUMENTS **************************************************************** -->    
    <div class="row" id="upload-documents-container">
        <div class="col-xs-12">
            <div id='num-docs-uploading'></div>
            <div id='user-documents-selector-form-group' class="form-group">
                <label for="document[]">Upload Documents:</label><br>
                <input type="file" id="user-documents-selector" class="form-control" name="documents[]" accept="application/pdf" multiple>
                <p><small class="form-text text-muted">PDF only (<?php echo get_max_doc_size_in_MB() . " max"; ?>)</small></p>
            </div><!-- form-group -->
        </div><!-- col-xs-12 -->
    </div><!-- upload-documents-container -->
    <br>
    <!-- ************************ DOCUMENTS **************************************************************** -->
    <div class="row" id="documents-container">
        <div class="col-xs-12">
<?php
            if($cust_id >= 0){
?>
                <label for="document[]">Documents:</label><br>
<?php
                if(is_S3()){
?>
                    [Storage: Amazon S3]<br>
<?php
                } else {
?>
                    [Storage: server filesystem]<br><br>
<?php
                }
?>
                <p>NOTE: Click links to download to your Downloads directory or right-click and "Save-As" to rename.</p>
                <div id="documents-list">
                    <table id="documents-table" class='table table-hover table-bordered table-striped'>
                        <thead class='thead-dark'>
                            <tr class='bg-primary'>
                                <td>Original Filename</td>
                                <td>Date Uploaded</td>
                                <td>File Size</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
<?php
                            $docs = get_documents($cust_id);
                            $num_rows = count($docs);
                            if($num_rows > 0){
                                foreach($docs as $doc){
                                    echo "<tr id='doc-" . $doc['id'] . "'>";
                                    echo "  <td><a rel='{$doc['id']}' class='link-view-doc' target='_blank' href='" . $doc['tmp_url'] . "'>{$doc['filename']}</a></td>";
                                    echo "  <td>{$doc['datetime']}</td>";
                                    echo "  <td>", convert_bytes_to_MB($doc['size']) ,"</td>";
                                    echo "  <td class='delete-doc-container text-center'><a rel='{$doc['id']}' class='link-del-doc btn btn-xs btn-warning' role='button' href='javascript:void(0)'>Delete</a></td>";
                                    echo "</tr>";
                                } 
                            } else {
                                    echo "<tr id='no-documents-row'>";
                                        echo "<td id='no-documents-filename'>", NO_USER_DOCS_FOUND_MSG, "</td>";
                                        echo "<td id='no-documents-datetime'>", NO_USER_DOCS_INFO_MSG, "</td>";
                                        echo "<td id='no-documents-size'>", NO_USER_DOCS_INFO_MSG, "</td>";
                                        echo "<td id='no-documents-delete'><a class='placeholder-button btn btn-xs btn-default' role='button' href='javascript:void(0)'>Delete</a></td>";
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

