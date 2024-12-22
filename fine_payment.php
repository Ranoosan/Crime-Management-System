<?php
// fine_payment.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to make a payment.");
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $case_number = $_POST['case_number'];
    $ic_number = $_POST['ic_number'];
    $full_name = $_POST['full_name'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];
    $user_id = $_SESSION['user_id'];  // Get logged-in user's ID
    $payment_status = "Pending"; // Assuming the payment status is pending until processed

    // Insert payment details into the database
    $sql = "INSERT INTO fine_payments (case_number, ic_number, full_name, payment_method, amount, payment_status, user_id)
            VALUES ('$case_number', '$ic_number', '$full_name', '$payment_method', '$amount', '$payment_status', '$user_id')";

    if ($conn->query($sql) === TRUE) {
        // Get the ID of the newly inserted record
        $payment_id = $conn->insert_id;

        // Redirect to the bill page with the payment ID
        header("Location: get_bill.php?payment_id=" . $payment_id);
        exit(); // Ensure the script stops after redirection
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Crime Fine Payment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Traffic Crime Fine Payment</h1>
    
    <form action="fine_payment.php" method="POST">
        <label for="case_number">Case Number:</label>
        <input type="text" id="case_number" name="case_number" required>
        
        <label for="ic_number">IC Number:</label>
        <input type="text" id="ic_number" name="ic_number" required>
        
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required>
        
        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="PayPal">PayPal</option>
        </select>
        
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required>
        
        <button type="submit">Proceed to Payment</button>
    </form>
</div>

</body>
</html>
