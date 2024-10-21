<?php
include('access.php');

// Check if 'id' is set and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid magazine ID.");
}

$id = intval($_GET['id']);
$sql = "SELECT Name, PDF_File FROM Magazines WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $magazine = $result->fetch_assoc();
} else {
    die("Magazine not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read <?= htmlspecialchars($magazine['Name']) ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= htmlspecialchars($magazine['Name']) ?></h1>

        <?php if ($magazine['PDF_File']): ?>
            <iframe src="<?= htmlspecialchars($magazine['PDF_File']) ?>" width="100%" height="600px" frameborder="0"></iframe>
        <?php else: ?>
            <div class="alert alert-warning text-center">PDF not available for this magazine.</div>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="magazines.php" class="btn btn-secondary">Back to Magazines</a>
        </div>
    </div>
</body>
</html>
