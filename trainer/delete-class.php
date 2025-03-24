<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

$trainer_id = $_SESSION['user_id'];

if (isset($_GET['class_id'])) {
    $class_id = (int)$_GET['class_id'];
    
    // Verify class belongs to trainer before deleting
    $query = "DELETE FROM Class WHERE class_id = ? AND trainer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $class_id, $trainer_id);
    
    if ($stmt->execute()) {
        header("Location: edit-class.php?deleted=1");
    } else {
        header("Location: edit-class.php?error=delete");
    }
    exit();
} else {
    header("Location: edit-class.php");
    exit();
}
?>