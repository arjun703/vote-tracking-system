<?php

session_start();

error_log("incoming request for arenatop100");

http_response_code(200);
 
if(!isset($_POST['userid']) || !isset($_POST['userip']) ||  !isset($_POST['voted']) ){
    die("Bad request");
}

error_log("userid");

echo "listening to arenatop100";
 
include './../../credentials.php';
include './../utils.php';
 
// Read the raw POST data from php://input
$inputData = file_get_contents('php://input');

error_log($inputData);

$settings = returnSettings('./../../settings.json');

$userid = isset($_POST['userid']) ? $_POST['userid'] : null;
$userip = isset($_POST['userip']) ? $_POST['userip'] : null;
$is_valid  = isset($_POST['voted'])  ? (int)$_POST['voted'] : 0;
   
if (!is_null($userid) && $is_valid  === 1 ){
    validateAndTakeAppropriateAction($userid, $userip, 'arenatop100');
}else{
    error_log("Either userid is null or is_valid is 0");
}
