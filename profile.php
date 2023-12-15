<?php
include 'db_connection.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect if user not logged in 
if(!isset($_SESSION['user_name'])) {
    header("Location: front_controller.php?page=login&error=You must be logged in to view your profile.");
    exit;
}

$user_id = $_SESSION['user_id'];

// Function to calculate duration in years, months, and days
function calculateDuration($startDate) {
    $startDate = new DateTime($startDate);
    $currentDate = new DateTime();
    $interval = $startDate->diff($currentDate);
    return $interval->format('%y years, %m months, and %d days');
}

// Fetch user data from database
$query = "SELECT firstname, lastname FROM users WHERE userid='$user_id';";
$result = pg_query($dbHandle, $query);
$user = pg_fetch_assoc($result);

// Fetch user's clubs, roles, and userclubid
$query = "SELECT c.clubname, uc.role, uc.timeinclub, uc.userclubid FROM userclubs uc JOIN clubs c ON uc.clubid = c.clubid WHERE uc.userid = '$user_id';";
$result = pg_query($dbHandle, $query);
$clubs = [];
while ($row = pg_fetch_assoc($result)) {
    $clubs[] = $row;
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
                                echo '<a class="nav-link active" href="front_controller.php?page=profile">Profile</a>';
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
                <h1 class="title-review">Hi, <?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?>!</h1>
                <p class="caption-review">This is your profile. Feel free to add your info below:</p>
            </div>
             <!-- Displaying messages if available in the URL -->   
            <?php
                if(isset($_GET['error'])) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                if(isset($_GET['message'])) {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
                }
                ?>   
            <?php
            if ($clubs) {
            ?>
            <h2 class="text-center">Your Clubs</h2>
            <!-- Sort Buttons -->
            <div class="text-center mb-3">
                <button id="sort-club" class="btn btn-secondary">Sort by Club Name</button>
                <button id="sort-duration" class="btn btn-secondary">Sort by Duration</button>
            </div>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Club Name</th>
                        <th>Role</th>
                        <th>Date Joined</th>
                        <th>Duration in Club (days)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($clubs as $club) {
                        // Call the function to calculate the duration
                        $duration = calculateDuration($club['timeinclub']);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($club['clubname']); ?></td>
                        <td><?php echo htmlspecialchars($club['role']); ?></td>
                        <td><?php echo htmlspecialchars($club['timeinclub']); ?></td>
                        <td><?php echo $duration; ?></td>
                        <td>
                            <!-- Edit button form -->
                            <form method="GET" action="edit_club.php" style="display: inline;">
                                <input type="hidden" name="userclub_id" value="<?php echo $club['userclubid']; ?>">
                                <button type="submit" class="btn edit-club-btn btn-sm">Edit</button>
                            </form>
                            <!-- Delete button form -->
                            <form method="POST" action="delete_club.php" style="display: inline;">
                                <input type="hidden" name="userclub_id" value="<?php echo $club['userclubid']; ?>">
                                <button type="submit" class="btn delete-club-btn btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
            } 
            else {
            ?>
            <div class="text-center" mt-4>
                <img src="images/sadFace.png" alt="sad face"  style="max-width: 100%;">
                <p>Oh no, you aren't in any clubs! Browse clubs to find a club to join. Or use the form below to add the clubs you are in.</p>
            </div>
            <?php
            }
            ?>
            <div class="add-club-form text-center">
                <!-- Form to add to the clubs they are in -->
                <h2>Add Your Clubs</h2>      
                <form action="add_user_clubs.php" method="post" class="mx-auto" style="max-width: 500px;">
                <div class="row mb-3">
                    <div class="col">
                        <label for="club" class="form-label">Club Name</label>
                        <input type="text" class="form-control" id="club" name="clubname" required>
                    </div>
                    <div class="col">
                        <label for="rating" class="form-label">Role</label>
                        <input type="text" class="form-control" id="role" name="role" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="review-text" class="form-label">Date Joined</label>
                    <input type="date" class="form-control" id="datejoined" name="datejoined" required>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Add Club</button>
                </form>
            </div>
        </div>

        <div class="mt-4">
                <a href="fetch_profile_json.php?user_id=<?php echo $user_id; ?>" class="btn btn-primary" name="json-btn">Download Your Reviews in JSON format</a>
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
                                echo '<a class="nav-link active" href="front_controller.php?page=profile">Profile</a>';
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
            // Helper function to convert duration string into total days for comparison
            function durationInDays(duration) {
                var [years, months, days] = duration.match(/\d+/g);
                return parseInt(years) * 365 + parseInt(months) * 30 + parseInt(days);
            }

            // Helper function to sort table rows and append them back to the table
            function sortTableRows(rows, comparator) {
                rows.sort(comparator).forEach(row => {
                    document.querySelector('tbody').appendChild(row);
                });
            }

            // Event listener for sorting by club name
            document.getElementById('sort-club').addEventListener('click', function() {
                let rows = Array.from(document.querySelector('tbody').rows);
                sortTableRows(rows, (a, b) => a.cells[0].textContent.localeCompare(b.cells[0].textContent));
            });

            // Event listener for sorting by duration
            document.getElementById('sort-duration').addEventListener('click', function() {
                let rows = Array.from(document.querySelector('tbody').rows);
                sortTableRows(rows, (a, b) => durationInDays(a.cells[3].textContent) - durationInDays(b.cells[3].textContent));
            });
        </script>

    </body>
</html>