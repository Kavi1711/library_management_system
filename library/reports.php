<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include('access.php');

// Fetch Member Report Data
$member_stmt = $conn->prepare("SELECT * FROM members");
$member_stmt->execute();
$member_result = $member_stmt->get_result();

// Fetch Book Report Data
$book_stmt = $conn->prepare("SELECT * FROM books");
$book_stmt->execute();
$book_result = $book_stmt->get_result();

// Fetch Transaction Report Data (including overdue fees)
$transaction_stmt = $conn->prepare("
    SELECT 
        t.transaction_id, 
        m.name AS member_name, 
        b.title AS book_title, 
        t.issue_date, 
        t.return_date, 
        t.status AS transaction_status, 
        of.days_overdue, 
        of.fee_amount, 
        of.status AS fee_status
    FROM transactions t
    LEFT JOIN members m ON t.member_id = m.member_id
    LEFT JOIN books b ON t.book_id = b.book_id
    LEFT JOIN overdue_fees of ON t.transaction_id = of.transaction_id
    ORDER BY t.issue_date
");
$transaction_stmt->execute();
$transaction_result = $transaction_stmt->get_result();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('assets/report.jpg');
            background-size: cover;
            background-attachment: fixed;
            color: white;
        }
        .table {
            background-color: rgba(0, 0, 0, 0.7);
        }
        .search-container {
            margin-bottom: 20px;
        }
        .print-btn {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function searchMembers() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#memberReport tbody tr');
            rows.forEach(row => {
                const memberName = row.cells[1].textContent.toLowerCase();
                row.style.display = memberName.includes(input) ? '' : 'none';
            });
        }

        function printReport() {
            window.print();
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Library Reports</h2>

    <div class="search-container">
        <input type="text" id="searchInput" onkeyup="searchMembers()" placeholder="Search for member names..." class="form-control" style="width: 300px; margin: auto;">
    </div>

    <button class="btn btn-primary print-btn" onclick="printReport()">Print Report</button>

    <!-- Member Report -->
    <h3 class="mt-5">Member Report</h3>
    <table id="memberReport" class="table table-bordered">
        <thead>
            <tr>
                <th>Member ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Join Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($member = $member_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $member['member_id']; ?></td>
                    <td><?php echo $member['name']; ?></td>
                    <td><?php echo $member['email']; ?></td>
                    <td><?php echo $member['contact_number']; ?></td>
                    <td><?php echo $member['address']; ?></td>
                    <td><?php echo $member['join_date']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Book Report -->
    <h3 class="mt-5">Book Report</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Publication Year</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($book = $book_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $book['book_id']; ?></td>
                    <td><?php echo $book['title']; ?></td>
                    <td><?php echo $book['author']; ?></td>
                    <td><?php echo $book['genre']; ?></td>
                    <td><?php echo $book['publication_year']; ?></td>
                    <td><?php echo $book['status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Transaction Report -->
    <h3 class="mt-5">Transaction Report</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Member Name</th>
                <th>Book Title</th>
                <th>Issue Date</th>
                <th>Return Date</th>
                <th>Transaction Status</th>
                <th>Days Overdue</th>
                <th>Fee Amount</th>
                <th>Fee Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($transaction = $transaction_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $transaction['transaction_id']; ?></td>
                    <td><?php echo $transaction['member_name']; ?></td>
                    <td><?php echo $transaction['book_title']; ?></td>
                    <td><?php echo $transaction['issue_date']; ?></td>
                    <td><?php echo $transaction['return_date'] ?? 'Not Returned'; ?></td>
                    <td><?php echo $transaction['transaction_status']; ?></td>
                    <td><?php echo $transaction['days_overdue'] ?? 0; ?></td>
                    <td><?php echo $transaction['fee_amount'] ?? 0; ?></td>
                    <td><?php echo $transaction['fee_status'] ?? 'N/A'; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
