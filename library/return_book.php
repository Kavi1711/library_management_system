<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include('access.php');

// Initialize variables
$fee_amount = 0;
$days_overdue = 0;
$transaction_id_for_payment = null;

// Handle form submission for returning a book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return'])) {
    $transaction_id = $_POST['transaction_id'];
    $return_date = $_POST['return_date'];

    // Fetch the transaction details
    $transaction_stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
    $transaction_stmt->bind_param("i", $transaction_id);
    $transaction_stmt->execute();
    $transaction_result = $transaction_stmt->get_result();

    if ($transaction_result->num_rows > 0) {
        $transaction = $transaction_result->fetch_assoc();

        // Calculate overdue fees
        $issue_date = new DateTime($transaction['issue_date']);
        $return_date_obj = new DateTime($return_date);
        $interval = $issue_date->diff($return_date_obj);
        $days_overdue = $interval->days;

        // Update the return date and status
        $update_transaction_stmt = $conn->prepare("UPDATE transactions SET return_date = ?, status = 'returned' WHERE transaction_id = ?");
        $update_transaction_stmt->bind_param("si", $return_date, $transaction_id);
        $update_transaction_stmt->execute();

        // Update the book status to 'available'
        $update_book_stmt = $conn->prepare("UPDATE books SET status = 'available' WHERE book_id = ?");
        $update_book_stmt->bind_param("i", $transaction['book_id']);
        $update_book_stmt->execute();

        // Store transaction ID for calculating fees later
        $transaction_id_for_payment = $transaction_id;

    } else {
        $error_message = "No issued book found for the given transaction ID.";
    }
}

// Handle fee calculation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['calculate'])) {
    $transaction_id = $_POST['transaction_id'];

    // Fetch the transaction details again
    $transaction_stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
    $transaction_stmt->bind_param("i", $transaction_id);
    $transaction_stmt->execute();
    $transaction_result = $transaction_stmt->get_result();

    if ($transaction_result->num_rows > 0) {
        $transaction = $transaction_result->fetch_assoc();
        
        // Calculate overdue fees
        $issue_date = new DateTime($transaction['issue_date']);
        $return_date = new DateTime($transaction['return_date']);
        $interval = $issue_date->diff($return_date);
        $days_overdue = $interval->days;

        // Calculate fee amount
        $fee_amount = max(0, $days_overdue); // Fee is 1 rupee per day

        // Insert the overdue fee record as unpaid
        if ($fee_amount > 0) {
            $insert_fee_stmt = $conn->prepare("INSERT INTO overdue_fees (transaction_id, days_overdue, fee_amount, status) VALUES (?, ?, ?, 'unpaid')");
            $insert_fee_stmt->bind_param("iid", $transaction_id, $days_overdue, $fee_amount);
            $insert_fee_stmt->execute();
        }

        $fee_message = "Overdue fee calculated: ₹$fee_amount for $days_overdue day(s).";
    } else {
        $error_message = "No issued book found for the given transaction ID.";
    }
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay'])) {
    $transaction_id = $_POST['transaction_id'];
    $payment_method = $_POST['payment_method'];

    // Update payment status
    $update_fee_stmt = $conn->prepare("UPDATE overdue_fees SET status = 'paid' WHERE transaction_id = ?");
    $update_fee_stmt->bind_param("i", $transaction_id);
    $update_fee_stmt->execute();

    $payment_message = "Payment of ₹$fee_amount received successfully via $payment_method.";
}

// Fetch all issued books
$issued_books_stmt = $conn->prepare("SELECT t.transaction_id, t.issue_date, m.name AS member_name, b.title AS book_title
FROM transactions t
JOIN members m ON t.member_id = m.member_id
JOIN books b ON t.book_id = b.book_id
WHERE t.status = 'issued'");
$issued_books_stmt->execute();
$issued_books_result = $issued_books_stmt->get_result();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            background-image: url('assets/b5.jpg');
            background-blend-mode: lighten;
        }
    
        .card {
            background-color: #ffffff;
        }
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .table {
            background-color: #f9f9f9;
        }
        .bg-grey {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Return Book</h2>

    <?php if (isset($success_message)): ?>
        <div class='alert alert-success'><?php echo $success_message; ?></div>
        <?php if (isset($fee_message)): ?>
            <div class='alert alert-warning'><?php echo $fee_message; ?></div>
        <?php endif; ?>
    <?php elseif (isset($error_message)): ?>
        <div class='alert alert-danger'><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (isset($transaction_id_for_payment)): ?>
        <div class="card mb-4">
            <div class="card-header">
                Payment Details
            </div>
            <div class="card-body">
                <form action="return_book.php" method="POST">
                    <input type="hidden" name="transaction_id" value="<?php echo $transaction_id_for_payment; ?>">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <button type="submit" name="pay" class="btn btn-success">Pay</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <h3 class="mt-5">Issued Books</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Book Title</th>
                <th>Member Name</th>
                <th>Issue Date</th>
                <th>Return Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($issued_book = $issued_books_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $issued_book['transaction_id']; ?></td>
                    <td><?php echo $issued_book['book_title']; ?></td>
                    <td><?php echo $issued_book['member_name']; ?></td>
                    <td><?php echo $issued_book['issue_date']; ?></td>
                    <td>
                        <form action="return_book.php" method="POST" class="d-inline">
                            <input type="hidden" name="transaction_id" value="<?php echo $issued_book['transaction_id']; ?>">
                            <input type="date" name="return_date" required>
                            <button type="submit" name="return" class="btn btn-primary">Return</button>
                        </form>
                        <form action="return_book.php" method="POST" class="d-inline">
                            <input type="hidden" name="transaction_id" value="<?php echo $issued_book['transaction_id']; ?>">
                            <button type="submit" name="calculate" class="btn btn-warning">Calculate</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if (isset($payment_message)): ?>
        <div class='alert alert-success'><?php echo $payment_message; ?></div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
