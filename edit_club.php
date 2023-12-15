<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php';
if (!$dbHandle) {
    die("Database connection failed: " . pg_last_error());
}

// Check if the user is logged in and the club association ID is set
if (!isset($_SESSION['user_name']) || !isset($_GET['userclub_id'])) {
    header("Location: front_controller.php?page=login&error=You must be logged in to edit club details.");
    exit;
}

$userclub_id = $_GET['userclub_id'];
$user_id = $_SESSION['user_id'];

// Use prepared statements for security
$select_query = 'SELECT uc.*, c.clubname FROM userclubs uc JOIN clubs c ON uc.clubid = c.clubid WHERE uc.userclubid = $1 AND uc.userid = $2;';
$select_result = pg_prepare($dbHandle, "select_club", $select_query);
$result = pg_execute($dbHandle, "select_club", array($userclub_id, $user_id));

if (pg_num_rows($result) != 1) {
    header("Location: profile.php?error=You can only edit your own club details.");
    exit;
}

$club_association = pg_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $new_timeinclub = filter_input(INPUT_POST, 'timeinclub', FILTER_SANITIZE_STRING);

    $update_query = 'UPDATE userclubs SET role = $1, timeinclub = $2 WHERE userclubid = $3;';
    $update_result = pg_prepare($dbHandle, "update_club", $update_query);
    $update_result = pg_execute($dbHandle, "update_club", array($new_role, $new_timeinclub, $userclub_id));

    if ($update_result) {
        header("Location: front_controller.php?page=profile&message=Club details updated successfully.");
        exit;
    } else {
        $error = pg_last_error($dbHandle);
        echo "Error updating club details: " . htmlspecialchars($error);
        exit;
    }
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
        <h2>Edit Club Details</h2>
        <h3><?php echo htmlspecialchars($club_association['clubname']); ?></h3>
        <form action="edit_club.php?userclub_id=<?php echo htmlspecialchars($userclub_id); ?>" method="post" class="mx-auto form-club-edit">
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <input type="text" class="form-control mx-auto" id="role" name="role" required value="<?php echo htmlspecialchars($club_association['role']); ?>">
            </div>
            <div class="mb-3">
                <label for="timeinclub" class="form-label">Time in Club</label>
                <input type="date" class="form-control mx-auto" id="timeinclub" name="timeinclub" required value="<?php echo htmlspecialchars($club_association['timeinclub']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Details</button>
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
</body>
</html>