<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

if ($_SESSION['role'] != 'trainer') {
    header("Location: ../auth/login.php");
    exit();
}

$trainer_id = $_SESSION['user_id'];

// Fetch trainer details
$trainerQuery = "SELECT * FROM Trainer WHERE trainer_id = ?";
$stmt = $conn->prepare($trainerQuery);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$trainer = $stmt->get_result()->fetch_assoc();

// Fetch class count
$classQuery = "SELECT COUNT(*) as count FROM Class WHERE trainer_id = ?";
$stmt = $conn->prepare($classQuery);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$classCount = $stmt->get_result()->fetch_assoc()['count'];

// Fetch total members in classes
$memberQuery = "SELECT COUNT(DISTINCT member_id) as count FROM Attendance 
               WHERE class_id IN (SELECT class_id FROM Class WHERE trainer_id = ?)";
$stmt = $conn->prepare($memberQuery);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$memberCount = $stmt->get_result()->fetch_assoc()['count'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Trainer Dashboard - Gym Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f9;
        }

        .header {
            background: #000000;  /* Changed from #343a40 to #000000 */
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px;
        }

        .action-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .action-card h3 {
            margin-top: 0;
            color: #333;
        }

        .action-link {
            display: block;
            padding: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            margin-top: 10px;
        }

        .action-link:hover {
            background: #0056b3;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <img src="../assets/images/gym_logo.png" alt="Gym Logo" class="logo">
            <h1>Welcome, <?php echo htmlspecialchars($trainer['first_name']); ?>!</h1>
        </div>
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>My Classes</h3>
            <p>Total Classes: <?php echo $classCount; ?></p>
        </div>

        <div class="stat-card">
            <h3>My Members</h3>
            <p>Total Members: <?php echo $memberCount; ?></p>
        </div>

        <div class="stat-card">
            <h3>Profile Information</h3>
            <p>Specialization: <?php echo htmlspecialchars($trainer['specialization']); ?></p>
            <p>Contact: <?php echo htmlspecialchars($trainer['contact_no']); ?></p>
        </div>
    </div>

    <div class="quick-actions">
        <div class="action-card">
            <h3>Profile Management</h3>
            <p>View and update your information</p>
            <a href="edit-profile.php" class="action-link">Manage Profile</a>
        </div>

        <div class="action-card">
            <h3>Class Management</h3>
            <p>Manage your fitness classes</p>
            <a href="edit-class.php" class="action-link">Manage Classes</a>
        </div>

        <div class="action-card">
            <h3>Attendance</h3>
            <p>Mark and view class attendance</p>
            <a href="mark-attendance.php" class="action-link">Mark Attendance</a>
        </div>

        <div class="action-card">
            <h3>Equipment</h3>
            <p>View available gym equipment</p>
            <a href="view-equipment.php" class="action-link">View Equipment</a>
        </div>
    </div>
</body>
</html>
