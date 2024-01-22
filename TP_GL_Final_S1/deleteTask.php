<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    $taskId = $_GET['id'];

    // Use a prepared statement to prevent SQL injection
    $deleteSql = "DELETE FROM tache WHERE id_tache = ?";
    $deleteStmt = $conn->prepare($deleteSql);

    // Bind the parameter
    $deleteStmt->bind_param('i', $taskId);

    // Execute the statement
    $deleteResult = $deleteStmt->execute();

    // Check for errors
    if ($deleteResult === false) {
        die("Error in SQL query: " . $conn->error);
    }

    // Redirect back to the page where the task was deleted from
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    // Handle the case where 'id' is not set
    echo "Invalid request";
    exit();
}
;
