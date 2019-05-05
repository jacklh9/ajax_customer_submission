<?php include_once "db.php"; ?>
<?php include_once "s3.php"; ?>
<?php

    //////////////////////////////////////////////////
    //
    // CONSTANTS
    //
    /////////////////////////////////////////////////

    unset($constants);
    $constants['date_format'] = 'Y-m-d H:i:s';
    $constants['documents_path'] = './docs';
    $constants['default_image'] = './images/default.png';
    $constants['max_addresses'] = 3;    // Max number of addresses customer can have
    $constants['max_email_len'] = 255;  // Max length of email 
    $constants['megabyte'] = 1048576; // Bytes in a megabyte (1024^2)
    $constants['max_doc_size'] = 4194304;  // in bytes
    $constants['max_pic_size'] = 1048576;   // in bytes
    $constants['no_user_docs_found_msg'] = "No Documents Saved Online"; // What to output in table if no docs saved.
    $constants['no_user_docs_info_msg'] = "N/A"; // What to output in table if no docs info.
    $constants['profile_path'] = './profiles'; // fileserver location and S3
    $constants['sleep_between_doc_saves'] = 1;  // MUST be 1 sec at the MINIMUM 
    $constants['states'] = array("AK","AL","AR","AZ","CA","CO","CT","DC","DE","FL","GA","GU","HI","IA","ID", "IL","IN","KS","KY","LA","MA","MD","ME","MH","MI","MN","MO","MS","MT","NC","ND","NE","NH","NJ","NM","NV","NY", "OH","OK","OR","PA","PR","PW","RI","SC","SD","TN","TX","UT","VA","VI","VT","WA","WI","WV","WY");
    $constants['valid_doc_extensions'] = ['pdf'];
    $constants['valid_pic_extensions'] = ['jpg', 'jpeg', 'png', 'webp'];

    foreach($constants as $key => $value){
        define(strtoupper($key), $value);
    }

    //////////////////////////////////////////////////
    //
    // FUNCTIONS
    //
    /////////////////////////////////////////////////

    function add_document($tmp_name, $orig_filename, $size, $cust_id){
        global $connection;

        // NAMING CONVENTION: id_datetime.ext
        // We use the customer ID and datetime as the filename and NOT the user's original filename
        // for security reasons. This ensures valid, safe filenames. 
        // We also sleep for 1 second to ensure no two files have the same datetime filename.
        // The customer's original filename is saved to the database "filename" column
        // so that we can rebuild later, such as when requesting doc for viewing.
        //
        // NOTE: In the future we can reimplement using milliseconds for even faster processing
        // as long as the database datetime is setup for milliseconds as well. 
        sleep(SLEEP_BETWEEN_DOC_SAVES);
        $datetime = date(DATE_FORMAT);
        $ext = get_file_extension($orig_filename);
        $doc_internal_fullpath = DOCUMENTS_PATH . "/" . "{$cust_id}_{$datetime}.{$ext}";

        $success = save_to_storage($tmp_name, $doc_internal_fullpath)
            &&  update_db_doc_info([
                'cust_id' => "$cust_id",
                'datetime' => "$datetime",
                'filename' => "$orig_filename",
                'size' => "$size",
                'type' => 'document', 
            ]);
        return $success;
    }

    function convert_bytes_to_MB($bytes){
        return sprintf("%1.2f MB", $bytes / MEGABYTE);
    }

    function debug_to_console( $data ) {
        $output = $data;
        if ( is_array( $output ) )
            $output = implode( ',', $output);
    
        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
    }
    
    function delete_addresses_by_cust($cust_id){
        global $connection;
        $success = FALSE;
        $query = "DELETE FROM addresses WHERE FK_cust_id = $cust_id";
        $result = mysqli_query($connection, $query);
        if(!$result){
            echo "Delete failed: " . mysqli_error($connection);
        } else {
            $success = TRUE;
        }
        return $success;
    }


    function delete_cust($cust_id){
        global $connection;
        $success = FALSE;
        $query = "DELETE FROM customers WHERE id = $cust_id";
        $result = mysqli_query($connection, $query);
        if(!$result){
            echo "Delete failed: " . mysqli_error($connection);
        } else {
            $success = TRUE;
        }
        return $success;
    }

    function delete_cust_and_related($cust_id){
        $success = FALSE;
        if (delete_documents_by_cust($cust_id)
            && delete_addresses_by_cust($cust_id) 
            && delete_profile_pic($cust_id) 
            && delete_cust($cust_id)){
            $success = TRUE;
        }
        return $success;
    }

    function delete_document($doc_id){
        $doc = get_document($doc_id);
        $fullpath_doc = $doc['path'];

        // Valid for local server FS
        // or Amazon S3 storage.
        // Remove DB doc reference
        // and then physical file.
        $success = delete_document_from_db($doc_id)
            && purge_from_storage($fullpath_doc); 

        return $success;
    }

    function delete_documents_by_cust($cust_id){
        $success = TRUE;
        $docs = get_documents($cust_id);
        
        foreach($docs as $doc){
            if(!delete_document($doc['id'])){
                // Exit IMMEDIATELY upon ANY SINGLE failure to delete a doc
                // We don't want any successive successful deletes to cloud
                // the fact that we already have at least one failure.
                return FALSE;
            }
        }
        return $success;
    }

    function delete_document_from_db($doc_id){
        global $connection;
        $query = "DELETE from documents WHERE id = {$doc_id}";
        $result = mysqli_query($connection, $query);
        if(confirmQResult($result)){
            $success = TRUE;
        } else {
            $success = FALSE;
        }
        return $success;
    }

    function delete_profile_pic($cust_id){
        $fullpath_image = get_profile_pic($cust_id);
        $fullpath_default = get_profile_pic_default();

        // Determine if cust has an actual profile pic or just the default one
        if(!empty($fullpath_image) && (basename($fullpath_image) != basename($fullpath_default))){

            // Delete cust's profile pic
            // Remove image file and
            // then remove DB profile pic reference.
            $success = purge_from_storage($fullpath_image)
                && update_db_doc_info([
                    'cust_id' => "$cust_id",
                    'filename' => "", // NULL filename to clear in DB
                    'type' => 'profile'
                ]);

        } else {

            // Customer didn't have a profile pic;
            // Default pic was in use.
            // So effectively their profile pic is already deleted.
            $success = TRUE;
        }
        return $success;
    }

    function get_cust_id($email){
        global $connection;
        $query = "SELECT id FROM customers WHERE email = '$email'";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }

    // KEYS:
    // 'id' = document id
    // 'filename' = original user filename
    // 'datetime' = datetime file uploaded (and used for internal filename)
    // 'path' = internal full-path filename
    // 'tmp_url'  = temp url to object (SEE: S3.php for OBJECT_TIMEOUT value) if S3, else same as 'path'
    // 'url' = permanent url to object [NOT YET IMPLEMENTED]
    // 'FK_cust_id' = customer id (foreign key)
    function get_document($doc_id){
        global $connection;
        $query = "SELECT * FROM documents WHERE id = {$doc_id}";
        $result = mysqli_query($connection, $query);
        if(confirmQResult($result)){
            $row = mysqli_fetch_assoc($result);
            $row['path'] = get_document_location($row['filename'], $row['datetime'], $row['FK_cust_id']);
            $row['tmp_url'] = (is_S3()) ? S3_get_temp_file_url($row['path']) : $row['path'];
            return $row;
        }
    }

    // Builds the internal document path given the original filename, datetime, and customer id
    function get_document_location($orig_filename, $datetime, $cust_id){
        $ext = get_file_extension($orig_filename);
        return DOCUMENTS_PATH . "/{$cust_id}_{$datetime}.{$ext}";
    }

    // KEYS: SEE: get_document()
    // Returns array of documents owned by $cust_id
    function get_documents($cust_id){
        global $connection;
        $query = "SELECT id FROM documents WHERE FK_cust_id = $cust_id";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
        $list = array();
        while($row = mysqli_fetch_assoc($result)){
            $doc_id = $row['id'];
            $doc = get_document($doc_id);
            array_push($list, $doc);
        }
        return $list;
    }

    function get_file_extension($filename){
        // deal with non ASCII characters by setting the locale first
        setlocale(LC_ALL,'en_US.UTF-8');
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    function get_max_doc_size_in_MB(){
        return convert_bytes_to_MB(MAX_DOC_SIZE);
    }

    function get_max_pic_size_in_MB(){
        return convert_bytes_to_MB(MAX_PIC_SIZE);
    }

    function get_profile_pic($cust_id){
        global $connection;

        $query = "SELECT profile FROM customers WHERE id = '$cust_id'";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
        $row = mysqli_fetch_assoc($result);
        if(!empty($row['profile'])){
            // User's own profile image
            $pic = $row['profile'];
        } else {
            // User doesn't have one, so use default
            $pic = get_profile_pic_default();
        }
        return $pic;
    }

    function get_profile_pic_url($cust_id){
        $pic = get_profile_pic($cust_id);
        return (is_S3()) ? S3_get_temp_file_url($pic) : $pic;
    }

    function get_profile_pic_default(){
        $default_image = DEFAULT_IMAGE;
        return $default_image;
    }

    function get_registered_users(){
        global $connection;
        $query = "SELECT email FROM customers";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
        while($row = mysqli_fetch_assoc($result)){
            echo "<li><a class='email' href='javascript:void(0)'>{$row['email']}</a></li>";
        }
    }

    function has_profile_pic($cust_id){
        global $connection;
        $query = "SELECT profile FROM customers WHERE id = $cust_id";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
        $row = mysqli_fetch_assoc($result);
        return !empty($row['profile']);
    }

    function is_email_inuse_by_another($email, $cust_id){
        global $connection;
        $inuse = TRUE;

        $query = "SELECT * FROM customers WHERE email = '{$email}'";
        $result = mysqli_query($connection, $query);
        if(confirmQResult($result)){
            if(mysqli_num_rows($result) > 0){
                $row = mysqli_fetch_assoc($result);
                if($cust_id == $row['id']){
                    // Looks like this user already owns this email
                    $inuse = FALSE;
                }
            } else {
                // no records with that email
                $inuse = FALSE;
            }
        }
        return $inuse;
    }

    function is_valid_email($email){
        $success = FALSE;
        // Pretend we did email validation.
        // For this demo, let's just rely on the client-side
        // javascript. Of course we would never do this in prod.
        if(strlen($email) <= MAX_EMAIL_LEN){
            $success = TRUE;
        }
        return $success;
    }

    // Validation rules for file
    // $valid_exts = array of valid extensions (case insensitive)
    // $max_size is in bytes
    // $file assoc array with keys (name, size)
    // (SEE: re_array_files() for quick setup with array of files)
    function is_valid_file($file, $valid_exts, $max_bytes){

        // Check filesize
        if($file['size'] > $max_bytes){
            $file_size_MB = sprintf("%1.1fMB", $file['size'] / MEGABYTE);
            $max_MB = sprintf("%1.1fMB", $max_bytes / MEGABYTE);
            echo "ERROR: File '" . $file['name'] . "' with size of {$file_size_MB} exceeds max of {$max_MB} allowed.";
            return FALSE;
        }
        // Ext in valid extensions whitelist?
        $ext = get_file_extension($file['name']);
        if (!preg_grep("/^$ext$/i", $valid_exts)){
            echo "ERROR: File '" . $file['name'] 
                . "' has invalid extension of {$ext}. Must be of type: " . implode( ", ", $valid_exts);
            return FALSE;
        }
        return TRUE;
    }

    function purge_from_storage($fullpath_filename){
        $success = FALSE;

        if(is_S3()){
            // delete from Amazon S3
            $result = S3_delete_file($fullpath_filename);
            if($result){
                $success=TRUE;
            }
        } else {
            // Purge from local filesystem
            if (unlink($fullpath_filename)){
                $success = TRUE;
            }
        }
        return $success;
    }

    /* 
        PURPOSE: Re-arranges uploaded files array from PHP to a more sensible array to 
        traverse using foreach.

        EXAMPLE:

        if ($_FILES['upload']) {
            $file_ary = reArrayFiles($_FILES['ufile']);

            foreach ($file_ary as $file) {
                print 'File Name: ' . $file['name'];
                print 'File Type: ' . $file['type'];
                print 'File Size: ' . $file['size'];
            }
        }

        SOURCE: https://www.php.net/manual/en/features.file-upload.multiple.php (phpuser at gmail dot com)
    */
    function re_array_files(&$file_post) {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
    
        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
    
        return $file_ary;
    }

    function save_to_storage($tmp_name, $fullpath_filename){
        $success = FALSE;

        if(is_S3()){
            // store in an Amazon S3
            // NOTE: do not use user's original filename
            $result = S3_upload_file($tmp_name, $fullpath_filename);
            if($result){
                $success=TRUE;
            }
        } else {
            // store on local server filesystem
            $success = move_uploaded_file($tmp_name, $fullpath_filename);
        }
        return $success;
    }

    function update_profile_pic($tmp_file, $filename, $cust_id){
        $ext = get_file_extension($filename);
        
        // NAMING CONVENTION: id.ext
        $safe_name = $cust_id . "." . $ext;
        $fullpath_image = PROFILE_PATH . "/" . $safe_name;

        // Although move_uploaded_file() overwrites files,
        // we have no guarantee it's the same filename
        // because image extensions can vary; therefore,
        // we always delete the old image as a matter
        // of good housekeeping.
        $success = delete_profile_pic($cust_id)
            &&  save_to_storage($tmp_file, $fullpath_image)
            && update_db_doc_info([
                'cust_id' => "$cust_id",
                'filename' => "$fullpath_image",
                'type' => 'profile'
            ]);

        return $success;
    }

    // 'cust_id' = customer id
    // 'datetime' = required for type 'document' only
    // 'filename' = full path to filename    
    // 'size' = filesize in bytes  (document only)
    // 'type' = [ profile | document ]
    function update_db_doc_info($args){
        global $connection;
        $cust_id = $args['cust_id'];
        $filename = $args['filename'];
        $type = $args['type'];

        switch($type){
            case 'profile':
                $query = "UPDATE customers SET profile = '$filename' WHERE id = {$cust_id}";
                break;
            case 'document':
                $datetime = $args['datetime'];
                $size = $args['size'];
                $query = "INSERT INTO documents(filename, size, datetime, FK_cust_id) VALUES ('{$filename}', '{$size}', '{$datetime}', $cust_id)";
                break;
            default:
                echo "'{$type}' is not a valid 'type' parameter in update_db_info()";
                return FALSE;
        }
        $result = mysqli_query($connection, $query);
        if(!$result){
            echo "Error saving file '{$filename}' to database: " . mysqli_error($connection);
        }
        return $result;
    }

?>

