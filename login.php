<?php include_once "includes/db.php"; ?>
<?php include_once "includes/functions.php"; ?>
<?php

    if(isset($_POST['email'])){
        $email = clean($_POST['email']);

        // Get customer personal info from DB
        $get_cust_info_query = "SELECT * FROM customers WHERE email = '$email'";
        $result_set = mysqli_query($connection, $get_cust_info_query);
        confirmQResult($result_set);
        $row = mysqli_fetch_assoc($result_set);
        $cust_id = $row['id'];
        $first = (empty($row['first'])) ? '' : $row['first'];
        $last = (empty($row['last'])) ? '' : $row['last'];
        $phone = (empty($row['phone'])) ? '' : $row['phone'];

        // Get customer addresses from DB
        $get_addresses_query = "SELECT * FROM addresses WHERE FK_cust_id = '$cust_id' LIMIT " . MAX_ADDRESSES;
        $result_set = mysqli_query($connection, $get_addresses_query);
        confirmQResult($result_set);
        for($i = 0; $i < MAX_ADDRESSES; $i++){
            $row = mysqli_fetch_assoc($result_set);
            $add[$i]['id'] = (empty($row['id'])) ? '' : $row['id'];
            $add[$i]['street_line1'] = (empty($row['street_line1'])) ? '' : $row['street_line1'];
            $add[$i]['street_line2'] = (empty($row['street_line2'])) ? '' : $row['street_line2'];
            $add[$i]['city'] = (empty($row['city'])) ? '' : $row['city'];
            $add[$i]['state'] = (empty($row['state'])) ? '' : $row['state'];
            $add[$i]['zip'] = (empty($row['zip'])) ? '' : $row['zip'];
        }

?>
<?php include "includes/submission_form.php"; ?>
<?php

    } else {

        echo "<div id='notification-bar' class='bg-danger'>ERROR: Login failed!</div>";

    }

?>

