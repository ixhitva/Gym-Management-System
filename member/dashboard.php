<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

if ($_SESSION['role'] != 'member') {
    header("Location: ../auth/login.php");
    exit();
}

$member_id = $_SESSION['user_id'];

// Fetch member details
$memberQuery = "SELECT * FROM Member WHERE member_id = $member_id";
$memberResult = $conn->query($memberQuery)->fetch_assoc();

// Fetch enrolled classes count
$classesQuery = "SELECT COUNT(*) as count FROM Attendance WHERE member_id = $member_id";
$classCount = $conn->query($classesQuery)->fetch_assoc()['count'];

// Fetch next class
$nextClassQuery = "SELECT c.class_name, c.day 
                  FROM Class c 
                  JOIN Attendance a ON c.class_id = a.class_id 
                  WHERE a.member_id = $member_id 
                  ORDER BY FIELD(c.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
                  LIMIT 1";
$nextClass = $conn->query($nextClassQuery)->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Dashboard - Gym Management System</title>
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

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .status-active {
            background: #28a745;
            color: white;
        }

        .status-expired {
            background: #dc3545;
            color: white;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <img src="../assets/images/gym_logo.png" alt="Gym Logo" class="logo">
            <h1>Welcome, <?php echo $memberResult['first_name']; ?>!</h1>
        </div>
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Membership Status</h3>
            <p>Valid until: <?php echo date('F d, Y', strtotime($memberResult['end_date'])); ?></p>
            <span class="status-badge <?php echo strtotime($memberResult['end_date']) > time() ? 'status-active' : 'status-expired'; ?>">
                <?php echo strtotime($memberResult['end_date']) > time() ? 'Active' : 'Expired'; ?>
            </span>
        </div>

        <div class="stat-card">
            <h3>Enrolled Classes</h3>
            <p>Total Classes: <?php echo $classCount; ?></p>
            <?php if ($nextClass): ?>
                <p>Next Class: <?php echo $nextClass['class_name']; ?></p>
                <p>Day: <?php echo $nextClass['day']; ?></p>
            <?php else: ?>
                <p>No upcoming classes</p>
            <?php endif; ?>
        </div>

        <div class="stat-card">
            <h3>Payment Details</h3>
            <p>Amount: $<?php echo $memberResult['price']; ?></p>
        </div>
    </div>

    <div class="quick-actions">
        <div class="action-card">
            <h3>Profile Management</h3>
            <p>View and update your personal information</p>
            <a href="profile.php" class="action-link">Manage Profile</a>
        </div>

        <div class="action-card">
            <h3>Class Enrollment</h3>
            <p>Browse and enroll in available classes</p>
            <a href="classes.php" class="action-link">View Classes</a>
        </div>

        <div class="action-card">
            <h3>Payment History</h3>
            <p>View your payment history and plan details</p>
            <a href="payments.php" class="action-link">View Payments</a>
        </div>

        <div class="action-card">
            <h3>Gym Equipment</h3>
            <p>View available gym equipment</p>
            <a href="equipment.php" class="action-link">View Equipment</a>
        </div>
    </div>
</body>
</html>
