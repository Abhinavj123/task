<?php
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dblogin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email from query parameters
$email = isset($_GET['email']) ? $conn->real_escape_string($_GET['email']) : '';

// Prepare and execute query
$sql = "SELECT * FROM registration WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Fetch data
$data = $result->fetch_assoc();

// Close connection
$stmt->close();
$conn->close();

// Output data as JSON
echo json_encode($data);