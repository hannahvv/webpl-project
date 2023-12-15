<?php
session_start();
include 'db_connection.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get POST data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$user_name = $_POST['user_name'];
$user_password = $_POST['user_password'];
error_log("Entered/Captured Data: $first_name, $last_name, $email, $user_name, $user_password");

// Array to hold any error messages
$errors = []; 

// 1. Check if fields are empty
if(empty($first_name) || empty($last_name) || empty($email) || empty($user_name) || empty($user_password)) {
    $errors[] = "All fields are required.";
}

// 2. Email validation
if(!preg_match("/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/", $email)) {
    $errors[] = "Please enter a valid email address.";
} else {
    // Check if email already exists
    $email_check_query = "SELECT * FROM users WHERE Email='$email';"; // Note: Changed 'Users' to 'users'
    $email_result = pg_query($dbHandle, $email_check_query);
    if($email_result && pg_num_rows($email_result) > 0) {
        $errors[] = "Email already exists.";
    }
}

// 3. Username validation
$username_check_query = "SELECT * FROM users WHERE Username='$user_name';"; // Note: Changed 'Users' to 'users'
$username_result = pg_query($dbHandle, $username_check_query);
if($username_result && pg_num_rows($username_result) > 0) {
    $errors[] = "Username already exists.";
}

// 4. Password strength (example: ensuring password length)
if(strlen($user_password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
}

if(count($errors) == 0) { 
    // Hash password
    $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

    // Insert into database
    $query = "INSERT INTO users (FirstName, LastName, Email, Username, Password, DOB, graduating_year) VALUES ('$first_name', '$last_name', '$email', '$user_name', '$hashed_password', null, null);"; // Note: Changed 'Users' to 'users'

    $result = pg_query($dbHandle, $query);

    if($result) {
        // Fetch the user ID
        $user_id_query = "SELECT userid FROM users WHERE username='$user_name';";
        $user_id_result = pg_query($dbHandle, $user_id_query);
        if($user_id_result) {
            $userRow = pg_fetch_assoc($user_id_result);
            $_SESSION['user_id'] = $userRow['userid'];
        } 
        else {
            header("Location: register.php?error=There was an error. Please try again.");
            exit;
        }

        // Set session variables
        $_SESSION['user_name'] = $user_name;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        
        // Redirect to the index page with success message
        header("Location: front_controller.php?page=index?message=Registration successful!");
        exit;
    } 
    else {
        // // Print out the exact error message for debugging
        // echo "Database insertion error: " . pg_last_error($dbHandle);
        // // error_log("Database insertion error: " . pg_last_error($dbHandle));
        header("Location: register.php?error=There was an error. Please try again.");
        exit;
    }
}
else {
    // Redirect to the registration page with validation errors
    $errorMessage = urlencode(implode('<br>', $errors));
    header("Location: register.php?error=" . $errorMessage);
    exit;
}
?>