<?php 

// Only get DB connection parameters once for efficiency
if(!defined('DB_PASS')){
    create_db_conn_constants();    
}

$connection = (defined('DB_PORT')) ? mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT) : mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

//test_query();

/* 
   Requires that one copies the hosting provider's mysql connection string
   to an environment variable called "DB_URL_COPY".
 */
function create_db_conn_constants(){

    // EXPECTED ENV VAR FORMAT: mysql://NEWUSER:NEWPASS@NEWHOST:3306/NEWDATABASE
    $db_conn = getenv('JAWSDB_MARIA_URL', true) ?: getenv('JAWSDB_MARIA_URL');

    if(empty($db_conn)) {

        // DEV:
        // No hosting database environment connection string found;
        // we must in development.

        ob_start();
        $db['db_host'] = 'localhost';
        $db['db_name'] = 'ctest';
        $db['db_user'] = 'ctest_user';
        $db['db_pass'] = 'HemDyZSKxCF2Uk21';

    } else {

        // PROD:
        // We're in production
        // Time to parse the db connection string for our db login parameters     
        // NOTE: Regexes created with help from: https://www.phpliveregex.com

        preg_match('/^.+:\/\/.+:.+@(.+):.+$/U', $db_conn, $match_list);
        $db['db_host'] = $match_list[1];

        preg_match('/^.+\/(.+)$/', $db_conn, $match_list);
        $db['db_name'] = $match_list[1];

        preg_match('/^.+:\/\/(.+):.+$/U', $db_conn, $match_list);
        $db['db_user'] = $match_list[1];

        preg_match('/^.+:\/\/.+:(.+)@.+$/U', $db_conn, $match_list);
        $db['db_pass'] = $match_list[1]; 

        preg_match('/^.+:(\d+)\/.+$/', $db_conn, $match_list);
        $db['db_port'] = $match_list[1];

    }

    // Turn DB vars into constants
    foreach($db as $key => $value){
        define(strtoupper($key), $value);
    }

}

function clean($string){
    global $connection;

    // trim: removes whitespace from string "edges"
    // strip_tags: removes HTML-like tags (OPTIONAL)
    return mysqli_real_escape_string($connection, trim(strip_tags($string)));
}

function confirmQResult($query_result){
    global $connection; 
    $success = FALSE;

    if(!$query_result){
        die("QUERY FAILED: " . mysqli_error($connection));
    } else {
        $success = TRUE;
    }
    return $success;
}

// Quick Test (remove in prod)
function test_query() {
    global $connection;
    $query = "SHOW TABLES";
    $result_set = mysqli_query($connection, $query);

    if($result_set) {
        echo "Connected: <br>";
        echo "<ul>";
        while($row = mysqli_fetch_row($result_set)){
            echo "<li> $row[0] </li>";
        }
        echo "</ul>";
    } else {
        echo "NOT connected <br>";
    }
}

?>
