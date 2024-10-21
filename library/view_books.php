<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include('access.php');

// Delete book functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $delete_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Book deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Fetch books from the database
$result = $conn->query("SELECT * FROM books");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('assets/dashboard.jpg'); /* Adjust the path to your background image */
            background-size: cover;
            background-attachment: fixed;
            color: white;
        }
        .table {
            background-color: rgba(0, 0, 0, 0.7);
        }
        .print-btn {
            float: right;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function printBooks() {
            window.print();
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">View Books</h2>

    <button class="btn btn-primary print-btn" onclick="printBooks()">Print</button>
    <a href="add_book.php" class="btn btn-primary mb-3">Add New Book</a>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Publication Year</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['book_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><?php echo htmlspecialchars($row['genre']); ?></td>
                        <td><?php echo $row['publication_year']; ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="view_books.php?delete_id=<?php echo $row['book_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No books found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
