<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_management";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Connection successful message (for testing purposes)
echo "Connected successfully";
?>
