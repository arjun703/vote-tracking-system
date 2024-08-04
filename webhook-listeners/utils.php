<?php

function insertEntryIntoDatabase($userid, $ip, $srcWebsite){

    global $DB_HOST; global $DB_USER; global $DB_PASS; global $DB; global $DB_PORT;

    $dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT) or die("Error connecting to the database");

    $userid = mysqli_real_escape_string($dbc, $userid);
    $ip = mysqli_real_escape_string($dbc, $ip);
    $srcWebsite = mysqli_real_escape_string($dbc, $srcWebsite);

    $query = "
        INSERT INTO user_votes
            ( user_id, ip_address, vote_source_website, last_voted_at )
        VALUES
            ( '$userid', '$ip', '$srcWebsite', NOW() )
    ";

    mysqli_query($dbc, $query)  or die(mysqli_error($dbc));

    echo "Succesfully inserted the data";
    error_log("Succesfully inserted the data");
    mysqli_close($dbc);

}



function checkIfValidBasedOnIP($ip){
    
    global $settings;

    global $DB_HOST; global $DB_USER; global $DB_PASS; global $DB; global $DB_PORT;

    $CYCLE_TIME_IN_HOURS =  $settings['cycle_time_in_hrs'];

    if(!is_numeric($CYCLE_TIME_IN_HOURS)){
        die("CYCLE_TIME_IN_HOURS is not numeric");
    }

    $CYCLE_TIME_IN_HOURS = (int)  $settings['cycle_time_in_hrs'];

    $dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT);

    $query = "SELECT TIMESTAMPDIFF(SECOND, user_votes.last_voted_at, NOW()) AS seconds_elapsed FROM user_votes WHERE ip_address ='$ip' ORDER BY last_voted_at DESC LIMIT 1 ";

    $result = mysqli_query($dbc, $query) or die(mysqli_error($dbc));

    if(mysqli_num_rows($result) === 0){
        // user has never voted
        echo "user has never voted";
        error_log("user has never voted");
        mysqli_close($dbc);
        return true;
    }

    $row = mysqli_fetch_assoc($result);

    if(!is_numeric($row['seconds_elapsed'])){
        error_log("seconds elapsed is not numeric");
        die("seconds elapsed is not numeric");
    }

    $secondsElapsedSinceLastVote = (int) $row['seconds_elapsed'];

    if($secondsElapsedSinceLastVote < ($CYCLE_TIME_IN_HOURS * 3600) ){
        // user tried to vote again within the cycle time
        echo "user tried to vote again withing the cycle time";
        error_log("user tried to vote again withing the cycle time");
        mysqli_close($dbc);
        return false;
    }else{
        // okay to vote
        error_log("okay to vote");
        echo "okay to vote";
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
        error_log("Data successfully appended to the file.");
        echo "Data successfully appended to the file.";
    } else {
        error_log("Data successfully appended to the file.");
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
        error_log("Error decoding JSON: " . json_last_error_msg());
        die('Error decoding JSON: ' . json_last_error_msg());
    }

    return $jsonData;

}

function validateAndTakeAppropriateAction($userid, $ip, $srcWebsite){

    if(checkIfValidBasedOnIP($ip)){
        insertEntryIntoDatabase($userid, $ip, $srcWebsite);
    }else{
        error_log("user tried to vote within 12 hours");
        die("not valid");
    }
    
}