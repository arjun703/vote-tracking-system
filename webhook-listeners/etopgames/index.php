<?php
 
http_response_code(200);
 
echo "listening to etopgames";
 
include './../../credintials.php';
include './../utils.php';
 
// Read the raw POST data from php://input
$inputData = file_get_contents('php://input');

error_log($inputData);

$settings = returnSettings('./../../settings.json');
     
$userid = isset($_POST['userid']) ? $_POST['userid'] : null;
$userip = isset($_POST['userip']) ? $_POST['userip'] : null;
     
if (!is_null($userid)){
    validateAndTakeAppropriateAction($userid, $ip, 'etopgames');
}

