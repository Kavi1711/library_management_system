<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include('access.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form input
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $publication_year = $_POST['publication_year'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO books (title, author, genre, publication_year) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $author, $genre, $publication_year);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Book added successfully!</div>";
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
    <title>Add Book</title>
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
            <!-- Search Bar -->
            <form class="d-flex ms-3" method="GET" action="search_results.php">
                <input class="form-control me-2" type="search" placeholder="Search..." aria-label="Search" name="query" required>
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="form-container">
        <h2 class="text-center">Add a New Book</h2>
        <?php if (isset($message)) echo $message; ?>
        <form method="POST" action="add_book.php">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <input type="text" class="form-control" id="genre" name="genre">
            </div>
            <div class="mb-3">
                <label for="publication_year" class="form-label">Publication Year</label>
                <input type="number" class="form-control" id="publication_year" name="publication_year" min="1900" max="<?php echo date('Y'); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Book</button>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
