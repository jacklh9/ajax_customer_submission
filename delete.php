<?php include_once "includes/db.php"; ?>
<?php include_once "includes/functions.php"; ?>
<?php

    $success = FALSE;

    if(isset($_POST['action'])){

        switch($_POST['action']){

            case 'delete-user':
                $cust_id = clean($_POST['cust_id']);
                $success = delete_cust_and_related($cust_id);
                break;

            case 'delete-profile-pic':
                $cust_id = clean($_POST['cust_id']);
                $success = delete_profile_pic($cust_id);
                break;

            case 'delete-document':
                $doc_id = clean($_POST['doc_id']);
                $success = delete_document($doc_id);
                break;

            default:
                break;
        }
    }

    echo $success;
?>
