<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

$trainer_id = $_SESSION['user_id'];
$error = null;
$success = null;

// First show list of trainer's classes if no class selected
if (!isset($_GET['class_id'])) {
    // Fetch trainer's classes
    $query = "SELECT * FROM Class WHERE trainer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $trainer_id);
    $stmt->execute();
    $classes = $stmt->get_result();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Mark Attendance - Trainer Dashboard</title>
        <style>
            .classes-container {
                max-width: 800px;
                margin: 20px auto;
                padding: 20px;
            }
            .class-card {
                background: white;
                padding: 15px;
                margin-bottom: 15px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .mark-link {
                display: inline-block;
                padding: 5px 10px;
                background: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <div class="classes-container">
            <h1>Select Class to Mark Attendance</h1>
            <?php while($class = $classes->fetch_assoc()): ?>
                <div class="class-card">
                    <h3><?php echo htmlspecialchars($class['class_name']); ?></h3>
                    <p>Day: <?php echo htmlspecialchars($class['day']); ?></p>
                    <a href="?class_id=<?php echo $class['class_id']; ?>" class="mark-link">Mark Attendance</a>
                </div>
            <?php endwhile; ?>
            <?php if ($classes->num_rows === 0): ?>
                <p>You don't have any classes assigned yet.</p>
            <?php endif; ?>
            <a href="dashboard.php" class="mark-link" style="background: #6c757d;">Back to Dashboard</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Validate class_id
if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {
    $error = "Invalid class selection";
} else {
    $class_id = (int)$_GET['class_id'];
    
    // Verify class belongs to trainer
    $classQuery = "SELECT * FROM Class WHERE class_id = ? AND trainer_id = ?";
    $stmt = $conn->prepare($classQuery);
    $stmt->bind_param("ii", $class_id, $trainer_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        $error = "Class not found or unauthorized access";
    } else {
        // Handle attendance submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['member_id'])) {
            try {
                $member_id = (int)$_POST['member_id'];
                
                // Add attendance record
                $insertQuery = "INSERT INTO Attendance (member_id, class_id, attendance_date) 
                              VALUES (?, ?, CURDATE())";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("ii", $member_id, $class_id);
                
                if ($stmt->execute()) {
                    $success = "Attendance marked successfully!";
                } else {
                    $error = "Error marking attendance";
                }
            } catch (Exception $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
        
        // Fetch all members not yet enrolled in this class
        $query = "SELECT m.member_id, m.first_name, m.last_name 
                  FROM Member m 
                  WHERE m.member_id NOT IN (
                      SELECT member_id FROM Attendance WHERE class_id = ?
                  )";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $available_members = $stmt->get_result();
        
        // Fetch enrolled members
        $enrolledQuery = "SELECT m.member_id, m.first_name, m.last_name, a.attendance_date
                         FROM Member m 
                         JOIN Attendance a ON m.member_id = a.member_id 
                         WHERE a.class_id = ?";
        $stmt = $conn->prepare($enrolledQuery);
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $enrolled_members = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <style>
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        .member-list { margin: 20px 0; }
        .success { color: green; }
        .error { color: red; }
        .form-group { margin-bottom: 15px; }
        select { padding: 5px; width: 200px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Mark Attendance</h2>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Form to add new member to class -->
        <form method="POST">
            <div class="form-group">
                <label>Select Member:</label>
                <select name="member_id" required>
                    <option value="">Select a member...</option>
                    <?php while($member = $available_members->fetch_assoc()): ?>
                        <option value="<?php echo $member['member_id']; ?>">
                            <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit">Mark Attendance</button>
        </form>
        
        <!-- Display enrolled members -->
        <div class="member-list">
            <h3>Enrolled Members</h3>
            <?php if ($enrolled_members->num_rows > 0): ?>
                <ul>
                    <?php while($member = $enrolled_members->fetch_assoc()): ?>
                        <li>
                            <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                            (Date: <?php echo date('Y-m-d', strtotime($member['attendance_date'])); ?>)
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No members enrolled yet.</p>
            <?php endif; ?>
        </div>
        
        <a href="dashboard.php" style="display: inline-block; margin-top: 20px; color: #007bff;">Back to Dashboard</a>
    </div>
</body>
</html>
