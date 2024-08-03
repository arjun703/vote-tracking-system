<?php

function insertEntryIntoDatabase($userid, $ip, $srcWebsite){

    $dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT);

    $userid = mysqli_real_escape_string($dbc, $userid);
    $ip = mysqli_real_escape_string($dbc, $ip);
    $srcWebsite = mysqli_real_escape_string($dbc, $srcWebsite);

    $query = "
        INSERT INTO user_votes
            ( user_id, ip_address, vote_source_website, last_voted_at )
        VALUES
            ( '$userid', '$ip', '$srcWebsite', NOW() )
    ";

    mysqli_query($dbc, $query) or error_log(mysqli_error($dbc)) ;

    mysqli_close($dbc);

}

function checkIfValidBasedOnIP($ip){
    
    global $settings;

    $CYCLE_TIME_IN_HOURS = (int) $settings['cycle_time_in_hrs'];

    $dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT);

    $query = "SELECT TIMESTAMPDIFF(SECOND, user_votes.last_voted_at, NOW()) AS seconds_elapsed FROM user_votes WHERE ip ='$ip' ORDER BY last_voted_at DESC LIMIT 1 ";

    $result = mysqli_query($dbc, $query);

    if(mysqli_num_rows($result) === 0){
        // user has never voted
        mysqli_close($dbc);
        return true;
    }

    $row = mysqli_fetch_assoc($result);

    $secondsElapsedSinceLastVote = (int) $row['seconds_elapsed'];

    if($secondsElapsedSinceLastVote < ($CYCLE_IN_HOURS * 3600) ){
        // user tried to vote again within the cycle time
        mysqli_close($dbc);
        return false;
    }else{
        // okay to vote
        mysqli_close($dbc);
        return true;
    }

}


function dumpPOSTdata(){

    // Read the raw POST data from php://input
    $inputData = file_get_contents('php://input');

    // Specify the path to the text file
    $filePath = 'input_data.txt';

    // Open the file in append mode
    $fileHandle = fopen($filePath, 'a');

    // Check if the file was opened successfully
    if ($fileHandle) {
        // Write the input data to the file
        fwrite($fileHandle, $inputData . PHP_EOL);
        
        // Close the file
        fclose($fileHandle);
        
        echo "Data successfully appended to the file.";
    } else {
        echo "Failed to open the file.";
    }

}

function returnSettings($filePath){

    // Read the JSON file content into a string
    $jsonString = file_get_contents($filePath);

    // Check if file reading was successful
    if ($jsonString === false) {
        die('Error reading the file');
    }

    // Decode the JSON string into a PHP associative array
    $jsonData = json_decode($jsonString, true);

    // Check if JSON decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Error decoding JSON: ' . json_last_error_msg());
    }

}

function validateAndTakeAppropriateAction($userid, $ip, $srcWebsite){

    if(checkIfValidBasedOnIP($ip)){
        insertEntryIntoDatabase($userid, $ip, $srcWebsite);
    }
    
}