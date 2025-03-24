<?php
require_once "../config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM Class WHERE class_id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $class = $result->fetch_assoc();
    } else {
        echo "Class not found.";
        exit;
    }
} else {
    echo "Class ID not provided!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $className = $_POST['class_name'];
    $trainerId = $_POST['trainer_id'];
    $scheduleTime = $_POST['schedule_time'];
    $duration = $_POST['duration_minutes'];
    $capacity = $_POST['capacity'];

    $updateSql = "UPDATE Class 
                  SET class_name='$className',
                      trainer_id=$trainerId,
                      schedule_time='$scheduleTime',
                      duration_minutes=$duration,
                      capacity=$capacity
                  WHERE class_id = $id";

    if ($conn->query($updateSql) === TRUE) {
        header("Location: view_classes.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Class</title>
</head>
<body>
<h1>Update Class</h1>
<form method="post">
    <label>Class Name:</label>
    <input type="text" name="class_name" value="<?php echo $class['class_name']; ?>" required><br><br>

    <label>Trainer ID:</label>
    <input type="number" name="trainer_id" value="<?php echo $class['trainer_id']; ?>" required><br><br>

    <label>Schedule Time:</label>
    <input type="time" name="schedule_time" value="<?php echo $class['schedule_time']; ?>" required><br><br>

    <label>Duration (minutes):</label>
    <input type="number" name="duration_minutes" value="<?php echo $class['duration_minutes']; ?>" required><br><br>

    <label>Capacity:</label>
    <input type="number" name="capacity" value="<?php echo $class['capacity']; ?>" required><br><br>

    <button type="submit">Update Class</button>
</form>
</body>
</html>