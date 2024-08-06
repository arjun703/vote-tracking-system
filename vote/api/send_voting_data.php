<?php

session_start();

if(!isset($_SESSION['id'])){
    die(json_encode([
        "is_logged_in" => false
    ]));
}

$userID = $_SESSION['id'];

$jsonString = file_get_contents('./../../settings.json');

// Decode the JSON string into a PHP associative array
$jsonSettings = json_decode($jsonString, true);

include './../../credentials.php';

$dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT);

$results = [];

foreach ($jsonSettings['voting_websites'] as $voting_website) {
    
    $votingWebsiteHandle = $voting_website['handle'];

    $query = "SELECT user_id, vote_source_website, TIMESTAMPDIFF(SECOND, last_voted_at, NOW()) as seconds_elapsed 
        FROM user_votes
    WHERE user_id = '$userID' AND vote_source_website = '$votingWebsiteHandle' ORDER BY seconds_elapsed DESC LIMIT 1";

    $result = mysqli_query($dbc, $query);

    if(mysqli_num_rows($result) === 1){

        $row = mysqli_fetch_assoc($result);

        if(isset($row['vote_source_website'])){
            $voteSourceWebsite =  $row['vote_source_website'];
            $results[$voteSourceWebsite] = $row;
        }

    }

}

mysqli_close($dbc);

foreach ($jsonSettings['voting_websites'] as &$voting_website) {
    if(isset($results[$voting_website['handle']])){
        $voting_website['seconds_elapsed'] = (int) $results[$voting_website['handle']]['seconds_elapsed'];
    }else{
        $voting_website['seconds_elapsed'] = -1;
    }
}

echo json_encode([
    "is_logged_in" => true,
    "id" => $userID,
    "settings" => $jsonSettings
]);

// the user is logged in then prepare the voting link