<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get payments data
$query = "SELECT p.payment_id, m.first_name, m.last_name, p.amount, 
          p.payment_date, p.payment_method 
          FROM Payment p 
          JOIN Member m ON p.member_id = m.member_id 
          ORDER BY p.payment_date DESC";
$result = $conn->query($query);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="financial_report.csv"');

// Create CSV file
$output = fopen('php://output', 'w');

// Add headers
fputcsv($output, array('Payment ID', 'Member Name', 'Amount', 'Date', 'Method'));

// Add data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, array(
        $row['payment_id'],
        $row['first_name'] . ' ' . $row['last_name'],
        $row['amount'],
        $row['payment_date'],
        $row['payment_method']
    ));
}

fclose($output);
exit();
?>
