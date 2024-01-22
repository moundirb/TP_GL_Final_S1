<?php
session_start();
include 'db.php';

if (isset($_POST['update_account'])) {
    $userId = $_SESSION['user'];

    $email = trim($_POST['email']);
    $oldPassword = trim($_POST['password']);
    $newPassword = $_POST['2password'];
    $verifyPassword = $_POST['3password'];

    // Retrieve old email and password from the database
    $sqlSelect = "SELECT u_email, u_pass FROM user WHERE Id_u = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param('i', $userId);
    $stmtSelect->execute();
    $stmtSelect->bind_result($oldEmail, $oldPass);
    $stmtSelect->fetch();

    // Close the first statement and its result set
    $stmtSelect->close();
    
    // Check if old email and password match the ones in the database
    if ($oldEmail == $email && $oldPassword == $oldPass) {

        // Check if new password matches the verified password
        if ($newPassword == $verifyPassword) {

            // Update the user's email and password
            $sqlUpdate = "UPDATE user SET u_email = ?, u_pass = ? WHERE Id_u = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param('ssi', $email, $newPassword, $userId);

            if ($stmtUpdate->execute()) {
                echo "Account updated successfully";
                header("Location: index.php");
            } else {
                echo "Error updating account: " . $stmtUpdate->error;
            }

            // Close the second statement
            $stmtUpdate->close();

        } else {
            echo "New password and verify password do not match";
        }

    } else {
        echo "Old email or password is incorrect";
    }
}

// Close the connection
$conn->close();
?>
