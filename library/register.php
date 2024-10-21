<?php
include 'access.php';  // Include database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Securely hash the password

    // Check if the user already exists
    $check_sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        // Insert new user into the database
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            header('Location: login.php?message=User added successfully!');
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Library Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Background Image */
        body {
            background-image: url('assets/2 login.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
        }

        /* Animation for form card */
        .card {
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Navbar style */
        .navbar {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .navbar-brand {
            color: white !important;
        }

        .navbar a:hover {
            background-color: #007bff;
            color: white !important;
        }

        /* Mouse effect for inputs */
        .form-control:hover {
            border-color: #007bff;
            box-shadow: 0 0 10px #007bff;
        }

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Library Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Back to Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Registration Form -->
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark text-light">
                <div class="card-header text-center">
                    <h3>Register New User</h3>
                </div>
                <div class="card-body">
                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" id="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" required>
                        </div>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Mouse interaction to animate form fields
    document.querySelectorAll('.form-control').forEach(function (input) {
        input.addEventListener('mouseover', function () {
            input.style.transition = 'border-color 0.3s ease, box-shadow 0.3s ease';
            input.style.borderColor = '#007bff';
            input.style.boxShadow = '0 0 10px #007bff';
        });
        input.addEventListener('mouseleave', function () {
            input.style.borderColor = '';
            input.style.boxShadow = '';
        });
    });
</script>

</body>
</html>
