<script>
    // ADD PREVIEW PIC
    function addPreviewPic(obj){
            
        // When this input is changed, the below native-JS will update the profile pic preview image
        document.getElementById('profile-pic').src = window.URL.createObjectURL(obj.files[0]);

        // We then remove the delete button because the delete button will never be able to clear the input control
        // and will only delete the preview image, but on submission the input field will put the picture back.
        // It causes confusion and is best to just hide the delete button at this juncture. 
        $('#btn-delete-profile-pic').hide();
    }
</script>