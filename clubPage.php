<?php
include('db_connection.php');

// Use GET to pass the club_id
$club_id = $_GET['club_id'];

// Fetch the club's information from the database
$query = "SELECT * FROM clubs WHERE clubid = $club_id;";
$result = pg_query($dbHandle, $query);
$club = pg_fetch_assoc($result);

// Fetch the average rating and number of reviews for the club
$ratingQuery = "SELECT AVG(rating) as average_rating, COUNT(*) as review_count FROM reviews WHERE clubid = $club_id;";
$ratingResult = pg_query($dbHandle, $ratingQuery);
$ratingData = pg_fetch_assoc($ratingResult);
$average_rating = round($ratingData['average_rating'], 1);
$review_count = $ratingData['review_count'];

// Fetch the reviews for the club
$reviewsQuery = "SELECT reviews.*, userclubs.role 
                 FROM reviews 
                 JOIN userclubs ON reviews.userid = userclubs.userid 
                 WHERE reviews.clubid = $club_id;";
$reviewsResult = pg_query($dbHandle, $reviewsQuery);
$reviews = array();
while ($row = pg_fetch_assoc($reviewsResult)) {
    $reviews[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="A comprehensive review system for UVA clubs">
        <meta name="keywords" content="UVA, clubs, reviews, ratings">
        <meta name="author" content="Kathryn Chadwick and Hannah Vaccaro">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <script src="https://cdn.jsdelivr.net/npm/less"></script>

        <link rel="stylesheet/less" type="text/css" href="styles/custom.less">
        <link rel="stylesheet" href="styles/main.css">

        <title>Hoos Review</title>
    </head>
    <body>

        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid"> 
                <a class="navbar-brand" href="front_controller.php?page=index">Hoos Review</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="nav navbar-nav ms-auto nav-pills">
                        <li class="nav-item">
                            <a class="nav-link" href="front_controller.php?page=index">Browse Clubs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="front_controller.php?page=events">Events</a>
                        </li>
                        <li class="nav-item">
                            <?php
                            if(isset($_SESSION['user_name'])) {
                                echo '<a class="nav-link" href="front_controller.php?page=reviews">My Reviews</a>';
                            }
                            ?>
                        </li>
                        <li class="nav-item">
                            <?php
                            if(isset($_SESSION['user_name'])) {
                                echo '<a class="nav-link" href="front_controller.php?page=profile">Profile</a>';
                            } 
                            ?>
                        </li>
                        <li class="nav-item">
                            <?php
                            if(isset($_SESSION['user_name'])) {
                                echo '<a class="nav-link" href="front_controller.php?page=logout">Logout</a>';
                            } 
                            else {
                                echo '<a class="nav-link" href="front_controller.php?page=login">Login</a>';
                            }
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
            <h1><?php echo $club['clubname']; ?></h1>
            <span><?php echo $club['category']; ?></span>
            <?php if($review_count > 0): ?>
                <span>- Average Rating: <?php echo $average_rating; ?> stars (<?php echo $review_count; ?> reviews)</span>
            <?php else: ?>
                <span>- No reviews yet.</span>
            <?php endif; ?>
            <div class="mt-4">
                <img src="<?php echo $club['clubimage']; ?>" alt="<?php echo $club['clubname']; ?>" class="club-image">
            </div>
            <p class="mt-4"><?php echo $club['description']; ?></p>

            <!-- Sorting Buttons and Search Input -->
            <div class="text-center mt-4">
                <input type="text" id="searchReview" class="form-control mt-2 text-center mx-auto" placeholder="Search reviews...">
                <button id="sort-asc" class="btn btn-primary">Sort by Rating Ascending</button>
                <button id="sort-desc" class="btn btn-primary">Sort by Rating Descending</button>
            </div>


            <?php if($review_count > 0): ?>
                <table class="table mt-5">
                    <thead>
                        <tr>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reviews as $review): ?>
                            <tr data-rating="<?php echo $review['rating']; ?>">
                                <td><?php echo $review['role']; ?></td>
                                <td><?php echo str_repeat("★", $review['rating']) . str_repeat("☆", 5 - $review['rating']); ?></td>
                                <td><?php echo $review['reviewtext']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No reviews for this club yet. Be the first to review!</p>
            <?php endif; ?>
            <?php
                if(isset($_SESSION['user_name'])) {
                    echo '<div class="add-review-form text-center">
            <h2>Add a Review</h2>
            <form action="submit_review.php" method="post" class="mx-auto" style="max-width: 500px;">
                <div class="row mb-3">
                    <div class="col">
                        <label for="club" class="form-label">Club Name</label>
                        <input type="text" class="form-control" id="club" name="clubname" value ="'.$club['clubname'].'"readonly>
                    </div>
                    <div class="col">
                        <label for="rating" class="form-label">Rating</label>
                        <input type="number" min="1" max="5" class="form-control" id="rating" name="rating" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="review-text" class="form-label">Your Review</label>
                    <textarea class="form-control" id="review-text" name="reviewtext" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Submit Review</button>
            </form>';
                }
            ?>
        </div>
    </div>

            <!-- Adding the link to download club info in JSON format -->
            <div class="mt-4">
                <a href="fetch_club_json.php?club_id=<?php echo $club_id; ?>" class="btn btn-primary" name="json-btn">Download Club Info in JSON format</a>
            </div>
        </div>

        <footer class="mt-5 text-white p-4">
            <div class="container">
                <nav>
                    <ul class="nav justify-content-center nav-pills">
                        <li class="nav-item">
                            <a class="nav-link" href="front_controller.php?page=index">Browse Clubs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="front_controller.php?page=events">Events</a>
                        </li>
                        <li class="nav-item">
                            <?php
                            if(isset($_SESSION['user_name'])) {
                                echo '<a class="nav-link" href="front_controller.php?page=reviews">My Reviews</a>';
                            }
                            ?>
                        </li>
                        <li class="nav-item">
                            <?php
                            if(isset($_SESSION['user_name'])) {
                                echo '<a class="nav-link" href="front_controller.php?page=profile">Profile</a>';
                            } 
                            ?>
                        </li>
                        <li class="nav-item">
                            <?php
                            if(isset($_SESSION['user_name'])) {
                                echo '<a class="nav-link" href="front_controller.php?page=logout">Logout</a>';
                            } 
                            else {
                                echo '<a class="nav-link" href="front_controller.php?page=login">Login</a>';
                            }
                            ?>
                        </li>
                    </ul>
                </nav>
                <small class="d-block text-center copyright">&copy; 2023 Hoos Review</small>
            </div>
        </footer>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Sort reviews by rating
                const sortButtons = {
                    asc: document.getElementById('sort-asc'),
                    desc: document.getElementById('sort-desc')
                };

                sortButtons.asc.addEventListener('click', () => sortReviews(true));
                sortButtons.desc.addEventListener('click', () => sortReviews(false));

                function sortReviews(ascending) {
                    let rows = Array.from(document.querySelector('table tbody').rows);
                    rows.sort((a, b) => {
                        // Extract numerical rating from data-rating attribute
                        let aRating = parseInt(a.getAttribute('data-rating'));
                        let bRating = parseInt(b.getAttribute('data-rating'));
                        return ascending ? aRating - bRating : bRating - aRating;
                    });
                    rows.forEach(row => document.querySelector('table tbody').appendChild(row));
                }

                // Live search functionality
                const searchInput = document.getElementById('searchReview');
                searchInput.addEventListener('keyup', () => {
                    let searchTerm = searchInput.value.toLowerCase();
                    let tableRows = document.querySelectorAll('table tbody tr');
                    tableRows.forEach(row => {
                        let reviewText = row.cells[2].textContent.toLowerCase();
                        row.style.display = reviewText.includes(searchTerm) ? '' : 'none';
                    });
                });
            });
        </script>

    </body>
</html>