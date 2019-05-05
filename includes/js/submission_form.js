$(document).ready(function(){
    <?php include_once "includes/js/functions.js"; ?>

    // ****************** GLOBAL VARS ***********************

    ////// GLOBAL STATIC
    window.MY_GLOBALS = {};

    // SEE: startUpLoadTimer(), Submit Form section
    //       Document File Upload Count section
    MY_GLOBALS.num_docs_uploading = 0;

    // The initial number of document rows displayed
    // We will subtract this number and at 0
    // display the default #empty-documents-row.
    MY_GLOBALS.num_docs_shown = $('input#num-docs-found').val();
    update_num_docs_found();

    ////// STANDARD GLOBALS
    var messageEnterValidEmail = "Enter valid email address";

    // ***************** END GLOBAL VARS *******************

    // ************* BEGIN: SET INITIAL STATES *******************

    // Initially the submit button is disabled until the 
    // email validation confirms we can submit.
    // We allow user to cancel page or delete acct 
    // (if valid cust_id) while we valid the email.
    btn_submit_disabled();
    btn_cancel_enabled();
    btn_delete_acct_enabled();

    // What does the email field look like right now?
    var email = $('input#email').val();
    if(is_valid_email(email)){

        // Looks okay, so enable the submit button.
        btn_submit_enabled();
    } else {

        // Initial email entered is no good. 
        // Leave submit button disabled, and
        // ask user to try again.
        notifyUser(messageEnterValidEmail);
    }

    // **************** END: INITIAL STATE *******************

    // *************** BEGIN: FUNCTIONS ***********************

    //  BUTTON CANCEL BUSY (SUBMITTING)
    function btn_cancel_busy(){
        $('#btn-cancel-busy').show();
        $('#btn-cancel-disabled').hide();
        $('#btn-cancel-enabled').hide();
        $('.hide-on-submit').hide();
    }

    // BUTTON CANCEL DISABLED
    function btn_cancel_disabled(){
        $('#btn-cancel-busy').hide();
        $('#btn-cancel-disabled').show();
        $('#btn-cancel-enabled').hide();
    }

    // BUTTON CANCEL ENABLED
    function btn_cancel_enabled(){
        $('#btn-cancel-busy').hide();
        $('#btn-cancel-disabled').hide();
        $('#btn-cancel-enabled').show();
        $('.hide-on-submit').show();
    }

    //  BUTTON DELETE ACCT BUSY (SUBMITTING)
    function btn_delete_acct_busy(){
        $('#btn-delete-acct-busy').show();
        $('#btn-delete-acct-disabled').hide();
        $('#btn-delete-acct-enabled').hide();
        $('.hide-on-submit').hide();
    }

    // BUTTON DELETE ACCT DISABLED
    function btn_delete_acct_disabled(){
        $('#btn-delete-acct-busy').hide();
        $('#btn-delete-acct-disabled').show();
        $('#btn-delete-acct-enabled').hide();
    }

    // BUTTON DELETE ACCT ENABLED
    function btn_delete_acct_enabled(){
        $('#btn-delete-acct-busy').hide();
        $('#btn-delete-acct-disabled').hide();
        $('#btn-delete-acct-enabled').show();
        $('.hide-on-submit').show();
    }

    //  BUTTON SUBMIT BUSY (SUBMITTING)
    function btn_submit_busy(){
        $('#btn-submit-busy').show();
        $('#btn-submit-disabled').hide();
        $('#btn-submit-enabled').hide();
        $('.hide-on-submit').hide();
    }

    // BUTTON SUBMIT DISABLED
    function btn_submit_disabled(){
        $('#btn-submit-busy').hide();
        $('#btn-submit-disabled').show();
        $('#btn-submit-enabled').hide();
    }

    // BUTTON SUBMIT ENABLED
    function btn_submit_enabled(){
        $('#btn-submit-busy').hide();
        $('#btn-submit-disabled').hide();
        $('#btn-submit-enabled').show();
        $('.hide-on-submit').show();
    }

    //////////////// BELOW IS GOING TO BE RE-ORDERED SOON /////

    // BUTTON CANCEL
    $('#btn-cancel-enabled').on('click', function(){
        btn_submit_disabled();
        btn_cancel_busy();
        btn_delete_acct_disabled();

        if(confirm("Are you sure you wish to cancel changes?\nOnly CHANGES made in the form WILL BE LOST!\nAny previously saved data will remain untouched.")){
            notifyUser("Form data not submitted.");
            resetLogin();
        } else {
            btn_submit_enabled();
            btn_delete_acct_enabled();
        }
    });

    // BUTTON CLEAR ADDRESS 
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
    $('#btn-delete-acct-enabled').on('click', function(){
        btn_submit_disabled();
        btn_cancel_disabled();
        btn_delete_acct_busy();

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
        } else {
            // User cancelled delete. Restore everything back to normal.
            btn_submit_enabled();
            btn_cancel_enabled();
            btn_delete_acct_enabled();
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

    // //////////////////////    DELETE DOC BUTTON (aka "a href")  ///////////////////////////////////
    $('.link-del-doc').on('click', function(){
        var doc_id = $(this).attr('rel');
        var filename = $(this).closest('td').prev('td').prev('td').text();

        // Make the delete button inactive and put a dummy in its place
        $(this).addClass("hidden");
        $(this).next('.placeholder-del-btn').removeClass("hidden");

        if(confirm("Delete document '" + filename + "' immediately?\nClick OK to confirm.\n\nWARNING:This cannot be undone.")){
            // Purge doc from DB and from filesystem
            notifyUser("Processing deletion of document '" + filename + "'");

            $.post("delete.php", {doc_id: doc_id, action: 'delete-document'}, function(status){
                if($.trim(status) === "1"){
                    // Successfully deleted document
                    var profile_path = '<?php echo get_profile_pic_default(); ?>';
                    
                    // hide deleted row
                    $('tr#doc-' + doc_id).hide();
                    MY_GLOBALS.num_docs_shown--;
                    update_num_docs_found();

                    notifyUser("Successfully deleted document '" + filename + "'");
                } else {
                    notifyUser("Error deleting document '" + filename + "': " + status);

                    // revert hidden delete button and dummy delete button so user can try again
                    $(this).removeClass("hidden");
                    $(this).next('.placeholder-del-btn').addClass("hidden");
                }
            });
        } else {
            // revert hidden delete button and dummy delete button so user can try again
            $(this).removeClass("hidden");
            $(this).next('.placeholder-del-btn').addClass("hidden");
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


        // prep these for next return
        // (the parent containers are hidden anyway)
        btn_submit_enabled();
        btn_cancel_enabled();
        btn_delete_acct_enabled();

        $('#login-user-form').show();
        show_registered_users();
    }

    function startUploadTimer(num_docs){
        MY_GLOBALS.doc_save_interval_id = setInterval(function(){
            // On avg, we upload one doc per second
            notifyUser("Uploading and processing " + num_docs + " documents. Please wait ... " + MY_GLOBALS.num_docs_uploading);
            MY_GLOBALS.num_docs_uploading--;
            if(MY_GLOBALS.num_docs_uploading <= 0){
                // kill thyself
                notifyUserHide();
                clearInterval(MY_GLOBALS.doc_save_interval_id);
            }
        }, 1000); // 1000 = 1 sec
    }


    // ************************ SUBMIT FORM *******************************
    $('#upload-user-form').submit(function(evt){
        evt.preventDefault();

        // Get submission controls in order
        btn_submit_busy();
        btn_cancel_disabled();
        btn_delete_acct_disabled();

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
                    startUploadTimer(MY_GLOBALS.num_docs_uploading);

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
                        btn_submit_enabled(); // So user can retry
                        btn_cancel_enabled();
                        btn_delete_acct_enabled();
                    });

                } else {
                    // email address already in use
                    notifyUser("ERROR: Email address in use.");
                    
                    // Do not re-enable submit. Let the validator do it.
                    // Too risky to submit while an invalid email chosen.
                    btn_submit_disabled();
                    btn_cancel_enabled();
                    btn_delete_acct_enabled();
                }
            })
            .fail(function(){
                notifyUser("ERROR: Unable to communicate with the server.");
                btn_submit_enabled(); // So user can retry
                btn_cancel_enabled();
                btn_delete_acct_enabled();
            });
        } else {
            notifyUser(messageEnterValidEmail); 
            // Do not re-enable submit. Let the validator do it.
            // Too risky to submit while an invalid email chosen.
            btn_submit_disabled();
            btn_cancel_enabled();
            btn_delete_acct_enabled();
        }
    });

    // THANK YOU: POST-SUBMISSION
    function thankYou(){
        btn_submit_busy();
        btn_cancel_disabled();
        btn_delete_acct_disabled();
        alert("Thank you. Your information has been successfully submitted.");
    }

    // UPDATE NUM DOCS
    function update_num_docs_found(){
        $('#documents-found').text(MY_GLOBALS.num_docs_shown);
    
        if(MY_GLOBALS.num_docs_shown <= 0){
            // unhide default placeholder row if all docs deleted
            $('tr#empty-documents-row').show();
        } else {
            $('#empty-documents-row').hide();
        }
    }

    // ******************************** VALIDATIONS ******************************************

    // VALIDATION: disabled-submit due to bad email or already in use
    $('input#disabled-submit').on('click', function(){
        notifyUser("Unable to submit: bad email address or already in use.");
        submit_disabled();
    });

    // VALIDATE EMAIL not in use
    // NOTE: Although we disable submission from the beginning,
    // we continue to explicitly disable whenever we determine we 
    // have a bad email as a matter of security in case
    // another asynchronous task has momentarily enabled the submit button.
    // Better safe (and overly verbose) than sorry.
    $('input#email').keyup(function(){
        btn_submit_disabled(); // We do this here to avoid race condition
        var email = $(this).val();
        var cust_id = $('#cust_id').val();

        if(is_valid_email(email)){
            notifyUser(""); // Clear any past transgressions
            $.post("validate.php", {validate: 'email', email: email, cust_id: cust_id}, function(status){
                if($.trim(status) === "1"){
                    // email is available
                    btn_submit_enabled();
                } else {
                    // email address already in use
                    notifyUser("ERROR: Email address in use.");
                    btn_submit_disabled();
                }
            })
            .fail(function(){
                notifyUser("ERROR: Unable to communicate with the server.");
                btn_submit_enabled(); // Let user retry
            });
        } else {
            notifyUser(messageEnterValidEmail);
            btn_submit_disabled(); // Sorry, bad email. User needs to correct before submitting allowed.
        }
    });

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


    // ******************************** END: VALIDATIONS ******************************************

});