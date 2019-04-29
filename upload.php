<?php include_once "includes/db.php"; ?>
<?php include "includes/functions.php"; ?>
<?php

    if(isset($_POST['email'])){
        $email = clean($_POST['email']);
        $first = clean($_POST['first']);
        $last = clean($_POST['last']);
        $phone = clean($_POST['phone']);
        
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
        confirmQResult($result_set);
        $cust_id = get_cust_id($email);

        // Update/Insert Customer Addresses
        $MAX_ADDRESSES = 3;

        for($i = 0; $i < $MAX_ADDRESSES; $i++){
            $street_line1 = clean($_POST['add' . $i . '_street_line1']);
            $street_line2 = clean($_POST['add' . $i . '_street_line2']);
            $city = clean($_POST['add' . $i . '_city']);
            $state = clean($_POST['add' . $i . '_state']);
            $zip = clean($_POST['add' . $i . '_zip']);
            $add_id = clean($_POST['add' . $i . '_id']);

            if(empty($add_id)){
            
                // Insert Address
                $query = "INSERT INTO addresses(street_line1, street_line2, city, state, zip, FK_cust_id) ";
                $query .= "VALUES('$street_line1', '$street_line2', '$city', '$state', '$zip', '$cust_id')";

            } else {
            
                // Update Address
                $query = "UPDATE addresses SET ";
                $query .= "street_line1 = '$street_line1'";
                $query .= "street_line2 = '$street_line2'";
                $query .= "city = '$city'";
                $query .= "state = '$state'";
                $query .= "zip = '$zip'";
                $query .= "WHERE id = $add_id";
            }
            $result_set = mysqli_query($connection, $query);
            confirmQResult($result_set);
        }

    } else {

        die("Error saving data.");

    }

?>