<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch statistics
$memberCount = $conn->query("SELECT COUNT(*) as count FROM Member")->fetch_assoc()['count'];
$trainerCount = $conn->query("SELECT COUNT(*) as count FROM Trainer")->fetch_assoc()['count'];
$classCount = $conn->query("SELECT COUNT(*) as count FROM Class")->fetch_assoc()['count'];
$equipmentCount = $conn->query("SELECT COUNT(*) as count FROM Equipment")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Gym Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
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
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0;
            color: #333;
        }
        
        .stat-card .number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
            margin: 10px 0;
        }
        
        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px;
        }
        
        .action-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .action-card a {
            display: block;
            padding: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .action-card a:hover {
            background: #0056b3;
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
        
        .logout-btn:hover {
            background: #c82333;
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
            <h1>Admin Dashboard</h1>
        </div>
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Members</h3>
            <div class="number"><?php echo $memberCount; ?></div>
            <a href="../members/view_members.php">View Details</a>
        </div>
        
        <div class="stat-card">
            <h3>Total Trainers</h3>
            <div class="number"><?php echo $trainerCount; ?></div>
            <a href="../trainers/view_trainers.php">View Details</a>
        </div>
        
        <div class="stat-card">
            <h3>Active Classes</h3>
            <div class="number"><?php echo $classCount; ?></div>
            <a href="../class/view_classes.php">View Details</a>
        </div>
        
        <div class="stat-card">
            <h3>Equipment Count</h3>
            <div class="number"><?php echo $equipmentCount; ?></div>
            <a href="../equipment/view_equipment.php">View Details</a>
        </div>
    </div>

    <div class="admin-actions">
        <div class="action-card">
            <h3>Member Management</h3>
            <a href="../members/add_member.php">Add New Member</a>
        </div>
        
        <div class="action-card">
            <h3>Trainer Management</h3>
            <a href="../trainers/add_trainer.php">Add New Trainer</a>
        </div>
        
        <div class="action-card">
            <h3>Class Management</h3>
            <a href="../class/add_class.php">Add New Class</a>
        </div>
        
        <div class="action-card">
            <h3>Equipment Management</h3>
            <a href="../equipment/add_equipment.php">Add New Equipment</a>
        </div>
        
        <div class="action-card">
            <h3>Financial Management</h3>
            <a href="financial.php">View Financial Reports</a>
        </div>
    </div>

</body>
</html>
