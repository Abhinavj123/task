<?php
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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Debugging: Check what is being received
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";

        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $deadline = isset($_POST['deadline']) ? trim($_POST['deadline']) : '';
        $subtasks = isset($_POST['subtasks']) ? $_POST['subtasks'] : [];

        // Check if required fields are filled
        if (empty($title) || empty($deadline)) {
            echo "Title and Deadline are required.";
        } else {
            // Insert task
            $sql = "INSERT INTO tasks (`title`, `description`, `deadline`, `progress`) VALUES ('$title', '$description', '$deadline', 0)";
            
            if ($conn->query($sql) === TRUE) {
                $task_id = $conn->insert_id;
                foreach ($subtasks as $subtask) {
                    if (!empty($subtask)) {
                        $conn->query("INSERT INTO subtasks (task_id, subtask) VALUES ('$task_id', '$subtask')");
                    }
                }
                echo "New task created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    $conn->close();
