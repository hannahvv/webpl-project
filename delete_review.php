<?php
session_start();
include 'db_connection.php';

// Redirect if user not logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: front_controller.php?page=login&error=You must be logged in to delete a review.");
    exit;
}

$user_id = $_SESSION['user_id'];
$review_id = $_POST['review_id'] ?? null;

if (!$review_id) {
    header("Location: reviews.php?error=Invalid request.");
    exit;
}

// Fetch the review to verify ownership
$query = "SELECT * FROM Reviews WHERE reviewid='$review_id' AND userid='$user_id';";
$result = pg_query($dbHandle, $query);

if (pg_num_rows($result) != 1) {
    header("Location: reviews.php?error=You can only delete your own reviews.");
    exit;
}

// Delete the review
$delete_query = "DELETE FROM Reviews WHERE reviewid='$review_id';";
$delete_result = pg_query($dbHandle, $delete_query);

if ($delete_result) {
    header("Location: reviews.php?message=Review deleted successfully.");
} else {
    header("Location: reviews.php?error=Error deleting review.");
}
?>