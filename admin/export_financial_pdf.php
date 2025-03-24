<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";
require_once '../vendor/autoload.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get total revenue
$revenueQuery = "SELECT SUM(amount) as total FROM Payment";
$totalRevenue = $conn->query($revenueQuery)->fetch_assoc()['total'];

// Get monthly revenue
$monthlyQuery = "SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, 
                 SUM(amount) as total 
                 FROM Payment 
                 GROUP BY month 
                 ORDER BY month DESC";
$monthlyRevenue = $conn->query($monthlyQuery);

// Get all payments
$query = "SELECT p.payment_id, m.first_name, m.last_name, p.amount, 
          p.payment_date, p.payment_method 
          FROM Payment p 
          JOIN Member m ON p.member_id = m.member_id 
          ORDER BY p.payment_date DESC";
$payments = $conn->query($query);

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins
$pdf->SetMargins(15, 15, 15);

// Add a page
$pdf->AddPage();

// Title
$pdf->SetFont('helvetica', 'B', 24);
$pdf->Cell(0, 10, 'Financial Report', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Generated on ' . date('Y-m-d H:i:s'), 0, 1, 'L');
$pdf->Ln(10);

// Revenue Overview
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 10, 'Revenue Overview', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 14);
$pdf->Cell(0, 10, 'Total Revenue: $' . number_format($totalRevenue, 2), 0, 1, 'L');
$pdf->Ln(10);

// Monthly Revenue Report
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 10, 'Monthly Revenue Report', 0, 1, 'L');
$pdf->Ln(5);

// Monthly Revenue Table
$pdf->SetFont('helvetica', 'B', 12);
$w = array(120, 60);
$pdf->Cell($w[0], 7, 'Month', 1, 0, 'L');
$pdf->Cell($w[1], 7, 'Revenue', 1, 1, 'L');

$pdf->SetFont('helvetica', '', 12);
while($month = $monthlyRevenue->fetch_assoc()) {
    $pdf->Cell($w[0], 7, date('F Y', strtotime($month['month'] . '-01')), 1, 0, 'L');
    $pdf->Cell($w[1], 7, '$' . number_format($month['total'], 2), 1, 1, 'L');
}
$pdf->Ln(10);

// Payment Records
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 10, 'Payment Records', 0, 1, 'L');
$pdf->Ln(5);

// Payment Records Table
$pdf->SetFont('helvetica', 'B', 12);
$w = array(20, 50, 35, 40, 35);
$pdf->Cell($w[0], 7, 'ID', 1, 0, 'L');
$pdf->Cell($w[1], 7, 'Member', 1, 0, 'L');
$pdf->Cell($w[2], 7, 'Amount', 1, 0, 'L');
$pdf->Cell($w[3], 7, 'Date', 1, 0, 'L');
$pdf->Cell($w[4], 7, 'Method', 1, 1, 'L');

$pdf->SetFont('helvetica', '', 12);
while($payment = $payments->fetch_assoc()) {
    $pdf->Cell($w[0], 7, $payment['payment_id'], 1, 0, 'L');
    $pdf->Cell($w[1], 7, $payment['first_name'] . ' ' . $payment['last_name'], 1, 0, 'L');
    $pdf->Cell($w[2], 7, '$' . number_format($payment['amount'], 2), 1, 0, 'L');
    $pdf->Cell($w[3], 7, date('Y-m-d', strtotime($payment['payment_date'])), 1, 0, 'L');
    $pdf->Cell($w[4], 7, $payment['payment_method'], 1, 1, 'L');
}

// Output PDF
$pdf->Output('financial_report.pdf', 'D');