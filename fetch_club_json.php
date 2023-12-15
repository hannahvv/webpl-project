<?php
include('db_connection.php');

// Use GET to pass the club_id
$club_id = $_GET['club_id'];

// Fetch the club's information from the database
$query = "SELECT * FROM clubs WHERE clubid = $club_id;";
$result = pg_query($dbHandle, $query);
$club = pg_fetch_assoc($result);

// Return the club info as a JSON object
header('Content-Type: application/json');
echo json_encode($club);
?>
