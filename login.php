<?php include_once "includes/db.php"; ?>
<?php

    if(isset($_POST['email'])){
        $email = clean($_POST['email']);

        $get_cust_info_query = "SELECT * FROM customers WHERE email = '$email'";
        $result_set = mysqli_query($connection, $get_cust_info_query);
        confirmQResult($result_set);
        $row = mysqli_fetch_assoc($result_set);
        $cust_id = $row['id'];
        $first = $row['first'];
        $last = $row['last'];
        $phone = $row['phone'];

?>
<?php include "includes/submission_form.php"; ?>
<?php

    } else {

        echo "ERROR: Submission failed!";

    }

?>

