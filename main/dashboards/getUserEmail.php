<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in and the email is set in session
if (isset($_SESSION['user_email'])) {
    $userEmail = $_SESSION['user_email'];
    echo json_encode(['email' => $userEmail]);
} else {
    echo json_encode(['error' => 'No user is logged in']);
}