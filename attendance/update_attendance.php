<?php
require_once "../config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch the current attendance record
    $sql = "SELECT * FROM Attendance WHERE attendance_id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $attendance = $result->fetch_assoc();
    } else {
        echo "Attendance record not found.";
        exit;
    }
} else {
    echo "Attendance ID not provided!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $memberId      = $_POST['member_id'];
    $classId       = $_POST['class_id'];
    $attendanceDate = $_POST['attendance_date'];
    $checkInTime   = $_POST['check_in_time'];

    $updateSql = "UPDATE Attendance 
                  SET member_id=$memberId,
                      class_id=$classId,
                      attendance_date='$attendanceDate',
                      check_in_time='$checkInTime'
                  WHERE attendance_id = $id";

    if ($conn->query($updateSql) === TRUE) {
        header("Location: view_attendance.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Attendance</title>
</head>
<body>
<h1>Update Attendance Record</h1>
<form method="post">
    <label>Member ID:</label>
    <input type="number" name="member_id" value="<?php echo $attendance['member_id']; ?>" required><br><br>

    <label>Class ID:</label>
    <input type="number" name="class_id" value="<?php echo $attendance['class_id']; ?>" required><br><br>

    <label>Attendance Date:</label>
    <input type="date" name="attendance_date" value="<?php echo $attendance['attendance_date']; ?>" required><br><br>

    <label>Check-in Time:</label>
    <input type="time" name="check_in_time" value="<?php echo $attendance['check_in_time']; ?>" required><br><br>

    <button type="submit">Update Attendance</button>
</form>
</body>
</html>