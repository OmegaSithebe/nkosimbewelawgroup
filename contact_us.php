<?php
// Enhanced error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log errors to file
ini_set('log_errors', 1);
ini_set('error_log', 'contact_us_errors.log');

// Database configuration
$host = "localhost";
$user = "nkosifwi_user";
$password = "nkosiLawDev@24!";
$dbname = "nkosifwi_nkosimbewelawgroup";

try {
    // Create connection
    $conn = new mysqli($host, $user, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Validate and sanitize inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    if (empty($name) || empty($email) || empty($message)) {
        throw new Exception("Missing required fields");
    }

    // Prepare and execute SQL
    $stmt = $conn->prepare("INSERT INTO contactUs (name, email, message) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("sss", $name, $email, $message);
    
    if (!$stmt->execute()) {
        throw new Exception("Execution failed: " . $stmt->error);
    }

    // Email configuration
    $to = "admin@nkosimbewelawgroup.co.za";
    $subject = "New Contact Form Submission";
    $messageBody = "New contact form submission:\n\nName: $name\nEmail: $email\nMessage:\n$message";
    $headers = "From: no-reply@nkosimbewelawgroup.co.za\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send email with error handling
    if (!mail($to, $subject, $messageBody, $headers)) {
        error_log("Failed to send email to $to");
    }

    // Success - redirect
    header("Location: success.html");
    exit();

} catch (Exception $e) {
    // Log the error
    error_log($e->getMessage());
    
    // Redirect with error
    header("Location: index.html?error=processing_error#contact");
    exit();
} finally {
    // Close connections
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>