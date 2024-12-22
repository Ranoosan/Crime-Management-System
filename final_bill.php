<?php
// Start session
session_start();

// Check if the user's NIC and payment details are stored in the session
if (!isset($_SESSION['nic_number'])) {
    echo "You need to log in to access this page.";
    exit();
}

$nic_number = $_SESSION['nic_number'];
$paid_amount = $_SESSION['paid_amount'];
$fine_amount = $_SESSION['fine_amount'];
$remaining_amount = $_SESSION['remaining_amount'];

// Determine if the case is dismissed (i.e., full payment made)
$is_case_dismissed = ($fine_amount == $paid_amount);

// Generate the bill
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Bill</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .bill-container {
            margin: 0 auto;
            width: 50%;
            padding: 20px;
            border: 1px solid #000;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        .bill-header {
            text-align: center;
        }
        .bill-details {
            margin-top: 20px;
        }
        .bill-details label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .dismiss-alert {
            color: green;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="bill-container">
    <h2 class="bill-header">Final Bill for Traffic Fine Payment</h2>
    
    <div class="bill-details">
        <label>NIC Number: <?php echo $nic_number; ?></label>
        <label>Total Fine Amount: <?php echo $fine_amount; ?> LKR</label>
        <label>Total Amount Paid: <?php echo $paid_amount; ?> LKR</label>
        <label>Remaining Amount: <?php echo $remaining_amount; ?> LKR</label>
    </div>
    
    <!-- Check if case is dismissed -->
    <?php if ($is_case_dismissed): ?>
        <p class="dismiss-alert">Congratulations! You have fully paid the fine. Your case has been dismissed.</p>
    <?php else: ?>
        <p class="dismiss-alert" style="color: red;">Your case is still active. Please pay the remaining amount.</p>
    <?php endif; ?>
    
    <p>Thank you for your payment!</p>
</div>

</body>
</html>
