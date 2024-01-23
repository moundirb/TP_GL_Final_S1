<?php
include('session.php'); // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['semail'];
    $password = $_POST['spassword'];
    $confirmPassword = $_POST['cpassword'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "Error: Passwords do not match";
        exit;  // Stop further execution
    }

    // Perform validation if needed

    // Insert data into the database
    $sql = "INSERT INTO user (u_email, u_pass) VALUES ('$email', '$password')";

    // Check if the connection is successful before executing the query
    if ($conn) {
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
            $_SESSION['user'] = $conn->insert_id; // Assuming 'id' is your auto-increment primary key
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: Database connection failed";
    }
}

// Close the database connection
$conn->close();
header('Location:index.php')
?>
