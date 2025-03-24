<?php
require_once "../config/config.php";
require_once "../auth/check_auth.php";

$member_id = $_SESSION['user_id'];

// Fetch member details
$query = "SELECT * FROM Member WHERE member_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_no'];
    
    $updateQuery = "UPDATE Member SET 
                   first_name = ?,
                   last_name = ?,
                   email = ?,
                   phone_no = ?
                   WHERE member_id = ?";
                   
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssi", $firstName, $lastName, $email, $phone, $member_id);
    
    // Add password update if provided
    if (!empty($_POST['new_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if ($newPassword === $confirmPassword) {
            $passwordQuery = "UPDATE Member SET password = ? WHERE member_id = ?";
            $stmt = $conn->prepare($passwordQuery);
            $stmt->bind_param("si", $newPassword, $member_id);
            
            if ($stmt->execute()) {
                $success = "Profile and password updated successfully!";
            } else {
                $error = "Error updating password.";
            }
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Error updating profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile Management - Gym Management System</title>
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
        <h1>Profile Management</h1>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($member['first_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($member['last_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="phone_no" value="<?php echo htmlspecialchars($member['phone_no']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>New Password (leave blank to keep current):</label>
                <input type="password" name="new_password" minlength="8">
            </div>
            
            <div class="form-group">
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_password" minlength="8">
            </div>
            
            <div class="button-group">
                <button type="submit" class="action-link">Update Profile</button>
                <a href="dashboard.php" class="action-link">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>

