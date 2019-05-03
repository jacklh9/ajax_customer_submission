<?php include_once "includes/db.php"; ?>
<?php include_once "includes/functions.php"; ?>
<?php

$success = FALSE;

if(isset($_POST['cust_id'])){
    $cust_id = clean($_POST['cust_id']);

    switch($_POST['action']){

        case 'delete-user':
            echo delete_cust_and_related($cust_id);
            
            $success = delete_cust_and_related($cust_id);
            break;

        case 'delete-profile-pic':
            $success = delete_profile_pic($cust_id);
            break;

        default:
            break;
    }
}

echo $success;
?>
