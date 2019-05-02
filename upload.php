<?php include_once "includes/db.php"; ?>
<?php include_once "includes/functions.php"; ?>
<?php

    if(isset($_POST['email']) && !empty(clean($_POST['email']))){
        
        //echo "Inside upload.php: " . print_r($_POST);
        $email = clean($_POST['email']);
        $first = (isset($_POST['first'])) ? clean($_POST['first']) : '';
        $last = (isset($_POST['last'])) ? clean($_POST['last']) : '';
        $phone = (isset($_POST['phone'])) ? clean($_POST['phone']) : '';

        $cust_id = get_cust_id($email);
        if(empty($cust_id)){

            // Get or Create Customer
            $query = "INSERT INTO customers(email, first, last, phone) ";
            $query .= "VALUES('$email', '$first', '$last', '$phone')";

        } else {

            // Update Customer Personal Info
            $query = "UPDATE customers SET ";
            $query .= "first = '{$first}', ";
            $query .= "last = '{$last}', ";
            $query .= "phone = '{$phone}' ";
            $query .= "WHERE email = '{$email}'";

        }
        $result_set = mysqli_query($connection, $query);
        
        $cust_id = get_cust_id($email);

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
            $filename = $_FILES['profile_pic']['name'];

            // deal with non ASCII characters by setting the locale first
            setlocale(LC_ALL,'en_US.UTF-8');
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            // NAMING CONVENTION: id.ext
            $basename = $cust_id . "." . $ext;
            $destination = PROFILE_PATH . "/" . $basename;

            // Although move_uploaded_file() overwrites files,
            // we have no guarantee it's the same filename
            // because image extensions can vary; therefore,
            // we always delete the old image as a matter
            // of good housekeeping.
            delete_cust_profile_pic($cust_id);
            move_uploaded_file($tmp_file, $destination);
            update_profile_pic_filename($basename, $cust_id);
        }

    } else {

        die("Error saving data: email address is required.");

    }

?>