$(document).ready(function(){
    <?php include_once "includes/js/functions.js"; ?>

    function resetLogin(){
        $('#upload-user-form').hide();
        $('#upload-user-form')[0].reset();
        $('#login-user-form')[0].reset();
        $('#login-user-form').show();
        show_registered_users();
    }

    // POST-SUBMISSION
    function thankYou(){
        alert("Thank you. Your information has been successfully submitted.");
    }

    // CANCEL button
    $('#btn-cancel').on('click', function(){
        if(confirm("Are you sure you wish to cancel changes?\nOnly CHANGES made in the form WILL BE LOST!\nAny previously saved data will remain untouched.")){
            notifyUser("Form data not submitted.");
            resetLogin();
        }
    });

    // CLEAR ADDRESS button
    $('.btn-clear-addr').on('click', function(){
        if(confirm("Delete address?")){
            var addr_num = $(this).attr('rel');
            $('#add' + addr_num + '_street_line1').val("");
            $('#add' + addr_num + '_street_line2').val("");
            $('#add' + addr_num + '_city').val("");
            $('#add' + addr_num + '_state').val("");
            $('#add' + addr_num + '_zip').val("");
            $('#add' + addr_num + '_state').prop('selectedIndex',0);
        }
    });

    // DELETE USER button
    $('#btn-delete-user').on('click', function(){
        if(confirm("Are you sure you wish to DELETE this user?\nAll changes in the form AND any data already saved to the database WILL BE LOST!\nThis data cannot be recovered once deleted.")){
            var cust_id = $('#cust_id').val();
            if(cust_id >= 0){

                // Customer exists in DB, so purge from DB.
                $.post("delete.php", {cust_id: cust_id, action: 'delete-user'}, function(status){
                    if($.trim(status) === "1"){
                        notifyUser("Customer# " + cust_id + " deleted.");
                    } else {
                        notifyUser("Error attempting to delete customer# " + cust_id + ": " + status);
                    }
                    resetLogin();
                });
            } else {
                notifyUser("Form data not submitted.");
                resetLogin();
            }
        }
    });

    // DELETE PROFILE PIC button
    $('#btn-delete-profile-pic').on('click', function(){
        
        if(confirm("Are you sure you wish to DELETE your profile pic?\nThis change WILL be saved immediately.")){
            var cust_id = $('#cust_id').val();
            if(cust_id >= 0){
                // Customer exists in DB, so purge from DB.
                $.post("delete.php", {cust_id: cust_id, action: 'delete-profile-pic'}, function(status){
                    if($.trim(status) === "1"){
                        var profile_path = '<?php echo get_profile_pic_default(); ?>';
                        $('#profile-pic').attr('src', profile_path);
                        
                        // Now hide the delete button as there is no more photo to delete.
                        $(#btn-delete-profile-pic).hide();
                    } else {
                        notifyUser("Error deleting profile pic: " + status);
                    }
                });
            }
        }
    });

    // DELETE DOC link
    $('.link-del-doc').on('click', function(){
        var doc_id = $(this).attr('rel');
        var filename = $(this).closest('td').prev('td').prev('td').text();
        var is_S3 = "<?php echo is_S3(); ?>";
        if($.trim(status) === "1"){

            // S3 env
            alert("Deletion of document '" + filename + "'\nnot yet implemented.\nClick any button to close this window.");

        } else {

            // non-S3 env
            if(confirm("Delete document '" + filename + "' immediately?\nClick OK to confirm.\n\nWARNING:This cannot be undone.")){
                // Purge doc from DB and from filesystem
                $.post("delete.php", {doc_id: doc_id, action: 'delete-document'}, function(status){
                    if($.trim(status) === "1"){
                        // Successfully deleted document
                        var profile_path = '<?php echo get_profile_pic_default(); ?>';
                                                $('tr#doc-' + doc_id).hide();
                        notifyUser("Successfully deleted document '" + filename + "'");
                    } else {
                        notifyUser("Error deleting document '" + filename + "': " + status);
                    }
                });
            }
        }
    });

    // ******************************** VALIDATIONS ******************************************

    // Initial state of the submit buttons
    var messageEnterValidEmail = "Enter valid email address";
    deny_submit();

    var email = $('input#email').val();
    if(is_valid_email(email)){
        allow_submit();
    } else {
        notifyUser(messageEnterValidEmail);
    }

    $('input#disabled-submit').on('click', function(){
        notifyUser("Unable to submit: bad email address or already in use.");
    });

    // VALIDATE EMAIL not in use
    $('input#email').keyup(function(){
        deny_submit(); // We do this here to avoid race condition
        var email = $(this).val();
        var cust_id = $('#cust_id').val();

        if(is_valid_email(email)){
            notifyUser(""); // Clear any past transgressions
            $.post("validate.php", {validate: 'email', email: email, cust_id: cust_id}, function(status){
                if($.trim(status) === "1"){
                    // email is available
                    allow_submit();
                } else {
                    // email address already in use
                    notifyUser("ERROR: Email address in use.");
                }
            })
            .fail(function(){
                notifyUser("ERROR: Unable to communicate with the server.");
            });
        } else {
            notifyUser(messageEnterValidEmail);
        }
    });

    function allow_submit(){
            $('input#disabled-submit').hide();
            $('input#submit').show();
    }

    function deny_submit(){
            $('input#submit').hide();
            $('input#disabled-submit').show();
    }

    // SOURCE: https://www.w3resource.com/javascript/form/email-validation.php
    function is_valid_email(email) {
        var success = false;
        if(email.length <= MAX_EMAIL_LEN){
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)){
            success = true;
            }
        }
        return success;
    }


    // ******************************** END VALIDATIONS ******************************************

    
    // // VIEW DOC link
    // $('.link-view-doc').on('click', function(){
    //     var doc_id = $(this).attr('rel');
    //     var filename = $(this).text();
    //     alert("Viewing of document '" + filename + "'\nnot yet implemented.\nClick any button to close this window.");
    //     // if(confirm()){
    //            //... 
    //     // }
    // });

    // SUBMIT button
    $('#upload-user-form').submit(function(evt){
        evt.preventDefault();
        notifyUser("Saving data...");
        var url = $(this).attr('action');
        var formData = new FormData(this);
        var email = $(this).find('input#email').val();
        var cust_id = $('#cust_id').val();

        // One last validation that email is not in-use before we submit form data
        if(is_valid_email(email)){
            notifyUser(""); // Clear any past transgressions
            $.post("validate.php", {validate: 'email', email: email, cust_id: cust_id}, function(status){
                if($.trim(status) === "1"){

                    // email is available
                    // submit form data
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            notifyUser(response);
                            resetLogin();
                            thankYou();
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    }).fail(function(){
                        alert("There was a problem uploading your information.\nWe are sorry for the inconvenience.");
                    });

                } else {
                    // email address already in use
                    notifyUser("ERROR: Email address in use.");
                }
            })
            .fail(function(){
                notifyUser("ERROR: Unable to communicate with the server.");
            });
        } else {
            notifyUser(messageEnterValidEmail); 
        }

    });
});