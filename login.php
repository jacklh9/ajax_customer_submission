<?php include_once "includes/db.php"; ?>
<?php

    if(isset($_POST['email'])){
        $email = clean($_POST['email']);

        // Get customer personal info
        $get_cust_info_query = "SELECT * FROM customers WHERE email = '$email'";
        $result_set = mysqli_query($connection, $get_cust_info_query);
        confirmQResult($result_set);
        $row = mysqli_fetch_assoc($result_set);
        $cust_id = $row['id'];
        $first = $row['first'];
        $last = $row['last'];
        $phone = $row['phone'];

        // Get customer addresses
        define(MAX_ADDRESSES, 3);
        $get_addresses_query = "SELECT * FROM addresses WHERE FK_cust_id = '$cust_id' LIMIT 3";
        $result_set = mysqli_query($connection, $get_addresses_query);
        confirmQResult($result_set);
        for($i = 0; $i < MAX_ADDRESSES; $i++){
            $row = mysqli_fetch_assoc($result_set);
            $add[$i]['street1'] = $row['street1'];
            $add[$i]['street2'] = $row['street2'];
            $add[$i]['city'] = $row['city'];
            $add[$i]['state'] = $row['state'];
            $add[$i]['zip'] = $row['zip'];
        }

?>
<?php include "includes/submission_form.php"; ?>
<?php

    } else {

        echo "ERROR: Submission failed!";

    }

?>

