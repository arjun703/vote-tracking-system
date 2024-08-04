<?php

include './../credentials.php';

$dbc= mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT) or die("Error connecting to database");
    
$query = "SELECT * FROM user_votes  ORDER BY last_voted_at DESC LIMIT 10";
    
$result = mysqli_query($dbc, $query) or die(mysqli_error($dbc));

echo "total rows returned: " . mysqli_num_rows($result) . "<BR>";

while($row = mysqli_fetch_assoc($result)){
    echo json_encode($row);
    echo "<br>--";
}   