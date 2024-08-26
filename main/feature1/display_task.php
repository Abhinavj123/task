<?php
// Database configuration
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

// Fetch tasks
$sql = "SELECT * FROM tasks";
$result = $conn->query($sql);

$tasksHtml = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $task_id = $row['id'];

        // Check if 'completed' field exists and is a boolean
        $taskCompleted = isset($row['completed']) ? (bool)$row['completed'] : false;

        // Fetch subtasks for the task
        $subtask_sql = "SELECT * FROM subtasks WHERE task_id = ?";
        $stmt = $conn->prepare($subtask_sql);
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $subtask_result = $stmt->get_result();

        $subtasksHtml = '';
        while ($subtask = $subtask_result->fetch_assoc()) {
            // Check if 'completed' field exists and is a boolean
            $subtaskCompleted = isset($subtask['completed']) ? (bool)$subtask['completed'] : false;

            $subtasksHtml .= '<li class="subtaskItem">
                <input type="checkbox" class="completeSubtask" ' . ($subtaskCompleted ? 'checked' : '') . ' data-task-id="' . $task_id . '" data-subtask-id="' . $subtask['id'] . '">
                ' . htmlspecialchars($subtask['subtask']) . '
            </li>';
        }
        
        $tasksHtml .= '<li class="taskItem">
            <div>
                <input type="checkbox" class="completeTask" ' . ($taskCompleted ? 'checked' : '') . ' data-task-id="' . $task_id . '">
                <span style="text-decoration: ' . ($taskCompleted ? 'line-through' : 'none') . ';">
                    ' . htmlspecialchars($row['title']) . ' - ' . htmlspecialchars($row['description']) . '
                </span>
                <div class="task-deadline">
                    Deadline: ' . date('Y-m-d H:i:s', strtotime($row['deadline'])) . '
                </div>
                <div class="task-priority">Priority: ' . htmlspecialchars($row['progress']) . '</div>
                <button  class="deleteTask" data-task-id="' . $task_id . '">Delete</button>
            </div>
            <ul class="subtaskList">
                ' . $subtasksHtml . '
            </ul>
        </li>';
    }
}

$conn->close();

// Output tasks as HTML
echo '<ul id="taskList">' . $tasksHtml . '</ul>';