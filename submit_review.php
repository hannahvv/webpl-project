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
    $rating = pg_escape_string($dbHandle, $_POST['rating']);
    $reviewtext = pg_escape_string($dbHandle, $_POST['reviewtext']);
    $user_id = $_SESSION['user_id'];

    // Query to fetch the clubid using clubname
    $clubIdQuery = "SELECT clubid FROM clubs WHERE clubname = '$clubname';";
    $clubIdResult = pg_query($dbHandle, $clubIdQuery);
    if ($row = pg_fetch_assoc($clubIdResult)) {
        $club_id = $row['clubid'];
    } else {
        $errorMessage = "Invalid club name!";
        header("Location: front_controller.php?page=reviews&error=" . urlencode($errorMessage));
        exit;
    }

    // SQL query to insert the new review
    $query = "INSERT INTO reviews (userid, clubid, reviewtext, rating, dateposted) VALUES ('$user_id', '$club_id', '$reviewtext', '$rating', CURRENT_DATE);";

    // Execute the query
    $result = pg_query($dbHandle, $query);

    // Check if the query was successful
    if ($result) {
        header("Location: front_controller.php?page=reviews&message=Review+submitted+successfully!");
        exit;
    } else {
        $errorMessage = "There was an error submitting your review! Please try again.";
        header("Location: front_controller.php?page=reviews&error=" . urlencode($errorMessage));
        exit;
    }
}
?>
