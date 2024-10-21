<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include('access.php');

// Delete member functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $delete_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Member deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Fetch members from the database
$result = $conn->query("SELECT * FROM members");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members</title>
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
        function printMembers() {
            window.print();
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">View Members</h2>

    <button class="btn btn-primary print-btn" onclick="printMembers()">Print</button>
    <a href="add_member.php" class="btn btn-primary mb-3">Add New Member</a>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Member ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Join Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['member_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo $row['join_date']; ?></td>
                        <td>
                            <a href="view_members.php?delete_id=<?php echo $row['member_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this member?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No members found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
