<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

$member_id = $_SESSION['user_id'];

// Handle enrollment if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll'])) {
    if (!isset($_POST['class_id'])) {
        $error = "Invalid class selection";
    } else {
        $class_id = (int)$_POST['class_id'];
        
        // Check if already enrolled
        $checkQuery = "SELECT * FROM Attendance WHERE member_id = ? AND class_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $member_id, $class_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows == 0) {
            // Simple INSERT query with only required fields
            $enrollQuery = "INSERT INTO Attendance (member_id, class_id, attendance_date) 
                           VALUES (?, ?, CURDATE())";
            $stmt = $conn->prepare($enrollQuery);
            $stmt->bind_param("ii", $member_id, $class_id);
            
            if ($stmt->execute()) {
                $success = "Successfully enrolled in class!";
            } else {
                $error = "Error enrolling in class: " . $stmt->error;
            }
        } else {
            $error = "You are already enrolled in this class.";
        }
    }
}

// Fetch enrolled classes
$enrolledQuery = "SELECT c.*, t.first_name, t.last_name 
                 FROM Class c 
                 JOIN Attendance a ON c.class_id = a.class_id
                 JOIN Trainer t ON c.trainer_id = t.trainer_id
                 WHERE a.member_id = ?";
$stmt = $conn->prepare($enrolledQuery);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$enrolled = $stmt->get_result();

// Fetch available classes
$availableQuery = "SELECT c.*, t.first_name, t.last_name 
                  FROM Class c 
                  JOIN Trainer t ON c.trainer_id = t.trainer_id
                  WHERE c.class_id NOT IN (
                      SELECT class_id FROM Attendance WHERE member_id = ?
                  )";
$stmt = $conn->prepare($availableQuery);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$available = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Classes - Gym Management System</title>
    <style>
        .classes-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .class-section {
            margin-bottom: 40px;
        }
        
        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .class-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="classes-container">
        <?php if (isset($success)): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="class-section">
            <h2>My Enrolled Classes</h2>
            <div class="class-grid">
                <?php while($class = $enrolled->fetch_assoc()): ?>
                    <div class="class-card">
                        <h3><?php echo htmlspecialchars($class['class_name']); ?></h3>
                        <p><?php echo htmlspecialchars($class['description']); ?></p>
                        <p>Day: <?php echo htmlspecialchars($class['day']); ?></p>
                        <p>Trainer: <?php echo htmlspecialchars($class['first_name'] . ' ' . $class['last_name']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <div class="class-section">
            <h2>Available Classes</h2>
            <div class="class-grid">
                <?php while($class = $available->fetch_assoc()): ?>
                    <div class="class-card">
                        <h3><?php echo htmlspecialchars($class['class_name']); ?></h3>
                        <p><?php echo htmlspecialchars($class['description']); ?></p>
                        <p>Day: <?php echo htmlspecialchars($class['day']); ?></p>
                        <p>Trainer: <?php echo htmlspecialchars($class['first_name'] . ' ' . $class['last_name']); ?></p>
                        <form method="POST">
                            <input type="hidden" name="class_id" value="<?php echo $class['class_id']; ?>">
                            <button type="submit" name="enroll" class="action-link">Enroll</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
