<?php

http_response_code(200);

include './../../credentials.php';
include './../utils.php';

logError("incoming request for top100arena");

logError(json_encode($_GET));

 
if(!isset($_GET['userid'])  ){
    die("Bad request");
}

logError("userid");

echo "listening to top100arena";
 

 
// Read the raw POST data from php://input
$inputData = file_get_contents('php://input');

logError($inputData);

$settings = returnSettings('./../../settings.json');

$userid = isset($_GET['userid']) ? $_GET['userid'] : null;
$userip = retrieveIpFromDatabase($userid);
$is_valid  = 1;
   
if (!is_null($userid) && $is_valid  === 1 ){
    validateAndTakeAppropriateAction($userid, $userip, 'top100arena');
}else{
    logError("Either userid is null or is_valid is 0");
}