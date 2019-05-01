<?php include_once "db.php"; ?>
<?php
    $constants['max_addresses'] = 3;
    $constants['profile_path'] = './images/profiles';
    $constants['default_image'] = 'default.png';

    foreach($constants as $key => $value){
        define(strtoupper($key), $value);
    }

    function debug_to_console( $data ) {
        $output = $data;
        if ( is_array( $output ) )
            $output = implode( ',', $output);
    
        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
    }
    
    function get_cust_id($email){
        global $connection;
        $get_cust_id_query = "SELECT id FROM customers WHERE email = '$email'";
        $result = mysqli_query($connection, $get_cust_id_query);
        confirmQResult($result);
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }

    function get_cust_profile($cust_id){
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

?>

