<?php
require_once "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $className = $_POST['class_name'];
    $description = $_POST['description'];
    $day = $_POST['day'];
    $maxCapacity = $_POST['max_capacity'];
    $trainerId = $_POST['trainer_id'];

    // Insert without specifying class_id (it will auto-increment)
    $sql = "INSERT INTO Class (class_name, description, day, max_capacity, trainer_id)
            VALUES (?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $className, $description, $day, $maxCapacity, $trainerId);
    
    if ($stmt->execute()) {
        header("Location: view_classes.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Class - Gym Management System</title>
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Add New Class</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Class Name:</label>
                <input type="text" name="class_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label>Day:</label>
                <select name="day" class="form-control" required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>

            <div class="form-group">
                <label>Max Capacity:</label>
                <input type="number" name="max_capacity" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Trainer:</label>
                <select name="trainer_id" class="form-control" required>
                    <?php
                    $trainers = $conn->query("SELECT trainer_id, first_name, last_name FROM Trainer");
                    while($trainer = $trainers->fetch_assoc()):
                    ?>
                        <option value="<?php echo $trainer['trainer_id']; ?>">
                            <?php echo $trainer['first_name'] . ' ' . $trainer['last_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="submit-btn">Add Class</button>
        </form>
        <a href="../admin/dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>