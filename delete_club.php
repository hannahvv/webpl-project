<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: front_controller.php?page=login&error=You must be logged in to delete a club association.");
    exit;
}

$user_id = $_SESSION['user_id'];
$userclub_id = $_POST['userclub_id'] ?? null;

if (!$userclub_id) {
    header("Location: profile.php?error=Invalid request.");
    exit;
}

// Use prepared statement to avoid SQL injection
$delete_query = 'DELETE FROM userclubs WHERE userclubid = $1 AND userid = $2;';
$delete_result = pg_prepare($dbHandle, "delete_club", $delete_query);
$delete_result = pg_execute($dbHandle, "delete_club", array($userclub_id, $user_id));

if ($delete_result) {
    header("Location: profile.php?message=Club association deleted successfully.");
    exit;
} else {
    $error = pg_last_error($dbHandle);
    header("Location: profile.php?error=Error deleting club association: " . urlencode($error));
    exit;
}
?>
