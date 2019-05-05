<?php include_once "includes/db.php"; ?>
<?php include_once "includes/functions.php"; ?>
<?php

    if(isset($_POST['email']) 
        && isset($_POST['cust_id'])
        && !empty($_POST['email'])
        && is_valid_email(clean($_POST['email']))
        && !is_email_inuse_by_another(clean($_POST['email']), $_POST['cust_id'])){
        
        $email = clean($_POST['email']);
        $first = (isset($_POST['first'])) ? clean($_POST['first']) : '';
        $last = (isset($_POST['last'])) ? clean($_POST['last']) : '';
        $phone = (isset($_POST['phone'])) ? clean($_POST['phone']) : '';
        $cust_id = (isset($_POST['cust_id'])) ? clean($_POST['cust_id']) : '';

        // submission_form.php already sets cust_id to -1 if email not in DB
        if($cust_id >= 0){

            // Update Existing Customer's Personal Info
            $query = "UPDATE customers SET ";
            $query .= "email = '{$email}', ";
            $query .= "first = '{$first}', ";
            $query .= "last = '{$last}', ";
            $query .= "phone = '{$phone}' ";
            $query .= "WHERE id = {$cust_id}";

        } else {

            // Get or Create Customer
            $query = "INSERT INTO customers(email, first, last, phone) ";
            $query .= "VALUES('$email', '$first', '$last', '$phone')";

        }
        $result_set = mysqli_query($connection, $query);
        if(confirmQResult($result_set)){
            if($cust_id < 0){
                // Get new cust_id if newly created
                $cust_id = get_cust_id($email);
            }
        }

        // Update/Insert Customer Addresses
        for($i = 0; $i < MAX_ADDRESSES; $i++){
            $street_line1 = (isset($_POST['add' . $i . '_street_line1'])) ? clean($_POST['add' . $i . '_street_line1']) : '';
            $street_line2 = (isset($_POST['add' . $i . '_street_line2'])) ? clean($_POST['add' . $i . '_street_line2']) : '';
            $city =         (isset($_POST['add' . $i . '_city'])) ? clean($_POST['add' . $i . '_city']) : '';
            $state =        (isset($_POST['add' . $i . '_state'])) ? clean($_POST['add' . $i . '_state']) : '';
            $zip =          (isset($_POST['add' . $i . '_zip'])) ? clean($_POST['add' . $i . '_zip']) : '';
            $add_id =       (isset($_POST['add' . $i . '_id'])) ? clean($_POST['add' . $i . '_id']) : '';

            if(empty($add_id)){
            
                // Insert Address
                $query = "INSERT INTO addresses(street_line1, street_line2, city, state, zip, FK_cust_id) ";
                $query .= "VALUES('$street_line1', '$street_line2', '$city', '$state', '$zip', $cust_id)";

            } else {

                // Update Address
                $query = "UPDATE addresses SET ";
                $query .= "street_line1 = '$street_line1', ";
                $query .= "street_line2 = '$street_line2', ";
                $query .= "city = '$city', ";
                $query .= "state = '$state', ";
                $query .= "zip = '$zip' ";
                $query .= "WHERE id = $add_id";
                
            }
            $result_set = mysqli_query($connection, $query);
            confirmQResult($result_set);
        }

        // Update/Insert Profile Pic
        if(!empty($_FILES['profile_pic']['name'])){
            
            $tmp_file = $_FILES['profile_pic']['tmp_name'];
            $filename = clean($_FILES['profile_pic']['name']);

            $file['name'] = $filename;
            $file['size'] = $_FILES['profile_pic']['size'];

            if(is_valid_file($file, VALID_PIC_EXTENSIONS, MAX_PIC_SIZE)){
                update_profile_pic($tmp_file, $filename, $cust_id);
            }
        }

        // Process document uploads if at least one exists
        if(!empty($_FILES['documents']['name'][0])) {

            $file_ary = re_array_files($_FILES['documents']);
        
            foreach ($file_ary as $file) {
                $orig_filename = clean($file['name']);
                if(is_valid_file($file, VALID_DOC_EXTENSIONS, MAX_DOC_SIZE)){
                    add_document($file['tmp_name'], $orig_filename, $cust_id);
                }
            }
        }

    } else {

        echo "Error saving data: email address is required or already in use.";

    }

?>