<?php include_once "db.php"; ?>
<?php include_once "s3.php"; ?>
<?php

    //////////////////////////////////////////////////
    //
    // CONSTANTS
    //
    /////////////////////////////////////////////////

    $constants['date_format'] = 'Y-m-d H:i:s';
    $constants['documents_path'] = './docs';
    $constants['default_image'] = './images/default.png';
    $constants['max_addresses'] = 3;
    $constants['max_email_len'] = 255;
    $constants['profile_path'] = './profiles';
    $constants['states'] = array("AK","AL","AR","AZ","CA","CO","CT","DC","DE","FL","GA","GU","HI","IA","ID", "IL","IN","KS","KY","LA","MA","MD","ME","MH","MI","MN","MO","MS","MT","NC","ND","NE","NH","NJ","NM","NV","NY", "OH","OK","OR","PA","PR","PW","RI","SC","SD","TN","TX","UT","VA","VI","VT","WA","WI","WV","WY");

    foreach($constants as $key => $value){
        define(strtoupper($key), $value);
    }

    //////////////////////////////////////////////////
    //
    // FUNCTIONS
    //
    /////////////////////////////////////////////////

    function add_document($tmp_name, $orig_filename, $cust_id){
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
        sleep(1);
        $datetime = date(DATE_FORMAT);
        $ext = get_file_extension($orig_filename);
        $doc_internal_fullpath = DOCUMENTS_PATH . "/" . "{$cust_id}_{$datetime}.{$ext}";

        $success = save_to_storage($tmp_name, $doc_internal_fullpath)
            &&  update_db_doc_info('document', $orig_filename, $cust_id, $datetime);
        return $success;
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
        if (delete_addresses_by_cust($cust_id) 
            && delete_profile_pic($cust_id) 
            && delete_cust($cust_id)){
            $success = TRUE;
        }
        return $success;
    }

    function delete_document($doc_id){
        $doc = get_document($doc_id);
        $fullpath_doc = $doc['path'];

        debug_to_console("Inside delete_document()");
        // Determine if S3 env
        if(is_S3()){

            // TODO: implement S3 purging
            // don't forget success status
            $success = FALSE;

        } else {

            // non-S3 filesystem
            $success = delete_document_from_db($doc_id)
                // Delete profile pic
                && purge_from_storage($fullpath_doc); 
        }
        return $success;
    }

    function delete_document_from_db($doc_id){
        global $connection;
        $query = "DELETE from documents WHERE id = $doc_id";
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

            // Cust has a profile pic.
            // Remove DB profile pic reference
            // and remove image file.
            $success = update_db_doc_info('profile', '', $cust_id)
                // Delete profile pic
                && purge_from_storage($fullpath_image); 

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
    // 'FK_cust_id' = customer id (foreign key)
    function get_document($doc_id){
        global $connection;
        $query = "SELECT * FROM documents WHERE doc_id = $doc_id";
        $result = mysqli_query($connection, $query);
        if(confirmQResult($result)){
            $row = mysqli_fetch_assoc($result);
            $row['path'] = get_document_location($row['filename'], $row['datetime'], $row['FK_cust_id']);
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
        $query = "SELECT * FROM documents WHERE FK_cust_id = $cust_id";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
        $list = array();
        while($row = mysqli_fetch_assoc($result)){
            $row['path'] = get_document_location($row['filename'], $row['datetime'], $cust_id);
            array_push($list, $row);
        }
        return $list;
    }

    function get_file_extension($filename){
        // deal with non ASCII characters by setting the locale first
        setlocale(LC_ALL,'en_US.UTF-8');
        return pathinfo($filename, PATHINFO_EXTENSION);
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

    /*
        Return TRUE if Amazon S3 environment detected else FALSE;
     */
    function is_S3(){
        global $bucket;
        return !empty($bucket);
    }

    function purge_from_storage($fullpath){

        if(is_S3()){
            /// Purge from Amazon S3 bucket
            //global $s3;
            // TODO: implement purging of any filename from S3 bucket

            // Don't forget to get a $success status
        } else {
            // Purge from local filesystem
            if (unlink($fullpath)){
                $success = TRUE;
            } else {
                $success = FALSE;
                echo "ERROR: Unable to delete file '{$fullpath}'";
            };
        }
        return $success;
    }

    /* 
        PURPOSE: Re-arranges uploaded files array from PHP to a more sensible array to 
        traverse using foreach.
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

    function save_to_storage($tmp_name, $filename){
        global $bucket;
        $success = FALSE;

        if(empty($bucket)){

            // store on local server filesystem
            $success = move_uploaded_file($tmp_name, $filename);
        } else {

            // store in an Amazon S3 bucket
            try {
                global $s3;

                // NOTE: do not use 'name' for upload (that's the original filename from the user's computer)
                $upload = $s3->upload($bucket, $filename, fopen($tmp_name, 'rb'), 'public-read');
                $success = TRUE;
            } catch(Exception $e) {
                $success=FALSE;
            }
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
            &&  update_db_doc_info('profile', $fullpath_image, $cust_id);
        return $success;
    }

    // $type = 'profile' | 'document'
    // $datetime is optional (used for type 'document')
    // update_db_doc_info(string $type, string $filename, int $cust_id, [datetime]):bool
    function update_db_doc_info(){
        $type = func_get_arg(0);
        $filename = func_get_arg(1);
        $cust_id = func_get_arg(2);

        global $connection;
        
        switch($type){
            case 'profile':
                $query = "UPDATE customers SET profile = '$filename' WHERE id = $cust_id";
                break;
            case 'document':
                $datetime = func_get_arg(3);
                $query = "INSERT INTO documents(filename, datetime, FK_cust_id) VALUES ('{$filename}', '{$datetime}', $cust_id)";
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

