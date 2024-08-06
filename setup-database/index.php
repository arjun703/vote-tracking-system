<?php

include './../credentials.php';

$dbc = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB, $DB_PORT);

$query = "DELETE FROM  user_votes";

mysqli_query($dbc, $query);

$query = "
    CREATE TABLE IF NOT EXISTS user_votes (
        entry_number BIGINT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(222),
        ip_address VARCHAR(222),
        vote_source_website VARCHAR(222),
        last_voted_at TIMESTAMP
    );
";

$result = mysqli_query($dbc, $query) or die(mysqli_error($dbc));

$query = "
    CREATE TABLE IF NOT EXISTS users_ips (
        user_id VARCHAR(222) PRIMARY KEY,
        ip VARCHAR(222)
    );
";

$result = mysqli_query($dbc, $query) or die(mysqli_error($dbc));