<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

$trainer_id = $_SESSION['user_id'];

// Fetch trainer details
$query = "SELECT * FROM Trainer WHERE trainer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$trainer = $stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['contact_no'];
    
    $updateQuery = "UPDATE Trainer SET 
                   first_name = ?,
                   last_name = ?,
                   email = ?,
                   contact_no = ?
                   WHERE trainer_id = ?";
                   
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssi", $firstName, $lastName, $email, $phone, $trainer_id);
    
    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - Trainer Dashboard</title>
    <style>
        .profile-container {
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
        
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .success {
            color: #28a745;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        
        .error {
            color: #dc3545;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Edit Profile</h1>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($trainer['first_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($trainer['last_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($trainer['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="contact_no" value="<?php echo htmlspecialchars($trainer['contact_no']); ?>" required>
            </div>
            
            <button type="submit" class="action-link">Update Profile</button>
        </form>
    </div>
</body>
</html>
