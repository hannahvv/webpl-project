<?php
include 'db_connection.php';
session_start();

// Redirect if user not logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: front_controller.php?page=login");
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $clubname = pg_escape_string($dbHandle, $_POST['clubname']);
    $role = pg_escape_string($dbHandle, $_POST['role']);
    $dateJoined = pg_escape_string($dbHandle, $_POST['datejoined']);
    $user_id = $_SESSION['user_id'];

    // Query to fetch the clubid using clubname
    $clubIdQuery = "SELECT clubid FROM clubs WHERE clubname = '$clubname';";
    $clubIdResult = pg_query($dbHandle, $clubIdQuery);
    if ($row = pg_fetch_assoc($clubIdResult)) {
        $club_id = $row['clubid'];
    } else {
        $errorMessage = "Invalid club name!";
        header("Location: front_controller.php?page=profile&error=" . urlencode($errorMessage));
        exit;
    }

    // SQL query to insert the user's club and role
    $query = "INSERT INTO userclubs (userid, clubid, role, timeinclub) VALUES ('$user_id', '$club_id', '$role', '$dateJoined');";

    // Execute the query
    $result = pg_query($dbHandle, $query);

    // Check if the query was successful
    if ($result) {
        header("Location: front_controller.php?page=profile&message=Club+successfully+added!");
        exit;
    } else {
        $errorMessage = "There was an error adding your club: " . pg_last_error($dbHandle);
        header("Location: front_controller.php?page=profile&error=" . urlencode($errorMessage));
        exit;
    }
}
?>
