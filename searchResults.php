<?php
include('db_connection.php');

$searchQuery = $_GET['searchQuery'] ?? '';
$categoryFilter = $_GET['categoryFilter'] ?? '';
$ratingFilter = $_GET['ratingFilter'] ?? '';

// Begin building the SQL query
$query = "SELECT * FROM clubs WHERE true";

// Add conditions based on the search query
if ($searchQuery !== '') {
    $query .= " AND LOWER(clubname) LIKE LOWER('%$searchQuery%')";
}

// Add conditions based on the category filter
if ($categoryFilter !== '') {
    $query .= " AND category = '$categoryFilter'";
}

// Add conditions based on the rating filter
if ($ratingFilter !== '') {
    $query .= " AND EXISTS (SELECT 1 FROM reviews WHERE clubs.clubid = reviews.clubid AND rating >= '$ratingFilter')";
}

// Execute the query
$result = pg_query($dbHandle, $query);

// Check for errors in the query
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

// Fetch the results
$clubs = pg_fetch_all($result);

// If no clubs found, display a message
if (!$clubs) {
    echo "<p>No clubs found matching your criteria.</p>";
    return;
}

// Generate the HTML for the results
echo "<h2>Search Results</h2>";
echo "<div class='row'>";

foreach ($clubs as $club) {
    echo "
    <div class='col-md-4'>
        <div class='card mb-4'>
            <img class='card-img-top' src='{$club['clubimage']}' alt='{$club['clubname']}'>
            <div class='card-body'>
                <h3 class='card-title'><a href='front_controller.php?page=clubPage&club_id={$club['clubid']}'>{$club['clubname']}</a></h3>
                <p class='card-text'>{$club['description']}</p>
            </div>
        </div>
    </div>";
}

echo "</div>";
?>