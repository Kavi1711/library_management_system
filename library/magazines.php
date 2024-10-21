<?php
include('access.php'); // Include database connection

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT * FROM Magazines";
if ($search) {
    $query .= " WHERE Name LIKE '%$search%' OR Publisher LIKE '%$search%' OR Type LIKE '%$search%'";
}
$result = $conn->query($query);

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM Magazines WHERE id = $id");
    header('Location: magazines.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazines</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Magazines</h1>

        <form class="form-inline mb-3" method="GET" action="magazines.php">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Publisher</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['Name']) ?></td>
                        <td><?= htmlspecialchars($row['Type']) ?></td>
                        <td><?= htmlspecialchars($row['Publisher']) ?></td>
                        <td>
                            <a href="read_magazine.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Read</a>
                            <a href="<?= htmlspecialchars($row['PDF_File']) ?>" download class="btn btn-success btn-sm">Download</a>
                            <a href="magazines.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <button onclick="window.print()" class="btn btn-secondary mt-3">Print</button>
        <a href="dashboard.php" class="btn btn-info mt-3">Back to Dashboard</a>    
    </div>
</body>
</html>
