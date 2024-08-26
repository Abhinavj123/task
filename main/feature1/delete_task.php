<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "dblogin";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$taskId = $data['id'] ?? null;

if ($taskId) {
    // Prepare and execute the delete query for the task
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $taskId);

    if ($stmt->execute()) {
        // Also delete subtasks associated with the task
        $stmt = $conn->prepare("DELETE FROM subtasks WHERE task_id = ?");
        $stmt->bind_param("i", $taskId);
        $stmt->execute();

        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'message' => 'Failed to delete task.'];
    }

    $stmt->close();
} else {
    $response = ['success' => false, 'message' => 'No task ID provided.'];
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);