<?php
include 'db_connection.php';
session_start();

// Redirect if user not logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: front_controller.php?page=login&error=You must be logged in to view your reviews.");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch reviews from the database
$query = "SELECT R.*, C.clubname FROM Reviews R LEFT JOIN clubs C ON R.clubid = C.clubid WHERE R.userid='$user_id';";
$result = pg_query($dbHandle, $query);
$reviews = pg_fetch_all($result);
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
                                echo '<a class="nav-link active" href="front_controller.php?page=reviews">My Reviews</a>';
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
    <div class="container mt-4">
        <div class="review-header">
            <h1 class="title-review">Your Reviews</h1>
            <p class="caption-review">A collections of the reviews, you have submitted in the past!</p>
        </div>
        <div class="search-reviews text-center">
            <input type="text" id="searchInput" class="form-control mx-auto" placeholder="Search reviews...">
        </div>

        <!-- Displaying messages if available in the URL -->
        <?php
            if(isset($_GET['error'])) {
                echo '<div class="alert alert-danger text-centered">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            if(isset($_GET['message'])) {
                echo '<div class="alert alert-success text-centered">' . htmlspecialchars($_GET['message']) . '</div>';
            }
        ?>
        <?php
        if ($reviews) {
        ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="thead-review">
                    <tr class="text-header">
                        <th scope="col">#</th>
                        <th scope="col">Review</th>
                        <th scope="col">Rated</th>
                        <th scope="col">Submitted</th>
                        <th scope="col">Club</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($reviews as $index => $review) {
                    ?>
                    <tr>
                        <th scope="row"><?php echo $index + 1; ?></th>
                        <td><?php echo $review['reviewtext']; ?></td>
                        <td><?php echo str_repeat('★', $review['rating']); ?></td>
                        <td><?php echo $review['dateposted']; ?></td>
                        <td><?php echo $review['clubname']; ?></td>
                        <td class="actions">
                            <!-- Edit button form -->
                            <form method="GET" action="edit_review.php" style="display: inline;">
                                <input type="hidden" name="review_id" value="<?php echo $review['reviewid']; ?>">
                                <input type="hidden" name="review_number" value="<?php echo $index + 1; ?>">
                                <button type="submit" class="btn edit-review-btn btn-sm">Edit</button>
                            </form>
                            <!-- Delete button form -->
                            <form method="POST" action="delete_review.php" style="display: inline;">
                                <input type="hidden" name="review_id" value="<?php echo $review['reviewid']; ?>">
                                <button type="submit" class="btn delete-review-btn btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        } 
        else {
        ?>
        <div class="text-center mt-4">
            <img src="images/sadFace.png" alt="sad face"  style="max-width: 100%;">
            <p>You have not left a review for a club yet! Add one using the form below.</p>
        </div>
        <?php
        }
        ?>
        <!-- Add Review Form -->
        <div class="add-review-form text-center">
            <h2>Add a Review</h2>
            <form action="submit_review.php" method="post" class="mx-auto" style="max-width: 500px;">
                <div class="row mb-3">
                    <div class="col">
                        <label for="club" class="form-label">Club Name</label>
                        <input type="text" class="form-control" id="club" name="clubname" required>
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
            </form>
        </div>
    </div>

    <div class="mt-4">
                <a href="fetch_review_json.php?user_id=<?php echo $user_id; ?>" class="btn btn-primary" name="json-btn">Download Your Clubs in JSON format</a>
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
                                echo '<a class="nav-link active" href="front_controller.php?page=reviews">My Reviews</a>';
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
            const searchInput = document.getElementById('searchInput');
            
            searchInput.addEventListener('keyup', function() {
                const searchTerm = searchInput.value.toLowerCase();
                const tableRows = document.querySelectorAll('table tbody tr');
                
                tableRows.forEach(row => {
                    const reviewText = row.cells[1].textContent.toLowerCase();
                    row.style.display = reviewText.includes(searchTerm) ? '' : 'none';
                });
            });
        });
    </script>

</body>

</html>