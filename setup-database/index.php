<?php

include './../credentials.php';

$dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT);

$query = "
    CREATE TABLE IF NOT EXISTS user_votes (
        entry_number BIGINT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(222),
        ip_address VARCHAR(222),
        vote_source_website VARCHAR(222),
        last_credited_at TIMESTAMP
    );
";

$result = mysqli_query($dbc, $query) or die(mysqli_error($dbc));