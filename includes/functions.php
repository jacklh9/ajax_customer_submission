<?php include_once "db.php"; ?>
<?php
    $constants['max_addresses'] = 3;
    $constants['profile_path'] = './images/profiles';
    $constants['default_image'] = 'default.png';
    $constants['states'] = array("AK","AL","AR","AZ","CA","CO","CT","DC","DE","FL","GA","GU","HI","IA","ID", "IL","IN","KS","KY","LA","MA","MD","ME","MH","MI","MN","MO","MS","MT","NC","ND","NE","NH","NJ","NM","NV","NY", "OH","OK","OR","PA","PR","PW","RI","SC","SD","TN","TX","UT","VA","VI","VT","WA","WI","WV","WY");

    foreach($constants as $key => $value){
        define(strtoupper($key), $value);
    }

    function debug_to_console( $data ) {
        $output = $data;
        if ( is_array( $output ) )
            $output = implode( ',', $output);
    
        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
    }
    
    function delete_addresses_by_cust($id){
        global $connection;

        $query = "DELETE FROM addresses WHERE FK_cust_id = '$id'";
        $result = mysqli_query($connection, $query);
        if(!$result){
            echo "Delete failed: " . mysqli_error($connection);
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function delete_cust($id){
        global $connection;

        $query = "DELETE FROM customers WHERE id = '$id'";
        $result = mysqli_query($connection, $query);
        if(!$result){
            echo "Delete failed: " . mysqli_error($connection);
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function delete_cust_profile_pic($cust_id){
        $filename = get_cust_profile_pic($cust_id);
        if(!empty($filename) && $filename != DEFAULT_IMAGE){
            return unlink(PROFILE_PATH . "/" . $filename);
        } else {
            // Customer didn't have a profile pic;
            // default pic was in use.
            // So effectively their profile is already deleted.
            return TRUE;
        }
    }

    function get_cust_id($email){
        global $connection;
        $get_cust_id_query = "SELECT id FROM customers WHERE email = '$email'";
        $result = mysqli_query($connection, $get_cust_id_query);
        confirmQResult($result);
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }

    function get_cust_profile_pic($cust_id){
        global $connection;
        $pic = DEFAULT_IMAGE;
        $get_cust_profile_query = "SELECT profile FROM customers WHERE id = '$cust_id'";
        $result = mysqli_query($connection, $get_cust_profile_query);
        confirmQResult($result);
        $row = mysqli_fetch_assoc($result);
        if(!empty($row['profile'])){
            $pic = $row['profile'];
        }
        return $pic;
    }

    function get_registered_users(){
        global $connection;

        $get_cust_query = "SELECT email FROM customers";
        $result = mysqli_query($connection, $get_cust_query);
        confirmQResult($result);
        while($row = mysqli_fetch_assoc($result)){
            echo "<li><a class='email' href='javascript:void(0)'>{$row['email']}</a></li>";
        }
    }

    function update_profile_pic_filename($basename, $cust_id){
        global $connection;
        $query = "UPDATE customers SET profile = '$basename' WHERE id = $cust_id";
        $result = mysqli_query($connection, $query);
        confirmQResult($result);
    }

?>

