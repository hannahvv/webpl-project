<?php
include 'db_connection.php';

// Check if username is set in the query string
if(isset($_GET['username'])) {
    $username = $_GET['username'];

    // Query to check if the username exists
    $query = "SELECT username FROM users WHERE username='$username';";
    $result = pg_query($dbHandle, $query);
    $userExists = pg_num_rows($result) > 0;

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['exists' => $userExists]);
} else {
    // If username not provided, return an error
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Username not provided']);
}
?>
