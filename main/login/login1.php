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

// Initialize status variables
$login_success = false;
$login_error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Login form submitted
        $email = $_POST['email_login'];
        $loginPassword = $_POST['password_login'];

        // SQL query to fetch user details by email
        $sql = "SELECT * FROM `registration` WHERE `Email` = '$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            // Fetch the user record
            $row = mysqli_fetch_assoc($result);
            $verify_password = password_verify($loginPassword, $row['password']);
            // Verify the password
            if ($verify_password) {
                $login_success = true;
                // After successful login
                session_start();
                $_SESSION['userEmail'] = $userEmail; // Where $userEmail is the email from the login process
                header("Location: ../dashboards/user.html"); // Redirect to dashboard on successful login
                exit();
            } else {
                $login_error = true;
            }
        } else {
            $login_error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Management</title>
    <link rel="stylesheet" href="stylesx.css">
</head>
<body>
    <div class="auth-container">
        <!-- Login Form -->
        <div class="form-container login-form-container">
            <form class="login-form" action="login1.php" method="post">
                <h2>Login</h2>
                <div class="input-group">
                    <label for="login-username">Email</label>
                    <input type="email" id="login-email" name="email_login" required>
                </div>
                <div class="input-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password_login" required>
                </div>
                <button type="submit" name="login">Login</button>
                <p>Don't have an account? <a href="register.html">Register here</a></p>
            </form>
            <?php if ($login_success): ?>
                <p>Login successful! Redirecting...</p>
            <?php endif; ?>

            <?php if ($login_error): ?>
                <p>Invalid email or password. Please try again.</p>
            <?php endif; ?>

        </div>
    </div>
    <script src="login.js"></script>
</body>
</html>
