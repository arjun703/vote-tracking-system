<?php

session_start();

include './../../credentials.php';

function saveIpToDatabase($userID, $ip){ 

    global $DB_HOST; global $DB_USER; global $DB_PASS; global $DB; global $DB_PORT;

    $dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT) or die("Error connecting to the database");

    $query = "SELECT * FROM users_ips WHERE user_id = '$userID' ";

    $result = mysqli_query($dbc, $query) or die("Error retrieving the IP for the user");

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        if($row['ip'] == $ip ){
            // do not update ip
        }else{
            $query = "UPDATE users_ips SET ip = '$ip' WHERE user_id = '$userID' ";

            $insertStatus =  mysqli_query($dbc, $query);

            if(!$insertStatus){
                error_log("error updating ip");
                echo ("error updating ip");
            }else{
                error_log("ip updated success"); 
            }
        }
    }else{
        // insert 
        $query =  "INSERT INTO users_ips(user_id, ip) VALUES('$userID', '$ip') ";

        if(mysqli_query($dbc,$query)){
            echo "ip inserted successfully";
            error_log("ip inserted successfully");
        }else{
            error_log(mysqli_error($dbc));
        }
    }

    die("No record found");
    
}

if(!isset($_SESSION['id']) || !isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADDR']) ){
    die("session does not exist or remote addr not present");
}

$ipAddress = $_SERVER['REMOTE_ADDR'];


saveIpToDatabase($_SESSION['id'], $ipAddress);