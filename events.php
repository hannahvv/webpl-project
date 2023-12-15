<?php
include('db_connection.php');

// Fetch all events from the database
$query = "SELECT * FROM events";
$result = pg_query($dbHandle, $query);

$events = array();
while ($row = pg_fetch_assoc($result)) {
    $events[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="A comprehensive review system for UVA clubs">
        <meta name="keywords" content="UVA, clubs, reviews, ratings, events">
        <meta name="author" content="Kathryn Chadwick and Hannah Vaccaro">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <script src="https://cdn.jsdelivr.net/npm/less"></script>

        <link rel="stylesheet/less" type="text/css" href="styles/custom.less">
        <link rel="stylesheet" href="styles/main.css">

        <title>Hoos Review</title>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function(){
                $('#sort-date').click(function(){
                    // Convert events data to a JavaScript array
                    let events = <?php echo json_encode($events); ?>;
                    
                    // Sort events by date using an arrow function
                    events.sort((a, b) => new Date(a.eventdate) - new Date(b.eventdate));

                    // Clear existing events
                    $('.row').empty();

                    // Append sorted events
                    events.forEach(event => {
                        $('.row').append(`
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <img class="card-img-top" src="${event.eventimage}" alt="${event.eventname}">
                                    <div class="card-body">
                                        <h3 class="card-title">${event.eventname}</h3>
                                        <p class="card-text">${event.eventdescription}</p>
                                        <p><strong>Hosted by:</strong> ${event.eventclubhost}</p>
                                        <p><strong>Date:</strong> ${event.eventdate}</p>
                                        <p><strong>Location:</strong> ${event.eventlocation}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                });
            });
        </script>
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
                            <a class="nav-link active" href="front_controller.php?page=events">Events</a>
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
                <h1>Upcoming Events at UVA</h1>
                <p>Discover upcoming events at UVA and join the community in various activities.</p>
                <button id="sort-date" class="btn btn-primary">Sort by Date</button>
                <div class="row">
                    <?php foreach ($events as $event): ?>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <img class="card-img-top" src="<?php echo $event['eventimage']; ?>" alt="<?php echo $event['eventname']; ?>">
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo $event['eventname']; ?></h3>
                                    <p class="card-text"><?php echo $event['eventdescription']; ?></p>
                                    <p><strong>Hosted by:</strong> <?php echo $event['eventclubhost']; ?></p>
                                    <p><strong>Date:</strong> <?php echo $event['eventdate']; ?></p>
                                    <p><strong>Location:</strong> <?php echo $event['eventlocation']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>                    
                </div>
            </section>
        </div>

        <footer class="mt-5 text-white p-4">
            <div class="container">
                <nav>
                    <ul class="nav justify-content-center nav-pills">
                        <li class="nav-item">
                            <a class="nav-link" href="front_controller.php?page=index">Browse Clubs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="front_controller.php?page=events">Events</a>
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