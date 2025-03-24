<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = (float)$_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    
    $updateQuery = "UPDATE Payment SET 
                   amount = ?,
                   payment_date = ?,
                   payment_method = ?
                   WHERE payment_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("dssi", $amount, $payment_date, $payment_method, $payment_id);
    
    if ($stmt->execute()) {
        header("Location: financial.php");
        exit();
    }
}

$query = "SELECT p.*, m.first_name, m.last_name 
          FROM Payment p 
          JOIN Member m ON p.member_id = m.member_id 
          WHERE p.payment_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Payment - Admin Dashboard</title>
    <style>
        .container { max-width: 600px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd; }
        .submit-btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Payment</h1>
        <p>Member: <?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?></p>
        
        <form method="POST">
            <div class="form-group">
                <label>Amount ($)</label>
                <input type="number" step="0.01" name="amount" value="<?php echo $payment['amount']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Payment Date</label>
                <input type="date" name="payment_date" value="<?php echo $payment['payment_date']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Payment Method</label>
                <input type="text" name="payment_method" value="<?php echo htmlspecialchars($payment['payment_method']); ?>" required>
            </div>
            
            <button type="submit" class="submit-btn">Update Payment</button>
        </form>
    </div>
</body>
</html>
