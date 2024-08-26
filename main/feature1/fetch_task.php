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

  // Fetch tasks
  $sql = "SELECT tasks.id, tasks.title, tasks.description, tasks.deadline, tasks.progress, subtasks.subtask 
          FROM tasks 
          LEFT JOIN subtasks ON tasks.id = subtasks.task_id";
  $result = $conn->query($sql);

  $tasks = [];

  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          $task_id = $row['id'];
          if (!isset($tasks[$task_id])) {
              $tasks[$task_id] = [
                  'id' => $task_id,
                  'title' => $row['title'],
                  'description' => $row['description'],
                  'deadline' => $row['deadline'],
                  'progress' => $row['progress'],
                  'subtasks' => []
              ];
          }
          if ($row['subtask']) {
              $tasks[$task_id]['subtasks'][] = [
                  'title' => $row['subtask'],
                  'completed' => false // or true if you have completion status in the database
              ];
          }
      }
  }

  $conn->close();

  echo json_encode(array_values($tasks));