<?php
include('db_connection.php');

// Use GET to pass the club_id
$user_id = $_GET['user_id'];

// Fetch the club's information from the database
$query = "SELECT c.clubname, uc.role, uc.timeinclub, uc.userclubid FROM userclubs uc JOIN clubs c ON uc.clubid = c.clubid WHERE uc.userid = '$user_id';";
$result = pg_query($dbHandle, $query);
$reviews = pg_fetch_assoc($result);

// Return the club info as a JSON object
header('Content-Type: application/json');
echo json_encode($reviews);
?>
