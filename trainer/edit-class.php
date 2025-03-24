<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

$trainer_id = $_SESSION['user_id'];
$error = null;
$success = null;

// Fetch all classes first if no specific class selected
if (!isset($_GET['class_id'])) {
    // Show list of trainer's classes
    $query = "SELECT * FROM Class WHERE trainer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $trainer_id);
    $stmt->execute();
    $classes = $stmt->get_result();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>My Classes - Trainer Dashboard</title>
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
            .edit-link {
                display: inline-block;
                padding: 5px 10px;
                background: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 4px;
            }
            .action-buttons {
                display: flex;
                gap: 10px;
                margin-top: 10px;
            }
            .delete-link {
                display: inline-block;
                padding: 5px 10px;
                background: #dc3545;
                color: white;
                text-decoration: none;
                border-radius: 4px;
            }
            .delete-link:hover {
                background: #c82333;
            }
            .success {
                background: #d4edda;
                color: #155724;
                padding: 10px;
                margin-bottom: 15px;
                border-radius: 4px;
            }
            .error {
                background: #f8d7da;
                color: #721c24;
                padding: 10px;
                margin-bottom: 15px;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <div class="classes-container">
            <h1>My Classes</h1>
            <div class="header-actions">
                <a href="add-class.php" class="add-link">Add New Class</a>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php
            if (isset($_GET['deleted'])) {
                $success = "Class deleted successfully!";
            } elseif (isset($_GET['error']) && $_GET['error'] === 'delete') {
                $error = "Error deleting class";
            }
            ?>
            
            <?php if (isset($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
                
            <?php while($class = $classes->fetch_assoc()): ?>
                <div class="class-card">
                    <h3><?php echo htmlspecialchars($class['class_name']); ?></h3>
                    <p><?php echo htmlspecialchars($class['description']); ?></p>
                    <p>Day: <?php echo htmlspecialchars($class['day']); ?></p>
                    <div class="action-buttons">
                        <a href="?class_id=<?php echo $class['class_id']; ?>" class="edit-link">Edit Class</a>
                        <a href="delete-class.php?class_id=<?php echo $class['class_id']; ?>" 
                           class="delete-link" 
                           onclick="return confirm('Are you sure you want to delete this class?');">Delete Class</a>
                    </div>
                </div>
            <?php endwhile; ?>

            

        
            <?php if (isset($success)): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($classes->num_rows === 0): ?>
                <p>You don't have any classes assigned yet.</p>
            <?php endif; ?>
            <a href="dashboard.php" class="edit-link" style="background: #6c757d;">Back to Dashboard</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Validate class_id
if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {
    $error = "Invalid class ID";
} else {
    $class_id = (int)$_GET['class_id'];
    
    // Fetch class details
    $query = "SELECT * FROM Class WHERE class_id = ? AND trainer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $class_id, $trainer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = "Class not found or you don't have permission to edit it";
    } else {
        $class = $result->fetch_assoc();
        
        // Fetch attendance for this class
        $attendanceQuery = "SELECT m.first_name, m.last_name, a.attendance_date 
                           FROM Attendance a 
                           JOIN Member m ON a.member_id = m.member_id
                           WHERE a.class_id = ?
                           ORDER BY a.attendance_date DESC";
        $stmt = $conn->prepare($attendanceQuery);
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $attendance = $stmt->get_result();
        
        // Handle class update
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $className = trim($_POST['class_name']);
            $description = trim($_POST['description']);
            $day = trim($_POST['day']);
            
            if (empty($className) || empty($description) || empty($day)) {
                $error = "All fields are required";
            } else {
                $updateQuery = "UPDATE Class SET 
                              class_name = ?,
                              description = ?,
                              day = ?
                              WHERE class_id = ? AND trainer_id = ?";
                              
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("sssii", $className, $description, $day, $class_id, $trainer_id);
                
                if ($stmt->execute()) {
                    $success = "Class updated successfully!";
                    // Refresh class data
                    $class['class_name'] = $className;
                    $class['description'] = $description;
                    $class['day'] = $day;
                } else {
                    $error = "Error updating class: " . $conn->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Class - Trainer Dashboard</title>
    <style>
        .class-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .success {
            color: #28a745;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #d4edda;
            border-radius: 4px;
        }
        
        .error {
            color: #dc3545;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f8d7da;
            border-radius: 4px;
        }
        
        .action-link {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        
        .attendance-section {
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .attendance-table th,
        .attendance-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .attendance-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="class-container">
        <h1>Edit Class</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <a href="dashboard.php" class="action-link">Back to Dashboard</a>
        <?php elseif ($class): ?>
            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Class Name:</label>
                    <input type="text" name="class_name" value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" required><?php echo htmlspecialchars($class['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Day:</label>
                    <select name="day" required>
                        <?php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        foreach ($days as $dayOption) {
                            $selected = ($class['day'] == $dayOption) ? 'selected' : '';
                            echo "<option value=\"$dayOption\" $selected>$dayOption</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" class="action-link">Update Class</button>
                <a href="dashboard.php" class="action-link" style="margin-left: 10px;">Back</a>
            </form>
            
            <div class="attendance-section">
                <h2>Class_Attendance</h2>
                <?php if ($attendance->num_rows > 0): ?>
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Date_signed_up</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($record = $attendance->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                                    <td><?php echo date('F d, Y', strtotime($record['attendance_date'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No attendance records found for this class.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
