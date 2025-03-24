<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

$trainer_id = $_SESSION['user_id'];

// Handle class addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $className = $_POST['class_name'];
    $description = $_POST['description'];
    $day = $_POST['day'];
    
    $insertQuery = "INSERT INTO Class (class_name, description, day, trainer_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssi", $className, $description, $day, $trainer_id);
    
    if ($stmt->execute()) {
        $success = "Class added successfully!";
    } else {
        $error = "Error adding class.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Class - Trainer Dashboard</title>
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Add Class</h1>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="class_name">Class Name:</label>
                <input type="text" name="class_name" id="class_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="day">Day:</label>
                <input type="text" name="day" id="day" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Class</button>
        </form>
    </div>
</body>
</html>
