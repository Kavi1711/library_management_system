<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include('access.php');

// Handle form submission for issuing a book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['issue'])) {
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];

    // Check if the book is available
    $book_stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ? AND status = 'available'");
    $book_stmt->bind_param("i", $book_id);
    $book_stmt->execute();
    $book_result = $book_stmt->get_result();

    // Check if the member exists
    $member_stmt = $conn->prepare("SELECT * FROM members WHERE member_id = ?");
    $member_stmt->bind_param("i", $member_id);
    $member_stmt->execute();
    $member_result = $member_stmt->get_result();

    if ($book_result->num_rows > 0 && $member_result->num_rows > 0) {
        $book = $book_result->fetch_assoc();
        $member = $member_result->fetch_assoc();

        // Issue the book
        $issue_date = date('Y-m-d');
        $insert_stmt = $conn->prepare("INSERT INTO transactions (book_id, member_id, issue_date) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iis", $book_id, $member_id, $issue_date);
        $insert_stmt->execute();

        // Update the book status to 'issued'
        $update_stmt = $conn->prepare("UPDATE books SET status = 'issued' WHERE book_id = ?");
        $update_stmt->bind_param("i", $book_id);
        $update_stmt->execute();

        $success_message = "Book '{$book['title']}' issued to '{$member['name']}' successfully!";
    } else {
        $error_message = "Invalid member ID or book ID. Please check and try again.";
    }
}

// Fetch all members
$members_stmt = $conn->prepare("SELECT * FROM members");
$members_stmt->execute();
$members_result = $members_stmt->get_result();

// Fetch all books
$books_stmt = $conn->prepare("SELECT * FROM books");
$books_stmt->execute();
$books_result = $books_stmt->get_result();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            background-image: url('assets/dashboard.jpg');
        }
        .card {
            margin-bottom: 20px;
        }
        .book-image {
            max-height: 300px;
            object-fit: cover;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Issue Book</h2>

    <form action="issue_book.php" method="POST">
        <div class="mb-3">
            <label for="member_id" class="form-label">Member ID</label>
            <input type="number" class="form-control" name="member_id" required>
        </div>
        <div class="mb-3">
            <label for="book_id" class="form-label">Book ID</label>
            <input type="number" class="form-control" name="book_id" required>
        </div>
        <button type="submit" name="issue" class="btn btn-primary">Issue Book</button>
        <a href="return_book.php" class="btn btn-secondary">Return Book</a>
    </form>

    <?php if (isset($success_message)): ?>
        <div class='alert alert-success mt-3'><?php echo $success_message; ?></div>
        <div class="text-center">
            <img src="<?php echo $book['image_url']; ?>" alt="<?php echo $book['title']; ?>" class="book-image">
        </div>
    <?php elseif (isset($error_message)): ?>
        <div class='alert alert-danger mt-3'><?php echo $error_message; ?></div>
    <?php endif; ?>

    <h3 class="mt-5">All Members</h3>
    <div class="row">
        <?php while ($member = $members_result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Member ID: <?php echo $member['member_id']; ?></h5>
                        <p class="card-text">Name: <?php echo $member['name']; ?></p>
                        <p class="card-text">Email: <?php echo $member['email']; ?></p>
                        <p class="card-text">Contact: <?php echo $member['contact_number']; ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <h3 class="mt-5">All Books</h3>
    <div class="row">
        <?php while ($book = $books_result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="<?php echo $book['image_url']; ?>" class="card-img-top" alt="<?php echo $book['title']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $book['title']; ?></h5>
                        <p class="card-text">Author: <?php echo $book['author']; ?></p>
                        <p class="card-text">Status: <?php echo $book['status']; ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
