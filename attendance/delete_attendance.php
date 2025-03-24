<?php
require_once "../config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM Attendance WHERE attendance_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: view_attendance.php");
        exit;
    } else {
        echo "Error deleting attendance record: " . $conn->error;
    }
} else {
    echo "Attendance ID not provided!";
}
?>