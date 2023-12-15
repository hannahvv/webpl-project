<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php';
if (!$dbHandle) {
    die("Database connection failed: " . pg_last_error());
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Check if the user is logged in and the review ID is set
if (!isset($_SESSION['user_name']) || !isset($_GET['review_id'])) {
    header("Location: front_controller.php?page=login&error=You must be logged in to edit a review.");
    exit;
}

$review_id = $_GET['review_id'];
$user_id = $_SESSION['user_id'];

// Fetch the review from the database
// Fetch the review and club name from the database
$query = "SELECT R.*, C.clubname FROM Reviews R LEFT JOIN clubs C ON R.clubid = C.clubid WHERE R.reviewid='$review_id' AND R.userid='$user_id';";
$result = pg_query($dbHandle, $query);

// If the review doesn't belong to the user, redirect them with an error message
if (pg_num_rows($result) != 1) {
    header("Location: reviews.php?error=You can only edit your own reviews.");
    exit;
}

$review = pg_fetch_assoc($result);

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $new_rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $new_reviewtext = filter_input(INPUT_POST, 'reviewtext', FILTER_SANITIZE_STRING);

    // Update the review in the database
    $update_query = "UPDATE Reviews SET rating='$new_rating', reviewtext='$new_reviewtext' WHERE reviewid='$review_id';";
    $update_result = pg_query($dbHandle, $update_query);

    // Redirect back to the reviews page with a success message
    if ($update_result) {
        header("Location: front_controller.php?page=reviews&message=Review updated successfully.");
        exit;        
    } else {
        echo "Error updating review: " . pg_last_error($dbHandle);
    }
}

$review_number = isset($_GET['review_number']) ? $_GET['review_number'] : 'Unknown';

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

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

    <div class="container mt-4 text-center">
        <h2>Edit Your Review</h2>
        <img src="images/editIcon.png" alt="edit" style="max-width: 100%;">
        <h3><?php echo htmlspecialchars($review['clubname']); ?> - Review #<?php echo htmlspecialchars($review_number); ?></h3>
        <form action="edit_review.php?review_id=<?php echo htmlspecialchars($review_id); ?>" method="post" class="mx-auto form-review-edit">
            <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <input type="number" min="1" max="5" class="form-control mx-auto" id="rating" name="rating" required value="<?php echo htmlspecialchars($review['rating']); ?>">
            </div>
            <div class="mb-3">
                <label for="review-text" class="form-label">Your Review</label>
                <textarea class="form-control mx-auto" id="review-text" name="reviewtext" rows="3" required><?php echo htmlspecialchars($review['reviewtext']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Review</button>
        </form>
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
</body>

</html>