<?php
session_start();
include 'db.php';

// Get user ID from the session
$userId = $_SESSION['user'];

// Delete user account
$sqlDelete = "DELETE FROM user WHERE Id_u = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param('i', $userId);

if ($stmtDelete->execute()) {
    // Account deleted successfully
    echo "Account deleted successfully";
    
    // Optionally, you may want to redirect the user to a login page or another page after deletion.
    // header("Location: login.php");
} else {
    // Deletion failed
    echo "Error deleting account: " . $stmtDelete->error;
}

// Close prepared statement
$stmtDelete->close();

// Close the database connection
$conn->close();
header('Location:logout.php');

