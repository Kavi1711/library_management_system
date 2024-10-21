<?php
session_start();
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');  // Redirect if already logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Library Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-image: url('assets/login.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            color: white;
        }

        .container {
            max-width: 1200px;
            margin-top: 5%;
        }

        .welcome-text {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            text-align: center;
        }

        .sidebar {
            background-image: url('assets/wp9381423.jpg');
            background-size: cover;
            background-position: center;
            height: 100%;
        }

        .login-form {
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 2rem;
        }

        .btn-submit {
            background-color: #007bff;
            border: none;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .carousel-inner img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .spinner-border {
            display: none; /* Hidden by default */
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-6 d-none d-md-block sidebar"></div>

        <div class="col-md-6">
            <div class="welcome-text">Welcome to Library Management System</div>
            <div class="login-form">
                <h3 class="text-center text-light mb-4">Login</h3>

                <form action="authenticate.php" method="POST" class="needs-validation" novalidate id="loginForm">
                    <div class="mb-3">
                        <label for="username" class="form-label text-light">Username</label>
                        <input type="text" name="username" class="form-control" id="username" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-light">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary w-100 btn-submit mb-3">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Login
                    </button>
                </form>

                <form action="register.php" method="GET">
                    <button type="submit" class="btn btn-success w-100">Add User</button>
                </form>
            </div>
        </div>
    </div>

    <div id="carouselExample" class="carousel slide mt-5" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/1 login.avif" class="d-block w-100" alt="First slide">
            </div>
            <div class="carousel-item">
                <img src="assets/2 login.jpg" class="d-block w-100" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img src="assets/3 login.webp" class="d-block w-100" alt="Third slide">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Form validation
    (() => {
        const forms = document.querySelectorAll('.needs-validation');

        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    // Show loading spinner on valid submission
                    const spinner = document.querySelector('.spinner-border');
                    spinner.style.display = 'inline-block';
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Clear form inputs after submission
    document.getElementById('loginForm').addEventListener('submit', () => {
        setTimeout(() => {
            document.getElementById('loginForm').reset();
            document.querySelector('.spinner-border').style.display = 'none';
        }, 1000);
    });

    // Carousel auto-slide every 2 seconds
    var myCarousel = document.querySelector('#carouselExample');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 2000
    });
</script>

</body>
</html>
