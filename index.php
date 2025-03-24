<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: auth/login.php");
    exit();
}

// If logged in, redirect to appropriate dashboard
switch($_SESSION['role']) {
    case 'admin':
        header("Location: admin/dashboard.php");
        break;
    case 'trainer':
        header("Location: trainer/dashboard.php");
        break;
    case 'member':
        header("Location: member/dashboard.php");
        break;
    default:
        header("Location: auth/login.php");
}
exit();
?>
