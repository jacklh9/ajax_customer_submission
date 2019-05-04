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
    $constants['default_image'] = 'default.png';
    $constants['max_addresses'] = 3;
    $constants['profile_path'] = './images/profiles';
    $constants['states'] = array("AK","AL","AR","AZ","CA","CO","CT","DC","DE","FL","GA","GU","HI","IA","ID", "IL","IN","KS","KY","LA","MA","MD","ME","MH","MI","MN","MO","MS","MT","NC","ND","NE","NH","NJ","NM","NV","NY", "OH","OK","OR","PA","PR","PW","RI","SC","SD","TN","TX","UT","VA","VI","VT","WA","WI","WV","WY");

    foreach($constants as $key => $value){
        define(strtoupper($key), $value);
    }

    //////////////////////////////////////////////////
    //
    // FUNCTIONS
    //
    /////////////////////////////////////////////////

    function add_document($tmp_name, $filename, $cust_id){
        global $connection;

        // NAMING CONVENTION: id_datetime.ext
        // We use the customer ID and datetime as the filename and NOT the user's original filename
        // for security reasons. This ensures valid, safe filenames. 
        // We also sleep for 1 second to ensure no two files have the same datetime filename.
        // NOTE: In the future we can reimplement with microseconds for faster processing.
        sleep(1);
        $datetime = date(DATE_FORMAT);
        $ext = get_file_extension($filename);
        $fullpath_doc = DOCUMENTS_PATH . "/" . "{$cust_id}_{$datetime}.{$ext}";

        $success = save_to_storage($tmp_file, $fullpath_doc, $cust_id)
            &&  update_db_doc_info('document', $fullpath_doc, $cust_id, $datetime);
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

    function delete_profile_pic($cust_id){

        $success = FALSE;
        $filename = get_profile_pic($cust_id);
        if(!empty($filename) && $filename != DEFAULT_IMAGE){

            // Remove DB profile pic reference
            global $connection;
            $query = "UPDATE customers SET profile = '' WHERE id = $cust_id";
            $result = mysqli_query($connection, $query);
            if($result){

                // Delete profile pic
                $success = unlink(PROFILE_PATH . "/" . $filename);
            }

        } else {
            // Customer didn't have a profile pic;
            // default pic was in use.
            // So effectively their profile is already deleted.
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

    function get_documents($cust_id){
        global $connection;
        $query = "SELECT * FROM documents WHERE FK_cust_id = $cust_id";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
        $list = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($list, $row);
        }
        return $list;
    }

    function get_file_extension($filename){
        // deal with non ASCII characters by setting the locale first
        setlocale(LC_ALL,'en_US.UTF-8');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
    }

    function get_profile_pic($cust_id){
        global $connection;
        $pic = DEFAULT_IMAGE;
        $query = "SELECT profile FROM customers WHERE id = '$cust_id'";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
        $row = mysqli_fetch_assoc($result);
        if(!empty($row['profile'])){
            $pic = $row['profile'];
        }
        return $pic;
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


    /* 
        PURPOSE: Re-arranges uploaded files array from PHP to a more sensible array to 
        traverse using foreach.
        SOURCE: https://www.php.net/manual/en/features.file-upload.multiple.php (phpuser at gmail dot com)
    */
    function reArrayFiles(&$file_post) {

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

    function save_to_storage($tmp_name, $filename, $cust_id){
        global $bucket;
        global $s3;
        $success = FALSE;

        if(empty($bucket)){

            // store on local server filesystem
            $success = move_uploaded_file($tmp_name, $filename);
        } else {

            // store in an Amazon S3 bucket
            try {
                // NOTE: do not use 'name' for upload (that's the original filename from the user's computer)
                $upload = $s3->upload($bucket, $filename, fopen($tmp_name, 'rb'), 'public-read');
                $success = TRUE;
            } catch(Exception $e) {
                $success=FALSE;
            }
        }
        return $success;
    }

    function update_profile_pic($timp_file, $filename, $cust_id){
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
            &&  save_to_storage($tmp_file, $fullpath_image, $cust_id)
            &&  update_db_doc_info('profile', $fullpath_image, $cust_id);
        return $success;
    }

    // $type = 'profile' | 'document'
    // $datetime is optional (used for type 'document')
    function update_db_doc_info($type, $filename, $cust_id, $datetime){
        global $connection;
        
        switch($type){
            case 'profile':
                $query = "UPDATE customers SET profile = '$filename' WHERE id = $cust_id";
                break;
            case 'document':
                $query = "INSERT INTO documents(filename, datetime, FK_cust_id) VALUES ('{$filename}', '{$datetime}', $cust_id)";
                break;
            default:
                echo "'{$type}' is not a valid 'type' parameter in update_db_info()";
                return FALSE;
        }
        $result = mysqli_query($connection, $query);
        if(!result){
            echo "Error saving file '{$filename}' to database: " . mysqli_error($connection);
        }
        return $result;
    }

?>

