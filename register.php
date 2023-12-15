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

    <link rel="stylesheet/less" type="text/css" href= "styles/custom.less">
    <link rel="stylesheet" href="styles/main.css">

    <title>Hoos Review</title>
</head>

<body class="sign-up-body">
    <div class="header">
        <div class="container-item">
            <h1 class="login-title"> Sign Up</h1>
            <p class="registration-text"> Already have an account? <a class="login-link" href="front_controller.php?page=login">Log In</a>
            </p>
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
            <form class="register" action="process_register.php" method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="input_name">First Name</label>
                        <input type="text" id="input_name" name="first_name" class="form-control"
                            style="border-radius: 30px; width: 100%;  max-width: none; display: inline-block;"
                            placeholder="Enter your first name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="input_lname">Last Name</label>
                        <input type="text" class="form-control" id="input_lname" name="last_name"
                            style="border-radius: 30px; width: 100%;  max-width: none; display: inline-block;"
                            placeholder="Enter your last name">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="input_email">Email</label>
                        <input type="email" class="form-control" id="input_email" name="email" placeholder="Enter your email">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="input_username">Username</label>
                        <input type="text" class="form-control" id="input_username" name="user_name"
                            style="border-radius: 20px;  width: 100%; max-width: none; display: inline-block;"
                            placeholder="Enter a username">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="input_password">Password</label>
                        <div id="password-strength"></div>
                        <input type="password" class="form-control" id="input_password" name="user_password" placeholder="Enter a password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="check" required>
                        <label class="form-check-label" for="check">
                            Agree to terms and conditions
                        </label>
                    </div>
                </div>
                <button class="btn btn-primary" id="sign-up-btn" type="submit">Sign Up</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/zxcvbn@4.4.2/dist/zxcvbn.js"></script>
    <script>
        document.getElementById('input_password').addEventListener('input', function(e) {
            var result = zxcvbn(e.target.value);
            var strengthBox = document.getElementById('password-strength');
            var score = result.score;
            var descriptions = ['Bad', 'Weak', 'Weak', 'Good', 'Great'];
            var colors = ['#ff0000', '#ffcc00', '#ffcc00', '#99cc00', '#008000'];

            strengthBox.innerText = 'Strength: ' + score + '/4 - ' + descriptions[score];
            strengthBox.style.backgroundColor = colors[score];
            strengthBox.style.border = '1px solid';
            strengthBox.style.textAlign = 'center';
            strengthBox.style.margin = '10px 0';
            strengthBox.style.padding = '5px';
        });
    </script>

</body>

</html>