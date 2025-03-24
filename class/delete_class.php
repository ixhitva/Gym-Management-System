<?php
require_once "../config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM Class WHERE class_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: view_classes.php");
        exit;
    } else {
        echo "Error deleting class: " . $conn->error;
    }
} else {
    echo "Class ID not provided!";
}
?>