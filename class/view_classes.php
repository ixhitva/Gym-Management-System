<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

// Fetch classes with trainer information
$query = "SELECT c.*, t.first_name, t.last_name 
          FROM Class c 
          LEFT JOIN Trainer t ON c.trainer_id = t.trainer_id";
$result = $conn->query($query);

// Handle class enrollment if member
if ($_SESSION['role'] == 'member' && isset($_POST['enroll'])) {
    $class_id = $_POST['class_id'];
    $member_id = $_SESSION['user_id'];
    
    // Check if already enrolled
    $checkQuery = "SELECT * FROM Attendance WHERE member_id = ? AND class_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $member_id, $class_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows == 0) {
        $enrollQuery = "INSERT INTO Attendance (member_id, class_id, attendance_date) 
                       VALUES (?, ?, CURDATE())";
        $stmt = $conn->prepare($enrollQuery);
        $stmt->bind_param("ii", $member_id, $class_id);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Classes - Gym Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f6f9;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        h1 {
            color: #333;
            margin: 0;
        }
        
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .class-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .class-card h3 {
            color: #333;
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        .class-info {
            margin-bottom: 15px;
        }
        
        .trainer-name {
            color: #666;
            font-style: italic;
        }
        
        .enroll-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        
        .enroll-btn:hover {
            background: #218838;
        }
        
        .action-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .action-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Available Classes</h1>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="add_class.php" class="action-btn">Add New Class</a>
        <?php endif; ?>
    </div>

    <div class="classes-grid">
        <?php while($class = $result->fetch_assoc()): ?>
            <div class="class-card">
                <h3><?php echo htmlspecialchars($class['class_name']); ?></h3>
                <p><?php echo htmlspecialchars($class['description']); ?></p>
                <p>Day: <?php echo htmlspecialchars($class['day']); ?></p>
                <p>Trainer: <?php echo htmlspecialchars($class['first_name'] . ' ' . $class['last_name']); ?></p>
                <p>Capacity: <?php echo htmlspecialchars($class['max_capacity']); ?></p>
                
                <?php if ($_SESSION['role'] == 'member'): ?>
                    <form method="POST">
                        <input type="hidden" name="class_id" value="<?php echo $class['class_id']; ?>">
                        <button type="submit" name="enroll" class="enroll-btn">Enroll</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
    <div style="margin-top: 20px; text-align: center;">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="../admin/dashboard.php" class="action-btn" style="background: #6c757d;">Back to Dashboard</a>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'trainer'): ?>
            <a href="../trainer/dashboard.php" class="action-btn" style="background: #6c757d;">Back to Dashboard</a>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'member'): ?>
            <a href="../member/dashboard.php" class="action-btn" style="background: #6c757d;">Back to Dashboard</a>
        <?php endif; ?>
    </div>
</body>
</html>