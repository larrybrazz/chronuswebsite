<?php
error_reporting(1);
// Database connection parameters
$servername = "srv1349.hstgr.io";
$username = "u698293704_trainer";
$password = "rk!4yEG/7";
$database = "u698293704_train_subs";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if email is set and not empty
if(isset($_POST['email']) && !empty($_POST['email'])) {
    // Sanitize email input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Check if the email is valid
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare SQL statement to insert email into database
        $sql = "INSERT INTO subscribers (email) VALUES ('$email')";
        
        if ($conn->query($sql) === TRUE) {
            $success_message = "Thank you for subscribing!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error_message = "Invalid email address!";
    }
} else {
    $error_message = "Email address is required!";
}

// Close database connection
$conn->close();
?>
