<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database config
$host = "localhost";
$user = "nkosifwi_user";
$password = "nkosiLawDev@24!";
$dbname = "nkosifwi_nkosimbewelawgroup";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data safely
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$preferredDate = isset($_POST['preferredDate']) ? trim($_POST['preferredDate']) : '';
$preferredTime = isset($_POST['preferredTime']) ? trim($_POST['preferredTime']) : '';

// Validate
if (empty($fullname) || empty($email) || empty($preferredDate) || empty($preferredTime)) {
    header("Location: index.html?error=missing_fields");
    exit();
}

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO scheduleConsultation (fullname, email, preferredDate, preferredTime) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ssss", $fullname, $email, $preferredDate, $preferredTime);

if ($stmt->execute()) {
    // Email details
    $to = "admin@nkosimbewelawgroup.co.za";
    $subject = "New Consultation Scheduled";
    $message = "New consultation booking:\n\nName: $fullname\nEmail: $email\nPreferred Date: $preferredDate\nPreferred Time: $preferredTime";
    $headers = "From: no-reply@nkosimbewelawgroup.co.za\r\n";
    $headers .= "Reply-To: $email\r\n";
    
    // Send email
    mail($to, $subject, $message, $headers);
    
    // Redirect to success page
    header("Location: success.html");
    exit();
} else {
    header("Location: index.html?error=database_error");
    exit();
}

$stmt->close();
$conn->close();
?>