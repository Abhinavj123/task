<?php
session_start(); // Start the session

// Clear all session data
session_unset();

// Destroy the session
session_destroy();

// Return a JSON response indicating success
echo json_encode(['success' => true]);
