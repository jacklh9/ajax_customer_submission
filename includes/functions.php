<?php include_once "db.php"; ?>
<?php

    function get_cust_id($email){
        $get_cust_id_query = "SELECT id FROM customers WHERE email = '$email'";
        $result = mysqli_query($connection, $get_cust_id_query);
        confirmQResult($result);
        return mysqli_fetch_assoc($result);
    }

?>
