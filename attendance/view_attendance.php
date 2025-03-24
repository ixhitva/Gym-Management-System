<?php
require_once "../config/config.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gym Management - Attendance</title>
</head>
<body>
    <h1>Attendance Records</h1>
    <table border="1">
        <tr>
            <th>Attendance ID</th>
            <th>Member ID</th>
            <th>Class ID</th>
            <th>Attendance Date</th>
            <th>Check-in Time</th>
            <th>Actions</th>
        </tr>
        <?php
        $sql = "SELECT attendance_id, member_id, class_id, attendance_date, check_in_time FROM Attendance";
        $result = $conn->query($sql);

        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo $row['attendance_id']; ?></td>
            <td><?php echo $row['member_id']; ?></td>
            <td><?php echo $row['class_id']; ?></td>
            <td><?php echo $row['attendance_date']; ?></td>
            <td><?php echo $row['check_in_time']; ?></td>
            <td>
                <a href="update_attendance.php?id=<?php echo $row['attendance_id']; ?>">Update</a> |
                <a href="delete_attendance.php?id=<?php echo $row['attendance_id']; ?>"
                   onclick="return confirm('Are you sure you want to delete this attendance record?');">
                   Delete
                </a>
            </td>
        </tr>
        <?php
            endwhile;
        else:
        ?>
        <tr>
            <td colspan="6">No attendance records found.</td>
        </tr>
        <?php endif; ?>
    </table>
    <br>
    <a href="add_attendance.php">Add New Attendance</a>
</body>
</html>