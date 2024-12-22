<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your payment details.");
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user's latest payment based on the user_id and payment status
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM fine_payments WHERE user_id = $user_id AND payment_status = 'Pending' ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $payment = $result->fetch_assoc();
} else {
    die("No pending payments found.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - Traffic Fine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Payment Details</h1>
    
    <h2>Fine Details</h2>
    <p><strong>Case Number:</strong> <?php echo htmlspecialchars($payment['case_number']); ?></p>
    <p><strong>IC Number:</strong> <?php echo htmlspecialchars($payment['ic_number']); ?></p>
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($payment['full_name']); ?></p>
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['payment_method']); ?></p>
    <p><strong>Amount:</strong> RM <?php echo number_format($payment['amount'], 2); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($payment['payment_status']); ?></p>
    
    <form action="generate_pdf.php" method="POST">
        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
        <button type="submit">Generate PDF</button>
    </form>
</div>

</body>
</html>
