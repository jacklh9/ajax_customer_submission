<?php include_once "db.php"; ?>
<?php

    //////////////////////////////////////////////////
    //
    // CONSTANTS
    //
    /////////////////////////////////////////////////

    $constants['date_format'] = 'm-d-Y H:i:s';
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
        $destination = DOCUMENTS_PATH . "/{$cust_id}." . clean($filename);
        $date = date(DATE_FORMAT);
        if(move_uploaded_file($tmp_name, $destination)){
            $query = "INSERT INTO documents(filename, date, FK_cust_id) ";
            $query .= "VALUE('{$destination}', '{$date}', $cust_id)";
            $result = mysqli_query($connection, $query);
            confirmQResult($result);
        }
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
            // REGEX EXAMPLE: /30.(Test_PDF.pdf)/  
            // for customer# 30.
            // (.+) should match "Test_PDF.pdf"
            $pattern = '/^' . $cust_id . '\.' . '(.+)$/U';
            preg_match($pattern, basename($row['filename']), $match_list);
            $row['filename'] = $match_list[1];
            array_push($list, $row);
        }
        return $list;
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

    function update_profile_pic_filename($basename, $cust_id){
        global $connection;
        $query = "UPDATE customers SET profile = '$basename' WHERE id = $cust_id";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
    }

?>

