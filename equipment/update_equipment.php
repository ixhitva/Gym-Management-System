<?php
require_once "../config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch the current equipment record
    $sql = "SELECT * FROM Equipment WHERE equipment_id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $equipment = $result->fetch_assoc();
    } else {
        echo "Equipment not found.";
        exit;
    }
} else {
    echo "Equipment ID not provided!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $equipmentName     = $_POST['equipment_name'];
    $category          = $_POST['category'];
    $purchaseDate      = $_POST['purchase_date'];
    $maintenanceStatus = $_POST['maintenance_status'];

    $updateSql = "UPDATE Equipment 
                  SET equipment_name='$equipmentName',
                      category='$category',
                      purchase_date='$purchaseDate',
                      maintenance_status='$maintenanceStatus'
                  WHERE equipment_id = $id";

    if ($conn->query($updateSql) === TRUE) {
        header("Location: view_equipment.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Equipment - Gym Management System</title>
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Update Equipment Details</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Equipment Name:</label>
                <input type="text" name="equipment_name" class="form-control" value="<?php echo htmlspecialchars($equipment['equipment_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Category:</label>
                <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($equipment['category']); ?>" required>
            </div>

            <div class="form-group">
                <label>Purchase Date:</label>
                <input type="date" name="purchase_date" class="form-control" value="<?php echo htmlspecialchars($equipment['purchase_date']); ?>" required>
            </div>

            <div class="form-group">
                <label>Maintenance Status:</label>
                <select name="maintenance_status" class="form-control" required>
                    <option value="Good" <?php echo $equipment['maintenance_status'] == 'Good' ? 'selected' : ''; ?>>Good</option>
                    <option value="Needs Maintenance" <?php echo $equipment['maintenance_status'] == 'Needs Maintenance' ? 'selected' : ''; ?>>Needs Maintenance</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">Update Equipment</button>
        </form>
        <a href="view_equipment.php" class="back-btn">Back to Equipment List</a>
    </div>
</body>
</html>