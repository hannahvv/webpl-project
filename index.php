<?php
//https://cs4640.cs.virginia.edu/zur5ms/project/front_controller.php
include('db_connection.php');

// Function to fetch average rating for each club
function fetchAverageRatings($dbHandle) {
    $ratingsQuery = "SELECT clubid, AVG(rating) as average_rating FROM reviews GROUP BY clubid";
    $ratingsResult = pg_query($dbHandle, $ratingsQuery);
    $ratings = [];
    while ($row = pg_fetch_assoc($ratingsResult)) {
        $ratings[$row['clubid']] = $row['average_rating'];
    }
    return $ratings;
}

// Fetch average ratings for all clubs
$averageRatings = fetchAverageRatings($dbHandle);

// Fetch club details and calculate average ratings
$query = "SELECT * FROM clubs";
$result = pg_query($dbHandle, $query);
$clubs = [];
while ($row = pg_fetch_assoc($result)) {
    $clubId = $row['clubid'];
    $row['average_rating'] = $averageRatings[$clubId] ?? 0; // Default to 0 if no rating
    $clubs[] = $row;
}

// Sort clubs by average rating and limit to top 6
usort($clubs, function($a, $b) {
    return $b['average_rating'] <=> $a['average_rating'];
});
$topRatedClubs = array_slice($clubs, 0, 6);

// Categories array for dropdown 
$categories = ['Academic', 'Arts', 'Culture', 'Service', 'Sports'];

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!--https://cs4640.cs.virginia.edu/zur5ms/project/front_controller.php-->
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
                            <a class="nav-link active" aria-current="page" href="front_controller.php?page=index">Browse Clubs</a>
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
            <section>
                <!-- Displaying messages if available in the URL -->
                <?php
                    if(isset($_GET['error'])) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
                    }
                    if(isset($_GET['message'])) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
                    }
                ?>
                <h1>Welcome to Hoos Review</h1>
                <p>Discover clubs at UVA, read reviews, and share your experiences to help others make informed decisions.</p>
                <img src="images/uvaRotunda.jpg" alt="UVA Rotunda"  style="max-width: 100%;">
                <div class="search justify-content-center">
                    <!-- Search and Filter Form -->
                    <form id="searchForm" method="GET" action="front_controller.php">
                        <input type="hidden" name="page" value="searchResults">
                        <div class="input-group mb-3">
                            <input type="text" name="searchQuery" class="form-control" placeholder="Search for a club...">
                            <!-- Dropdown for Ratings -->
                            <select class="form-select" name="ratingFilter">
                                <option value="">Select Rating</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4+ Stars</option>
                                <option value="3">3+ Stars</option>
                                <option value="2">2+ Stars</option>
                                <option value="1">1+ Stars</option>
                            </select>
                            <!-- Dropdown for Categories -->
                            <select class="form-select" name="categoryFilter">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-outline-secondary" type="submit" id="searchButton">Search</button>
                        </div>
                    </form>
                        <!-- Script for AJAX request and dynamic filtering -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const searchForm = document.getElementById('searchForm');
                            const searchInput = searchForm.querySelector('input[name="searchQuery"]');
                            const categoryFilter = searchForm.querySelector('select[name="categoryFilter"]');
                            const ratingFilter = searchForm.querySelector('select[name="ratingFilter"]');
                            const resultsSection = document.querySelector('.results-section'); // Add a class to the section where you want to display results

                            searchForm.addEventListener('submit', function(event) {
                                event.preventDefault(); // Prevent the form from submitting through the browser
                                const query = searchInput.value;
                                const category = categoryFilter.value;
                                const rating = ratingFilter.value;

                                // Construct the URL with query parameters
                                let url = `searchResults.php?searchQuery=${encodeURIComponent(query)}&categoryFilter=${encodeURIComponent(category)}&ratingFilter=${encodeURIComponent(rating)}`;

                                // Perform the AJAX request
                                fetch(url)
                                .then(response => response.text())
                                .then(html => {
                                    // Hide the Top Rated Clubs section
                                    document.getElementById('top-rated-clubs').style.display = 'none';

                                    // Show the search results
                                    document.getElementById('search-results').style.display = 'block';
                                    resultsSection.innerHTML = html;
                                })
                                .catch(error => {
                                    console.error('Error fetching filtered results:', error);
                                });
                            });
                        });
                    </script>                  
                </div>
            </section>

            <!-- Top Rated Clubs section -->
            <section class="mt-5" id="top-rated-clubs">
                <h2>Top Rated Clubs</h2>
                <div class="row">
                    <?php foreach ($topRatedClubs as $club): ?>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <img class="card-img-top" src="<?php echo $club['clubimage']; ?>" alt="<?php echo $club['clubname']; ?>">
                                <div class="card-body">
                                    <h3 class="card-title"><a href="front_controller.php?page=clubPage&club_id=<?php echo $club['clubid']; ?>"><?php echo $club['clubname']; ?></a></h3>
                                    <p class="card-text"><?php echo $club['description']; ?></p>
                                    <p class="card-text">Average Rating: <?php echo round($club['average_rating'], 1); ?> Stars</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Results section -->
            <section class="mt-5" id="search-results">
                <div class="results-section">
                    <!-- AJAX search results will be displayed here -->
                </div>
            </section>
        </div>

        <footer class="mt-5 text-white p-4">
            <div class="container">
                <nav>
                    <ul class="nav justify-content-center nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="front_controller.php?page=index">Browse Clubs</a>
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