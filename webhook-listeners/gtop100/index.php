<?php

error_log("incoming request for gtop100");

http_response_code(200);


// Read the raw POST data from php://input
$inputData = file_get_contents('php://input');

error_log($inputData);

if(!isset($_POST['pingUsername']) || !isset($_POST['voterIP']) ||  !isset($_POST['Successful']) ){
    die("Bad request");
}

echo "listening to gtop100";
 
include './../../credentials.php';
include './../utils.php';


$settings = returnSettings('./../../settings.json');

// You should validate and sanitize all input.
$voterIP = $_POST["VoterIP"] ?? null;
// 1 for error, 0 for successful
$success = abs((int)($_POST["Successful"] ?? 1));
// log reason the vote failed
$reason = $_POST["Reason"] ?? null;
// Retrieve the ping username if provided
$pingUsername = $_POST["pingUsername"] ?? null;
   
if (!is_null($pingUsername) && $success  === 0 ){
    validateAndTakeAppropriateAction($pingUsername, $voterIP, 'gtop100');
}else{
    error_log("Either userid is null or not success");
}