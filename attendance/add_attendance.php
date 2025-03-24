<?php
require_once "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $memberId      = $_POST['member_id'];
    $classId       = $_POST['class_id'];
    $attendanceDate = $_POST['attendance_date'];
    $checkInTime   = $_POST['check_in_time'];

    $sql = "INSERT INTO Attendance (member_id, class_id, attendance_date, check_in_time)
            VALUES ($memberId, $classId, '$attendanceDate', '$checkInTime')";

    if ($conn->query($sql) === TRUE) {
        echo "New attendance record added successfully!";
        header("Location: view_attendance.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Attendance</title>
</head>
<body>
<h1>Add Attendance Record</h1>
<form method="post">
    <label>Member ID:</label>
    <input type="number" name="member_id" required><br><br>

    <label>Class ID:</label>
    <input type="number" name="class_id" required><br><br>

    <label>Attendance Date:</label>
    <input type="date" name="attendance_date" required><br><br>

    <label>Check-in Time:</label>
    <input type="time" name="check_in_time" required><br><br>

    <button type="submit">Add Attendance</button>
</form>
</body>
</html>