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
<body class = "login-body">
        <div class="header">
        <div class="container-item">
        <h1 class="login-title"> Log In</h1>
        <p class = "registration-text"> Do not have an account? <a class="register-link" href="register.php">Register</a></p>
        <hr>
            <!-- Displaying messages if available in the URL -->
            <?php
                if(isset($_GET['error'])) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                if(isset($_GET['message'])) {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
                }
            ?>
        <form class="signin" action="front_controller.php?page=process_login" method="post">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="username-form">Username</label>
                    <div id="username-assistance"></div>
                    <input type="text" class="form-control" id="username-form" name="login_user_name" placeholder="Enter your username">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="password-form">Password</label>
                    <input type="password" class="form-control" id="password-form" name="login_user_password" placeholder="Enter a password">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="log-in-btn">Log In</button>
            <p class="forgot">Forgot password?</p>
        </form>
    </div>
</div>
<script>
    document.getElementById('username-form').addEventListener('blur', function(e) {
        var username = e.target.value;
        fetch('check_username.php?username=' + username)
        .then(response => response.json())
        .then(data => {
            var assistanceBox = document.getElementById('username-assistance');
            if(!data.exists) {
                assistanceBox.innerText = 'Username not found. Would you like to register? Use link above to register.';
                assistanceBox.style.display = 'block'; // Show the assistance box
            } else {
                assistanceBox.style.display = 'none'; // Hide the assistance box
            }
        });
    });
</script>
</body>
</html>