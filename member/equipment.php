<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

// Fetch all equipment
$query = "SELECT * FROM Equipment ORDER BY category";
$result = $conn->query($query);

// Group equipment by category
$equipment = [];
while($item = $result->fetch_assoc()) {
    $equipment[$item['category']][] = $item;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gym Equipment - Gym Management System</title>
    <style>
        .equipment-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .category-section {
            margin-bottom: 40px;
        }
        
        .equipment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .equipment-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .status-available {
            color: #28a745;
        }
        
        .status-maintenance {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="equipment-container">
        <h1>Gym Equipment</h1>
        
        <?php foreach($equipment as $category => $items): ?>
            <div class="category-section">
                <h2><?php echo htmlspecialchars($category); ?></h2>
                <div class="equipment-grid">
                    <?php foreach($items as $item): ?>
                        <div class="equipment-card">
                            <h3><?php echo htmlspecialchars($item['equipment_name']); ?></h3>
                            <p class="status-<?php echo strtolower($item['maintenance_status']); ?>">
                                Status: <?php echo htmlspecialchars($item['maintenance_status']); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
