<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit(); // Ensure script stops execution after redirect
}

include 'db.php'; // Assuming db.php contains the database connection code

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set
    if (isset($_POST['pname'], $_POST['pstart'])) {
        $userId = $_SESSION['user'];
        $projectName = $_POST['pname'];
        $startDate = $_POST['pstart'];
        // $numberOfTasks = $_POST['pnbr'];

        // Use a prepared statement to prevent SQL injection
        $sqlInsertProject = "INSERT INTO projet (id_u, Nom_projet, Date_dp) VALUES (?, ?, ?)";
        $stmtInsertProject = $conn->prepare($sqlInsertProject);
        $stmtInsertProject->bind_param('iss', $userId, $projectName, $startDate);

        if ($stmtInsertProject->execute()) {
            // Project inserted successfully
            $projectId = $stmtInsertProject->insert_id;

            // Redirect to mesProjects.php
            header('location: mesProjects.php');
            exit();
        }

        $stmtInsertProject->close();
        $conn->close();
    }
}

