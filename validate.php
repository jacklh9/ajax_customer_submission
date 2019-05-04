<?php include_once "includes/db.php"; ?>
<?php include_once "includes/functions.php"; ?>
<?php
    $success = FALSE;

    if(isset($_POST['validate'])){
        switch($_POST['validate']){
            case 'email':
                $cust_id = clean($_POST['cust_id']);
                $email = clean($_POST['email']);
                // TODO: validate email not in use with diff cust_id
                break;
            
            default:
                echo "Invalid validation type: {$_POST['validate']}";
                break;
        }
    }
    return $success;
?>