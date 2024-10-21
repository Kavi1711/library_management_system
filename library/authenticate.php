<?php
session_start();
include 'access.php';  // Include database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to find the user
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Start session and store username
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');  // Redirect to dashboard on successful login
            exit();
        } else {
            header('Location: login.php?error=Invalid password');
            exit();
        }
    } else {
        header('Location: login.php?error=Username not found');
        exit();
    }
}
?>
