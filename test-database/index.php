<?php

include './../credentials.php';

$dbc= mysqli_query($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT);
    
$query = "SELECT * FROM user_votes  ORDER BY last_credited_at LIMIT 10";
    
$result = mysqli_query($dbc, $query);

echo "total rows returned: " . mysqli_num_rows($result) . "<BR>";

while($row = mysqli_fetch_assoc($result)){
    echo json_encode($row);
    echo "<br>--";
}   