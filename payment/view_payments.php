<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

$member_id = $_SESSION['user_id'];

// Fetch payments
$query = "SELECT p.*, m.first_name, m.last_name 
          FROM Payment p 
          JOIN Member m ON p.member_id = m.member_id 
          WHERE " . ($_SESSION['role'] == 'admin' ? '1' : 'p.member_id = ' . $member_id);
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment History - Gym Management System</title>
    <style>
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .payments-table th, .payments-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .payments-table th {
            background-color: #f8f9fa;
        }
        
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment History</h1>
    </div>

    <table class="payments-table">
        <thead>
            <tr>
                <th>Payment ID</th>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <th>Member Name</th>
                <?php endif; ?>
                <th>Amount</th>
                <th>Date</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            <?php while($payment = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <td><?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?></td>
                    <?php endif; ?>
                    <td>$<?php echo htmlspecialchars($payment['amount']); ?></td>
                    <td><?php echo date('F d, Y', strtotime($payment['payment_date'])); ?></td>
                    <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
