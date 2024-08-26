<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "dblogin";

// Create connection to MySQL server (without specifying a database)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it does not exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database '$database' created or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database for use
$conn->select_db($database);

// Create Registration table
$sql = "
CREATE TABLE IF NOT EXISTS registration (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    name VARCHAR(255),
    password VARCHAR(255),
    phoneNumber VARCHAR(15)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table registration created or already exists<br>";
} else {
    echo "Error creating registration table: " . $conn->error . "<br>";
}

// Create Tasks table with foreign key reference to registration table
$sql = "
CREATE TABLE IF NOT EXISTS tasks (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    deadline DATE,
    progress INT(3),
    userEmail VARCHAR(255),
    FOREIGN KEY (userEmail) REFERENCES registration(email) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table tasks created successfully<br>";
} else {
    echo "Error creating tasks table: " . $conn->error . "<br>";
}

// Create Subtasks table with foreign key reference to tasks table
$sql = "
CREATE TABLE IF NOT EXISTS subtasks (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id INT(6) UNSIGNED,
    subtask VARCHAR(255),
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table subtasks created successfully<br>";
} else {
    echo "Error creating subtasks table: " . $conn->error . "<br>";
}

$conn->close();
