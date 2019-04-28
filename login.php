<?php include_once "includes/db.php"; ?>
<?php

    if(isset($_POST['email'])){
        $email = clean($_POST['email']);

?>
<?php include "includes/submission_form.php"; ?>
<?php

    } else {

        echo "ERROR: Submission failed!";

    }

?>

