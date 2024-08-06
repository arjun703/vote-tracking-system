<?php

session_start();

if(!isset($_SESSION['id'])){
    die(json_encode([
        "is_logged_in" => false
    ]));
}

$userID = $_SESSION['id'];

echo json_encode([
    "is_logged_in" => true,
    "id" => $userID
]);

// the user is logged in then prepare the voting link