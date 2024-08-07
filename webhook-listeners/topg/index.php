<?php

http_response_code(200);

include './../../credentials.php';
include './../utils.php';


logError("incoming request for topg");
echo "listening to topg";

// Read the raw POST data from php://input
$inputData = file_get_contents('php://input');

logError($inputData);

if(!isset($_GET['p_resp']) || !isset($_GET['ip'])){
    die("Bad request");
}

$settings = returnSettings('./../../settings.json');

// You should validate and sanitize all input.
$voterIP = $_GET["ip"] ?? null;

$valid = 1; // respone from topg means it is always valid

$pingUsername = $_GET["p_resp"] ?? null;
   
if (!is_null($pingUsername) && !is_null($voterIP) && $valid  === 1 ){
    validateAndTakeAppropriateAction($pingUsername, $voterIP, 'topg');
}else{
    logError("Either userid is null or not valid");
}