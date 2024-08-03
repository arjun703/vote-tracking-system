<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   
<a href="https://www.etopgames.com/"><img src="https://www.etopgames.com/button.php?u=pwember&buttontype=static" alt="Best Game Servers - Top Gaming Servers 100 List | eTopGames" /></a>

<?php

http_response_code(200);

echo "listening to etopgames";

include './../../credintials.php';
include './../utils.php';

dumpPOSTdata();

$settings = returnSettings('./../../settings.json');

$userid = isset($_POST['userid']) ? $_POST['userid'] : null;
$userip = isset($_POST['userip']) ? $_POST['userip'] : null;

if (!is_null($userid)){
    validateAndTakeAppropriateAction($userid, $ip, 'etopgames');
}

?>

</body>
</html>

