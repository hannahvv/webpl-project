<?php
include 'db_connection.php';

$user_name = $_POST['login_user_name'];
$user_password = $_POST['login_user_password'];

// Get user by Username
$query = "SELECT * FROM users WHERE username='$user_name';";
$result = pg_query($dbHandle, $query);
$user = pg_fetch_assoc($result);

if($user && password_verify($user_password, $user['password'])) {
    session_start();
    $_SESSION['user_id'] = $user['userid'];
    $_SESSION['user_name'] = $user['username'];
    $_SESSION['first_name'] = $user['firstname'];
    $_SESSION['last_name'] = $user['lastname'];
    // Redirect to dashboard or homepage
    header("Location: front_controller.php?page=index&message=Login+successful!");
} else {
    header("Location: front_controller.php?page=login&error=Invalid+Credentials!+Please+try+again+with+correct+Username+and/or+Password.");
}

?>
