<?php
require_once "../config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM Equipment WHERE equipment_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: view_equipment.php");
        exit;
    } else {
        echo "Error deleting equipment: " . $conn->error;
    }
} else {
    echo "Equipment ID not provided!";
}
?>