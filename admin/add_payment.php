<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];

    $sql = "INSERT INTO Payment (member_id, amount, payment_date, payment_method) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idss", $member_id, $amount, $payment_date, $payment_method);
    
    if ($stmt->execute()) {
        header("Location: financial.php?success=1");
    } else {
        header("Location: financial.php?error=1");
    }
    exit();
}

header("Location: financial.php");
exit();