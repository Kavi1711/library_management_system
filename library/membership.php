<?php
session_start();
require_once 'access.php'; // Ensure to include your database connection

// Create the membership table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS membership (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    membership_plan ENUM('Gold', 'Silver', 'Platinum') NOT NULL,
    membership_end DATE NOT NULL,
    payment_mode ENUM('Credit Card', 'Debit Card', 'PayPal', 'Bank Transfer') DEFAULT NULL
)";
mysqli_query($conn, $sql);

// Registration Handler
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $membership_plan = $_POST['membership_plan'];
    $membership_end = date('Y-m-d', strtotime('+1 year')); // Valid for 1 year

    $sql = "INSERT INTO membership (username, password, membership_plan, membership_end) VALUES ('$username', '$password', '$membership_plan', '$membership_end')";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Registration successful!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
}

// Login Handler
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM membership WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['membership_end'] = $user['membership_end'];
        $_SESSION['membership_plan'] = $user['membership_plan']; // Store membership plan
        $_SESSION['user_id'] = $user['user_id']; // Store user ID for renewal
        header('Location: membership.php'); // Redirect to the same page to display dashboard
        exit();
    } else {
        $_SESSION['error'] = "Invalid credentials.";
    }
}

// Renew Membership Handler
if (isset($_POST['renew'])) {
    $username = $_SESSION['username'];
    $payment_mode = $_POST['payment_mode'];
    $new_membership_end = date('Y-m-d', strtotime('+1 year')); // Renew for another year

    // Update membership
    $sql = "UPDATE membership SET membership_end='$new_membership_end', payment_mode='$payment_mode' WHERE user_id='".$_SESSION['user_id']."'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Membership renewed successfully! Valid until: $new_membership_end";
        $_SESSION['membership_end'] = $new_membership_end; // Update session variable
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Membership Management</title>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Membership Management</h2>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); endif; ?>
    
    <?php if (!$is_logged_in): ?>
        <!-- Registration Form -->
        <h3>Register</h3>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label for="membership_plan">Membership Plan:</label>
                <select name="membership_plan" class="form-control" required>
                    <option value="Gold">Gold</option>
                    <option value="Silver">Silver</option>
                    <option value="Platinum">Platinum</option>
                </select>
            </div>
            <button type="submit" name="register" class="btn btn-primary">Register</button>
        </form>

        <!-- Login Form -->
        <h3 class="mt-4">Login</h3>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
        </form>
    <?php else: ?>
        <!-- Dashboard -->
        <h3>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
        <p>Your membership plan: <?= htmlspecialchars($_SESSION['membership_plan']) ?? 'Not set' ?></p>
        <p>Your membership is valid until: <?= htmlspecialchars($_SESSION['membership_end']) ?? 'Not Set' ?></p>
        
        <!-- Renew Membership Form -->
        <h3>Renew Membership</h3>
        <form method="POST">
            <div class="form-group">
                <label for="payment_mode">Payment Mode:</label>
                <select name="payment_mode" class="form-control" required>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Debit Card">Debit Card</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>
            <button type="submit" name="renew" class="btn btn-warning">Renew Membership</button>
        </form>

        <form method="POST" action="logout.php" class="mt-4">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>

        <!-- Back to Dashboard Button -->
        <a href="dashboard.php" class="btn btn-info mt-3">Back to Dashboard</a>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
