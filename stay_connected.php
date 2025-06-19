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
$fullname = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Validate
if (empty($fullname) || empty($email)) {
    header("Location: index.html?error=missing_fields#stay-connected");
    exit();
}

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO stayConnected (fullname, email) VALUES (?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ss", $fullname, $email);

if ($stmt->execute()) {
    // Email details
    $to = "admin@nkosimbewelawgroup.co.za";
    $subject = "New Stay Connected Subscription";
    $message = "New subscription:\n\nName: $fullname\nEmail: $email";
    $headers = "From: no-reply@nkosimbewelawgroup.co.za\r\n";
    $headers .= "Reply-To: $email\r\n";
    
    // Send email
    mail($to, $subject, $message, $headers);
    
    // Redirect to success page
    header("Location: success.html");
    exit();
} else {
    header("Location: index.html?error=database_error#stay-connected");
    exit();
}

$stmt->close();
$conn->close();
?>