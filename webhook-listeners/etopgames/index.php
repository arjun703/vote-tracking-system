<?php

http_response_code(200);

echo "listening to etopgames";

die();

include './../../credintials.php';
include './../utils.php';

dumpPOSTdata();

$settings = returnSettings('./../../settings.json');

$userid = isset($_POST['userid']) ? $_POST['userid'] : null;
$userip = isset($_POST['userip']) ? $_POST['userip'] : null;

if (!is_null($userid)){
    validateAndTakeAppropriateAction($userid, $ip, 'etopgames');
}