<?php include_once "includes/db.php"; ?>
<?php include "includes/functions.php"; ?>
<?php

debug_to_console("Inside delete.php");

if(isset($_POST['cust_id'])){

    // Delete Customer    
    $cust_id = clean($_POST['cust_id']);
    if(delete_addresses_by_cust($cust_id)){
        delete_cust($cust_id);
    }
}


?>
