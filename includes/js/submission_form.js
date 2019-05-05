$(document).ready(function(){
    <?php include_once "includes/js/functions.js"; ?>

    // ****************** GLOBAL VARS ***********************

    ////// GLOBAL STATIC
    window.MY_GLOBALS = {};

    // SEE: startUpLoadTimer(), Submit Form section
    //       Document File Upload Count section
    MY_GLOBALS.num_docs_uploading = 0; 

    ////// STANDARD GLOBALS
    var messageEnterValidEmail = "Enter valid email address";

    // ***************** END GLOBAL VARS *******************

    // ************* BEGIN INITIAL STATE *******************

    // We begin by immediately disabling submit button
    // so we can first do some validations.
    submit_deny_while_validating();

    // What does the email field look like right now?
    var email = $('input#email').val();
    if(is_valid_email(email)){

        // Looks okay, so enable the submit button.
        submit_enabled();
    } else {

        // Initial email entered is no good. Ask user to try again.
        notifyUser(messageEnterValidEmail);
    }

    // **************** END INITIAL STATE *******************

    // *************** BEGIN FUNCTION ***********************

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
                        $('#btn-delete-profile-pic').hide();
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

    // DOCUMENT FILE UPLOAD COUNT
    $('#user-documents-selector').change(function(){
        var files = $(this)[0].files;

        /// update static global var 
        MY_GLOBALS.num_docs_uploading = files.length;
        $('#user-documents-selector-form-group').hide();
        $('#num-docs-uploading').html("<strong>" + MY_GLOBALS.num_docs_uploading + " documents ready to upload.</strong>");
    });


    // PREVIEW PIC updated and DELETE PIC button hidden
    $('input#add-profile-pic').change(function(){
        // When this input is changed, the below native-JS will update the profile pic preview image
        document.getElementById('profile-pic').src = window.URL.createObjectURL(this.files[0]);

        // We then remove the delete button because the delete button will never be able to clear the input control
        // and will only delete the preview image, but on submission the input field will put the picture back.
        // It causes confusion and is best to just hide the delete button at this juncture. 
        $('#btn-delete-profile-pic').hide();
    });


    function resetLogin(){
        $('#upload-user-form').hide();
        $('#upload-user-form')[0].reset();
        $('#login-user-form')[0].reset();
        $('#login-user-form').show();
        show_registered_users();
    }

    function startUploadTimer(){
        MY_GLOBALS.doc_save_interval_id = setInterval(function(){
            // On avg, we upload one doc per second
            notifyUser("Uploading documents. Please wait ... " + MY_GLOBALS.num_docs_uploading);
            MY_GLOBALS.num_docs_uploading--;
            if(MY_GLOBALS.num_docs_uploading <= 0){
                // kill thyself
                clearInterval(MY_GLOBALS.doc_save_interval_id);
            }
        }, 1000); // 1000 = 1 sec
    }

    // SUBMIT DISABLE
    function submit_disabled(){
        $('input#submit').hide();
        $('input#disasbled-submit-while-processingg').show();
    }

    // SUBMIT ENABLE
    function submit_enabled(){
        $('input#disabled-submit-while-validating').hide();
        $('input#disabled-submit-while-processingg').hide();
        $('input#submit').show();
    }


    // ************************ SUBMIT FORM *******************************
    $('#upload-user-form').submit(function(evt){
        evt.preventDefault();
        submit_disabled();
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
                    
                    // Start upload countdown
                    // so user knows how long
                    // to expect the upload
                    // of one doc per second
                    // per sleep_between_doc_saves constant
                    // in functions.php
                    startUploadTimer();

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
                        submit_enabled(); // So user can retryyyyy
                    });

                } else {
                    // email address already in use
                    notifyUser("ERROR: Email address in use.");

                    // Do not re-enable submit. Let the validator do it.
                    // Too risky to submit while an invalid email chosen.
                }
            })
            .fail(function(){
                notifyUser("ERROR: Unable to communicate with the server.");
                submit_enabled(); // So user can retry
            });
        } else {
            notifyUser(messageEnterValidEmail); 
        }
    });


    // THANK YOU: POST-SUBMISSION
    function thankYou(){
        alert("Thank you. Your information has been successfully submitted.");
    }

    // ******************************** VALIDATIONS ******************************************

    // VALIDATION: disabled-submit due to bad email or already in use
    $('input#disabled-submit-while-validating').on('click', function(){
        notifyUser("Unable to submit: bad email address or already in use.");
    });

    // VALIDATE EMAIL not in use
    $('input#email').keyup(function(){
        submit_deny_while_validating(); // We do this here to avoid race condition
        var email = $(this).val();
        var cust_id = $('#cust_id').val();

        if(is_valid_email(email)){
            notifyUser(""); // Clear any past transgressions
            $.post("validate.php", {validate: 'email', email: email, cust_id: cust_id}, function(status){
                if($.trim(status) === "1"){
                    // email is available
                    submit_enabled();
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

    function submit_deny_while_validating(){
            $('input#submit').hide();
            $('input#disabled-submit-while-validating').show();
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

});