<?php

http_response_code(200);

error_log("incoming request for top100arena");

error_log(json_encode($_GET));

 
if(!isset($GET['userid'])  ){
    die("Bad request");
}

error_log("userid");

echo "listening to etopgames";
 
include './../../credentials.php';
include './../utils.php';
 
// Read the raw POST data from php://input
$inputData = file_get_contents('php://input');

error_log($inputData);

$settings = returnSettings('./../../settings.json');

$userid = isset($_GET['userid']) ? $_POST['userid'] : null;
$userip = retrieveIpFromDatabase($userid);
$is_valid  = 1;
   
if (!is_null($userid) && $is_valid  === 1 ){
    validateAndTakeAppropriateAction($userid, $userip, 'top100arena');
}else{
    error_log("Either userid is null or is_valid is 0");
}