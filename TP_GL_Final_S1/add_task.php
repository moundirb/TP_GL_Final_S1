<?php
session_start();
include 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize user inputs
    if (isset($_POST['projectId'])) {
        $projectId = $_POST['projectId'];
        $taskName = $_POST['tname'];
        $startDate = $_POST['tstart'];
        $duration = $_POST['tdurat'];
        $FTA = $_POST['tfta'];

        // Check if there are dependencies
        $dependencies = isset($_POST['dependencies']) ? $_POST['dependencies'] : array();
        $maxFTO = 0;

        // Loop through dependencies to find max FTO
        foreach ($dependencies as $dependency) {
            $result = $conn->query("SELECT MAX(FTO) AS maxFTO FROM tache WHERE Id_projet = $projectId AND Nom_tache IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $dependencies)) . "')");

            if ($result && $row = $result->fetch_assoc()) {
                $maxFTO = max($maxFTO, strtotime($row['maxFTO']));
            }
        }

        // Calculate DTO based on dependencies or start date
        $DTO = ($maxFTO > 0) ? date('Y-m-d', $maxFTO) : $startDate;

        // Calculate FTO
        $FTO = date('Y-m-d', strtotime($DTO . ' +' . $duration . ' days'));

        // Calculate end date (Date_ft)
        $endDate = date('Y-m-d', strtotime($startDate . ' +' . $duration . ' days'));

        // Calculate DTA based on FTA and duration
        $DTA = date('Y-m-d', strtotime($FTA . ' -' . $duration . ' days'));

        // Query to insert task into the database
        $sql = "INSERT INTO tache (Id_projet, Nom_tache, Date_dt, Date_ft, Durée, Predecesseur, DTO, FTO, DTA, FTA) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        // Check if the statement is prepared successfully
        if (!$stmt) {
            die("Error in SQL statement preparation: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("isssssssss", $projectId, $taskName, $startDate, $endDate, $duration, $predecessor, $DTO, $FTO, $DTA, $FTA);

        // Set the task dependencies (Predecesseur)
        $predecessor = isset($_POST['dependencies']) ? implode(',', array_map([$conn, 'real_escape_string'], $_POST['dependencies'])) : '';

        // Execute the statement
        if ($stmt->execute()) {
            // Close the statement
            $stmt->close();

            // Redirect to the task list or a success page
            header("location: projectDetails.php?id=$projectId");
            exit();
        } else {
            die("Error executing SQL statement: " . $stmt->error);
        }
    } else {
        die("Error: Project ID not provided.");
    }
}
?>
