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



function checkIfValidBasedOnIP($ip, $srcWebsite){
    
    global $settings;

    global $DB_HOST; global $DB_USER; global $DB_PASS; global $DB; global $DB_PORT;

    $waiting_time_in_seconds_for_next_vote = 0;

    foreach ($settings['voting_websites'] as $voting_site) {
        if($voting_site['handle'] === $srcWebsite ){
            $waiting_time_in_seconds_for_next_vote = $voting_site['waiting_time_in_seconds_for_next_vote'];
        }
    }

    if($waiting_time_in_seconds_for_next_vote == 0 || !is_numeric($waiting_time_in_seconds_for_next_vote)){
        error_log("waiting time retrieved while validating is not nunreic or is0");
        die("waiting time retrieved while validating is not nunreic or is0");
    }

    $waiting_time_in_seconds_for_next_vote = (int) $waiting_time_in_seconds_for_next_vote;

    $dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT);

    $query = "SELECT TIMESTAMPDIFF(SECOND, user_votes.last_voted_at, NOW()) AS seconds_elapsed FROM user_votes WHERE 
        vote_source_website = '$srcWebsite' AND ip_address ='$ip' ORDER BY last_voted_at DESC LIMIT 1 ";

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

    if($secondsElapsedSinceLastVote < $waiting_time_in_seconds_for_next_vote ){
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

    global $settings;

    if(checkIfValidBasedOnIP($ip, $srcWebsite)){
        
        insertEntryIntoDatabase($userid, $ip, $srcWebsite);

        $creditMultipliesOnDaysOfMonth = $settings['credit_multiplier']['active_on_days_of_month'];

        $multiplier =  1;

        if(in_array(date('j'), $creditMultipliesOnDaysOfMonth)){
            $multiplier =  $settings['credit_multiplier']['multiply_by'];
        }

        $creditCountForThisSrcWebsite = 0;

        foreach ($settings['voting_websites'] as $voting_website) {
            if($voting_website['handle'] === $srcWebsite ){
               $creditCountForThisSrcWebsite = $voting_website['credit_count'] * $multiplier;
               break; 
            }
        }

        if($creditCountForThisSrcWebsite != 0){
            updateCoinCount($userid, $creditCountForThisSrcWebsite);
        }

    }else{
        error_log("user tried to vote within waiting time");
        die("not valid");
    }
    
}

function retrieveIpFromDatabase($userID){ 

    global $DB_HOST; global $DB_USER; global $DB_PASS; global $DB; global $DB_PORT;

    $dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT) or die("Error connecting to the database");

    $query = "SELECT ip FROM users_ips WHERE user_id = '$userID' ";

    $result = mysqli_query($dbc, $query) or die("Error retrieving the IP for the user");

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        if(isset($row['ip'])){
            return $row['ip'];
        }
    }

    die("No record found");
    
}

function updateCoinCount($userid, $addby){
    
    if(!is_numeric($addby)){
        error_log("addby is not numeric");
        die("addby is not numeric");
    }

    error_log("trying to add $addby coins to $userid");
    echo "trying to add $addby coins to $userid";

    global $ACC_DB_HOST; global $ACC_DB_USER; global $ACC_DB_PASS; global $ACC_DB;

    $dbc = mysqli_connect($ACC_DB_HOST, $ACC_DB_USER, $ACC_DB_PASS, $ACC_DB) or die("Error connecting to the database: " . mysqli_connect_error() );

    $query = "UPDATE users SET iksilver = iksilver + $addby  WHERE ID = '$userID' ";

    $result = mysqli_query($dbc, $query) or die("Error adding count to the user user: " . mysqli_error($dbc));
    
    mysqli_close($dbc);

    echo("coin updated successfully");

}