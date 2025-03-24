<?php
require_once "../config/config.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gym Management System - View Members</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f6f9;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .data-table tr:hover {
            background: #f5f5f5;
        }
        
        .action-links a {
            display: inline-block;
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .update-link {
            background: #007bff;
            color: white;
        }
        
        .delete-link {
            background: #dc3545;
            color: white;
        }
        
        .add-new-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .add-new-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <h1>All Members</h1>
    <table class="data-table">
        <tr>
            <th>Member ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone No</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
        <?php
        $sql = "SELECT member_id, first_name, last_name, email, phone_no, start_date, end_date FROM Member";
        $result = $conn->query($sql);

        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['member_id']); ?></td>
            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['phone_no']); ?></td>
            <td><?php echo date('Y-m-d', strtotime($row['start_date'])); ?></td>
            <td><?php 
                $end_date = strtotime($row['end_date']);
                $today = time();
                $status_class = $end_date > $today ? 'status-active' : 'status-expired';
                echo '<span class="' . $status_class . '">' . date('Y-m-d', $end_date) . '</span>';
            ?></td>
            <td class="action-links">
                <a href="update_member.php?id=<?php echo $row['member_id']; ?>" class="update-link">Update</a>
                <a href="delete_member.php?id=<?php echo $row['member_id']; ?>" 
                   onclick="return confirm('Are you sure you want to delete this member?');"
                   class="delete-link">Delete</a>
            </td>
        </tr>
        <?php
            endwhile;
        else:
        ?>
        <tr>
            <td colspan="8">No members found.</td>
        </tr>
        <?php endif; ?>
    </table>
    <div style="margin-top: 20px;">
        <a href="add_member.php" class="add-new-btn">Add New Member</a>
        <a href="../admin/dashboard.php" class="add-new-btn" style="background: #6c757d; margin-left: 10px;">Back to Dashboard</a>
    </div>
</body>
</html>
<style>
    /* Add these styles to the existing CSS */
    .status-active {
        color: #28a745;
        font-weight: bold;
    }
    
    .status-expired {
        color: #dc3545;
        font-weight: bold;
    }
</style>