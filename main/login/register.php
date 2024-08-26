<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "dblogin";

// Create a connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Retrieve and sanitize form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Prepare and execute the SQL query
    $sql = "INSERT INTO `registration` (`PhoneNumber`, `Email`, `Password`, `Name`) VALUES ('$phone', '$email', '$password', '$name')";
    
    if (mysqli_query($conn, $sql)) {
        // Redirect with success status
        header("Location: login1.php?status=success");
        exit();
    } else {
        // Redirect with error status
        echo "<alert>Registration error</alert>";
        header("Location: register.html?status=error");
        exit();
    }
}

// Close the connection
mysqli_close($conn);