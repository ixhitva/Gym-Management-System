<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

$member_id = $_SESSION['user_id'];

// Fetch payment history with correct columns
$query = "SELECT payment_id, amount, payment_date, payment_method 
          FROM Payment 
          WHERE member_id = ? 
          ORDER BY payment_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$payments = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment History - Gym Management System</title>
    <style>
        .payments-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .payment-table th,
        .payment-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .payment-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="payments-container">
        <h1>Payment History</h1>
        
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                <?php while($payment = $payments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                        <td>$<?php echo htmlspecialchars($payment['amount']); ?></td>
                        <td><?php echo date('F d, Y', strtotime($payment['payment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($payments->num_rows == 0): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No payment records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
