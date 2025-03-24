<?php
require_once "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $equipmentName      = $_POST['equipment_name'];
    $category           = $_POST['category'];
    $purchaseDate       = $_POST['purchase_date'];
    $maintenanceStatus  = $_POST['maintenance_status'];

    $sql = "INSERT INTO Equipment (equipment_name, category, purchase_date, maintenance_status)
            VALUES ('$equipmentName', '$category', '$purchaseDate', '$maintenanceStatus')";

    if ($conn->query($sql) === TRUE) {
        echo "New equipment added successfully!";
        header("Location: view_equipment.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Equipment - Gym Management System</title>
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Add New Equipment</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Equipment Name:</label>
                <input type="text" name="equipment_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Category:</label>
                <input type="text" name="category" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Purchase Date:</label>
                <input type="date" name="purchase_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Maintenance Status:</label>
                <select name="maintenance_status" class="form-control" required>
                    <option value="Good">Good</option>
                    <option value="Needs Maintenance">Needs Maintenance</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">Add Equipment</button>
        </form>
        <a href="../admin/dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>