<?php
require('fpdf/fpdf.php'); // Include the FPDF library

// Check if the payment ID is passed via POST
if (!isset($_POST['payment_id'])) {
    die("No payment details found.");
}

$payment_id = $_POST['payment_id'];

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

// Get payment details for the specific payment ID
$sql = "SELECT * FROM fine_payments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);  // Prevent SQL injection
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $payment = $result->fetch_assoc();
} else {
    die("Payment details not found.");
}

$conn->close();

// Create the PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Title
$pdf->Cell(0, 10, 'Traffic Fine Payment Bill', 0, 1, 'C');

// Line break
$pdf->Ln(10);

// Payment details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Case Number: ' . $payment['case_number'], 0, 1);
$pdf->Cell(0, 10, 'IC Number: ' . $payment['ic_number'], 0, 1);
$pdf->Cell(0, 10, 'Full Name: ' . $payment['full_name'], 0, 1);
$pdf->Cell(0, 10, 'Payment Method: ' . $payment['payment_method'], 0, 1);
$pdf->Cell(0, 10, 'Amount: RM ' . number_format($payment['amount'], 2), 0, 1);
$pdf->Cell(0, 10, 'Payment Status: ' . $payment['payment_status'], 0, 1);

// Output the PDF
$pdf->Output('D', 'payment_bill_' . $payment['id'] . '.pdf'); // D to download the file

?>
