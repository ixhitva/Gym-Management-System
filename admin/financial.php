<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Calculate total revenue
$revenueQuery = "SELECT SUM(amount) as total FROM Payment";
$totalRevenue = $conn->query($revenueQuery)->fetch_assoc()['total'];

// Get monthly revenue
$monthlyQuery = "SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, 
                 SUM(amount) as total 
                 FROM Payment 
                 GROUP BY month 
                 ORDER BY month DESC";
$monthlyRevenue = $conn->query($monthlyQuery);

// Get all payments with member details
$paymentsQuery = "SELECT p.*, m.first_name, m.last_name 
                  FROM Payment p 
                  JOIN Member m ON p.member_id = m.member_id 
                  ORDER BY p.payment_date DESC";
$payments = $conn->query($paymentsQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Financial Management - Admin Dashboard</title>
    <style>
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        .stats-card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        .payments-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .payments-table th, .payments-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .payments-table th { background: #f8f9fa; }
        .edit-btn { padding: 5px 10px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .monthly-report { margin-top: 30px; }
        .export-btn { padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; }
        .action-card { margin-top: 20px; }
        .action-link { padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; }
        <style>
            .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
            .stats-card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
            .payments-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .payments-table th, .payments-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            .payments-table th { background: #f8f9fa; }
            .edit-btn { padding: 5px 10px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
            .monthly-report { margin-top: 30px; }
            .export-btn { padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; }
            .action-card { margin-top: 20px; }
            .action-link { padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; }
            .payment-form {
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
            .form-control {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-sizing: border-box;
            }
            .btn {
                cursor: pointer;
            }
            .btn:hover {
                opacity: 0.9;
            }
        </style>
    </style>
</head>
<body>
    <div class="container">
        <h1>Financial Management</h1>
        
        <div class="stats-card">
            <h2>Revenue Overview</h2>
            <p>Total Revenue: $<?php echo number_format($totalRevenue, 2); ?></p>
        </div>

        <div class="monthly-report">
            <h2>Monthly Revenue Report</h2>
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($month = $monthlyRevenue->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('F Y', strtotime($month['month'] . '-01')); ?></td>
                            <td>$<?php echo number_format($month['total'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="payments-list">
            <h2>Payment Records</h2>
            <div class="export-buttons">
                <a href="export_financial.php" class="export-btn">Export CSV</a>
                <a href="export_financial_pdf.php" class="export-btn">Export PDF</a>
            </div>
            <table class="payments-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($payment = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $payment['payment_id']; ?></td>
                            <td><?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?></td>
                            <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo date('F d, Y', strtotime($payment['payment_date'])); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td>
                                <a href="edit_payment.php?id=<?php echo $payment['payment_id']; ?>" class="edit-btn">Edit</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="action-card">
            <h2>Add New Payment</h2>
            <form method="POST" action="add_payment.php" class="payment-form">
                <div class="form-group">
                    <label>Member:</label>
                    <select name="member_id" required class="form-control">
                        <?php
                        $memberQuery = "SELECT member_id, first_name, last_name FROM Member ORDER BY first_name";
                        $members = $conn->query($memberQuery);
                        while($member = $members->fetch_assoc()): ?>
                            <option value="<?php echo $member['member_id']; ?>">
                                <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Amount:</label>
                    <input type="number" name="amount" step="0.01" min="0" required class="form-control">
                </div>

                <div class="form-group">
                    <label>Payment Date:</label>
                    <input type="date" name="payment_date" required class="form-control" 
                           value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Payment Method:</label>
                    <select name="payment_method" required class="form-control">
                        <option value="Credit Card">Credit Card</option>
                        <option value="Debit Card">Debit Card</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>

                <button type="submit" class="btn" style="background: #28a745;">Add Payment</button>
            </form>
        </div>

        <div class="action-card">
            <a href="membership_reminders.php" class="action-link">Manage Membership Reminders</a>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="../admin/dashboard.php" class="btn";">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
