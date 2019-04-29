<?php include_once "includes/db.php"; ?>
<?php include "functions.php"; ?>
<?php

    if(isset($_POST['email'])){
        $email = clean($_POST['email']);
        $first = clean($_POST['first']);
        $last = clean($_POST['last']);
        $phone = clean($_POST['phone']);
        
        // Get or Create Customer
        $cust_id = get_cust_id($email);
        if(empty($cust_id)){
            $create_new_cust_query = "INSERT INTO customers(email) VALUES('$email')";
            $result = mysqli_query($connection, $create_new_cust_query);
            confirmQResult($result);
            $cust_id = get_cust_id($email);
        }

        $get_cust_query = "SELECT * FROM customers WHERE email = '$email'";
        $result = mysqli_query($connection, $get_cust_query);
        confirmQResult($result);
        $cust = mysqli_fetch_assoc($result);

        // Update Customer Personal Info
        $update_cust_info = "UPDATE customers SET " . 
        $update_cust_info .= "{$cust['first']} = '{$first}' AND ";
        $update_cust_info .= "{$cust['last']} = '{$last}' AND ";
        $update_cust_info .= "{$cust['phone']} = '{$phone}' ";
        $update_cust_info .= "WHERE email = '{$email}'";
        $update_result = mysqli_query($connection, $update_cust_info);
        confirmQResult($update_result);


        // // Update Customer Addresses
        // $MAX_ADDRESSES = 3;


        // // Get customer personal info
        // $get_cust_info_query = "SELECT * FROM customers WHERE email = '$email'";
        // $result_set = mysqli_query($connection, $get_cust_info_query);
        // confirmQResult($result_set);
        // $row = mysqli_fetch_assoc($result_set);
        // $cust_id = $row['id'];
        // $first = (defined($row['first']) && !empty($row['first'])) ? $row['first'] : '';
        // $last = (empty($row['last'])) ? '' : $row['last'];
        // $phone = (empty($row['phone'])) ? '' : $row['phone'];

        // // Get customer addresses
        
        // $get_addresses_query = "SELECT * FROM addresses WHERE FK_cust_id = '$cust_id' LIMIT {$MAX_ADDRESSES}";
        // $result_set = mysqli_query($connection, $get_addresses_query);
        // confirmQResult($result_set);
        // for($i = 0; $i < $MAX_ADDRESSES; $i++){
        //     $row = mysqli_fetch_assoc($result_set);
        //     $add[$i]['street_line1'] = (empty($row['street_line1'])) ? '' : $row['street_line1'];
        //     $add[$i]['street_line2'] = (empty($row['street_line2'])) ? '' : $row['street_line2'];
        //     $add[$i]['city'] = (empty($row['city'])) ? '' : $row['city'];
        //     $add[$i]['state'] = (empty($row['state'])) ? '' : $row['state'];
        //     $add[$i]['zip'] = (empty($row['zip'])) ? '' : $row['zip'];
        // }

?>
<?php include "includes/submission_form.php"; ?>
<?php

    } else {

        echo "<div id='notification-bar' class='bg-danger'>ERROR: Submission failed!</div>";

    }

?>