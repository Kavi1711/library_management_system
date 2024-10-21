<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection
include('access.php');

// Simulated data (replace with DB queries)
$usersCount = 120;
$booksIssued = 45;
$booksRemaining = 105;
$borrowingTrends = [10, 20, 30, 25, 15]; // Example data
$popularGenres = ['Fiction', 'Non-Fiction', 'Science', 'History', 'Fantasy'];
$popularGenresCounts = [15, 10, 20, 8, 5];

$checkInMessage = '';
$checkOutMessage = '';

// Check-in logic
if (isset($_POST['checkin'])) {
    $name = $_POST['username'];
    $stmt = $conn->prepare("SELECT * FROM members WHERE name LIKE ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $username = $user['name'];

        $checkin_stmt = $conn->prepare("INSERT INTO attendance (username, checkin_time) VALUES (?, NOW())");
        $checkin_stmt->bind_param('s', $username);
        $checkin_stmt->execute();
        $checkInMessage = "User $username checked in successfully!";
    } else {
        $checkInMessage = "User not found!";
    }
}

// Check-out logic
if (isset($_POST['checkout'])) {
    $name = $_POST['username'];
    $stmt = $conn->prepare("SELECT * FROM members WHERE name LIKE ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $username = $user['name'];

        $checkout_stmt = $conn->prepare("UPDATE attendance SET checkout_time = NOW() WHERE username = ? AND checkout_time IS NULL ORDER BY id DESC LIMIT 1");
        $checkout_stmt->bind_param('s', $username);
        $checkout_stmt->execute();
        $checkOutMessage = "User $username checked out successfully!";
    } else {
        $checkOutMessage = "User not found!";
    }
}

// Fetch checked-in users
$checkedInUsers = [];
$checkedIn_stmt = $conn->prepare("SELECT username FROM attendance WHERE checkout_time IS NULL ORDER BY checkin_time DESC");
$checkedIn_stmt->execute();
$result = $checkedIn_stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $checkedInUsers[] = $row['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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

        .sidebar {
            background-color: rgba(0, 0, 0, 0.8);
            height: 100vh;
            padding: 20px;
        }

        .card-sidebar {
            margin-bottom: 15px;
            color: white;
        }

        .footer {
            background-color: black;
            padding: 20px;
            text-align: center;
        }

        .jumbotron {
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 20px;
        }

        /* Custom Colors for Sidebar Cards */
        .card-blue { background-color: #007bff; }
        .card-green { background-color: #28a745; }
        .card-orange { background-color: #fd7e14; }
        .card-purple { background-color: #6f42c1; }
        .card-teal { background-color: #20c997; }

        /* Remove hover animation */
        .card-sidebar:hover {
            transform: none;
            box-shadow: none;
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

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <div class="card card-sidebar card-blue">
                <div class="card-body">
                    <h5 class="card-title">Library Sections</h5>
                    <a href="view_books.php" class="btn btn-light w-100">View Books</a>
                    <a href="view_members.php" class="btn btn-light w-100">View Members</a>
                    <a href="issue_book.php" class="btn btn-light w-100">Issue Book</a>
                    <a href="return_book.php" class="btn btn-light w-100">Return Book</a>
                </div>
            </div>
            <div class="card card-sidebar card-green">
                <div class="card-body">
                    <h5 class="card-title">New Sections</h5>
                    <a href="membership.php" class="btn btn-light w-100">Membership Section</a>
                    <a href="magazines.php" class="btn btn-light w-100">Magazine Section</a>
                </div>
            </div>
            <div class="card card-sidebar card-orange">
                <div class="card-body">
                    <h5 class="card-title">Book Habits</h5>
                    <p>Track reading habits and preferences.</p>
                </div>
            </div>
            <div class="card card-sidebar card-purple">
                <div class="card-body">
                    <h5 class="card-title">Quotes</h5>
                    <p>Inspire your reading with great quotes.</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="text-center">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>Manage the library below:</p>
            </div>

            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card bg-info text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p><?php echo $usersCount; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-center">
                        <div class="card-body">
                            <h5 class="card-title">Books Issued</h5>
                            <p><?php echo $booksIssued; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-center">
                        <div class="card-body">
                            <h5 class="card-title">Books Remaining</h5>
                            <p><?php echo $booksRemaining; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h2>Check In / Check Out</h2>
                <form method="post">
                    <input type="text" name="username" placeholder="Enter Username" required><br>
                    <button type="submit" name="checkin" class="btn btn-success">Check In</button>
                    <button type="submit" name="checkout" class="btn btn-danger">Check Out</button>
                </form>
                <?php if ($checkInMessage): ?>
                    <div class="alert alert-success mt-3"><?php echo $checkInMessage; ?></div>
                <?php endif; ?>
                <?php if ($checkOutMessage): ?>
                    <div class="alert alert-warning mt-3"><?php echo $checkOutMessage; ?></div>
                <?php endif; ?>
                <h4 class="mt-4">Checked In Users:</h4>
                <ul class="list-group">
                    <?php foreach ($checkedInUsers as $user): ?>
                        <li class="list-group-item bg-dark text-light"><?php echo htmlspecialchars($user); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="jumbotron">
                        <h2>Borrowing Trends</h2>
                        <canvas id="borrowingTrendsChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="jumbotron">
                        <h2>Popular Genres</h2>
                        <canvas id="popularGenresChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2024 Library Management System. All rights reserved.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx1 = document.getElementById('borrowingTrendsChart').getContext('2d');
    const borrowingTrendsChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
            datasets: [{
                label: 'Books Borrowed',
                data: <?php echo json_encode($borrowingTrends); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                fill: true,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctx2 = document.getElementById('popularGenresChart').getContext('2d');
    const popularGenresChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($popularGenres); ?>,
            datasets: [{
                label: 'Popular Genres',
                data: <?php echo json_encode($popularGenresCounts); ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
