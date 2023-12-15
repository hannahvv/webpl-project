<?php
session_start();
$page = $_GET['page'] ?? 'index'; // Default to index if 'page' parameter is not set

switch ($page) {
    case 'clubPage':
        include 'clubPage.php';
        break;
    case 'events':
        include 'events.php';
        break;
    case 'reviews':
        include 'reviews.php';
        break;
    case 'profile':
        include 'profile.php';
        break;
    case 'login':
        include 'login.php';
        break;
    case 'process_login':
        include 'process_login.php';
        break;
    case 'logout':
        include 'logout.php';
        break;
    case 'register':
        include 'register.php';
        break;
    case 'edit_review':
        include 'edit_review.php'; 
        break;
    case 'delete_review':
        include 'delete_review.php';  
        break;
    case 'edit_club':
        include 'edit_club.php';
        break;
    case 'delete_club':
        include 'delete_club.php';
        break;
    case 'searchResults':
        include 'searchResults.php'; 
        break;
    default:
        include 'index.php';
}
?>
