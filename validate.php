<?php include_once "includes/db.php"; ?>
<?php include_once "includes/functions.php"; ?>
<?php
    $success = FALSE;

    if(isset($_POST['validate'])){
        switch($_POST['validate']){
            case 'email':
                $cust_id = clean($_POST['cust_id']);
                $email = clean($_POST['email']);
                if(strlen($email) <= MAX_EMAIL_LEN){
                    $success = !is_email_inuse_by_another($email, $cust_id);
                }
                break;
            
            default:
                echo "Invalid validation type: {$_POST['validate']}";
                break;
        }
    }
    echo $success;
?>