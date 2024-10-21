<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include('access.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $join_date = $_POST['join_date'];

    // Prepare the INSERT statement
    $stmt = $conn->prepare("INSERT INTO members (name, email, contact_number, address, join_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $contact_number, $address, $join_date);

    // Execute the statement
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Member added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('assets/dashboard.jpg');
            background-size: cover;
            background-attachment: fixed;
            color: white;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .form-container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            margin-top: 50px;
        }

        .carousel-item img {
            width: 100%;
            height: auto;
        }

        /* Set carousel dimensions */
        #memberCarousel {
            width: 10cm;
            height: 10cm;
            margin: 0 auto;
        }

        /* Make sure images fit in the carousel */
        .carousel-item {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%; /* Match the height of the carousel */
        }

        .carousel-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover; /* Ensure images cover the space without distortion */
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Library Management</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link btn btn-primary" href="add_book.php">Add Book</a></li>
                <li class="nav-item"><a class="nav-link btn btn-success" href="add_member.php">Add Member</a></li>
                <li class="nav-item"><a class="nav-link btn btn-warning" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link btn btn-danger" href="logout.php">Logout</a></li>
            </ul>
            <form class="d-flex ms-3" method="GET" action="search_results.php">
                <input class="form-control me-2" type="search" placeholder="Search..." aria-label="Search" name="query" required>
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="form-container">
        <h2 class="text-center">Add New Member</h2>
        <?php if (isset($message)) echo $message; ?>
        <form action="add_member.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control" name="contact_number">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" name="address"></textarea>
            </div>
            <div class="mb-3">
                <label for="join_date" class="form-label">Join Date</label>
                <input type="date" class="form-control" name="join_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Member</button>
        </form>
        <a href="view_members.php" class="btn btn-secondary mt-3">View Members</a>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<!-- Carousel -->
<div id="memberCarousel" class="carousel slide mt-5" data-bs-ride="carousel" data-bs-interval="2000">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/mem3.webp" class="d-block" alt="Member 3">
        </div>
        <div class="carousel-item">
            <img src="assets/mem2.jpg" class="d-block" alt="Member 2">
        </div>
        <div class="carousel-item">
            <img src="assets/mem1.webp" class="d-block" alt="Member 1">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#memberCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#memberCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
