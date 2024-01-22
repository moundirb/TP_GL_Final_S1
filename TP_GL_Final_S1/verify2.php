<?php
include 'session.php';

if(isset($_POST['submit'])){
    $email = isset($_POST['lemail']) ? $_POST['lemail'] : '';
    $password = isset($_POST['lpassword']) ? $_POST['lpassword'] : '';

    try {
        // Use prepared statements to prevent SQL injection
        $sql = "SELECT Id_u FROM user WHERE u_email = ? AND u_pass = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        $stmt->bind_result($id);

        // Fetch the result
        $stmt->fetch();

        // Check if the user exists
        if($id){
            // User exists, perform login logic here
            // For example, set session variables or redirect to a dashboard
            echo "Login successful";
            $_SESSION['user'] = $id;
            header("Location: index.php");
            exit(); // Stop execution after redirection
        } else {
            // User does not exist or credentials are incorrect
            echo "Invalid email or password";
        }
    } catch(mysqli_sql_exception $e) {
        // Handle any database connection errors here
        echo "Error: " . $e->getMessage();
    } finally {
        // Close the database connection
        $conn->close();
    }
}
?>
