<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    $projectId = $_GET['id'];

    // Use a prepared statement to prevent SQL injection
    $deleteSql = "DELETE FROM projet WHERE Id_projet = ?";
    $deleteStmt = $conn->prepare($deleteSql);

    // Bind the parameter
    $deleteStmt->bind_param('i', $projectId);

    // Execute the statement
    $deleteResult = $deleteStmt->execute();

    // Check for errors
    if ($deleteResult === false) {
        die("Error in SQL query: " . $conn->error);
    }

    // Redirect back to mesProjects.php after deletion
    header('Location: mesProjects.php');
    exit();
} else {
    // Handle the case where 'id' is not set
    echo "Invalid request";
    exit();
}
?>
