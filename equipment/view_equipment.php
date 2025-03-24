<?php
require_once "../config/config.php"; // Adjust path as needed
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gym Management - Equipment</title>
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
        
        .status-available {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-maintenance {
            color: #dc3545;
            font-weight: bold;
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
    <h1>All Equipment</h1>
    <table class="data-table">
        <tr>
            <th>Equipment ID</th>
            <th>Equipment Name</th>
            <th>Category</th>
            <th>Purchase Date</th>
            <th>Maintenance Status</th>
            <th>Actions</th>
        </tr>
        <?php
        $sql = "SELECT equipment_id, equipment_name, category, purchase_date, maintenance_status FROM Equipment";
        $result = $conn->query($sql);

        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                $statusClass = strtolower($row['maintenance_status']) == 'available' ? 'status-available' : 'status-maintenance';
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['equipment_id']); ?></td>
            <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
            <td class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['maintenance_status']); ?></td>
            <td class="action-links">
                <a href="update_equipment.php?id=<?php echo $row['equipment_id']; ?>" class="update-link">Update</a>
                <a href="delete_equipment.php?id=<?php echo $row['equipment_id']; ?>"
                   onclick="return confirm('Are you sure you want to delete this equipment?');"
                   class="delete-link">Delete</a>
            </td>
        </tr>
        <?php
            endwhile;
        else:
        ?>
        <tr>
            <td colspan="6">No equipment found.</td>
        </tr>
        <?php endif; ?>
    </table>
    <div style="margin-top: 20px;">
        <a href="add_equipment.php" class="add-new-btn">Add New Equipment</a>
        <a href="../admin/dashboard.php" class="add-new-btn" style="background: #6c757d; margin-left: 10px;">Back to Dashboard</a>
    </div>
</body>
</html>