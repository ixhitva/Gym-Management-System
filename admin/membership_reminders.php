<?php
require_once "../config/config.php";
require_once "../config/mail_config.php";
require_once "../auth/check_auth.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Function to send reminder email
function sendReminderEmail($memberEmail, $memberName) {
    $subject = "Gym Membership Renewal Reminder";
    $body = "
        <html>
        <body>
            <h2>Membership Renewal Reminder</h2>
            <p>Dear {$memberName},</p>
            <p>Your gym membership has expired. Please renew your membership to continue enjoying our services.</p>
            <p>Best regards,<br>Gym Management Team</p>
        </body>
        </html>";
    
    return MailConfig::getInstance()->sendMail($memberEmail, $subject, $body);
}

// Handle manual reminder sending
if (isset($_POST['send_reminder'])) {
    $member_id = $_POST['member_id'];
    
    // Get member details
    $memberQuery = "SELECT first_name, last_name, email FROM Member WHERE member_id = ?";
    $stmt = $conn->prepare($memberQuery);
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $member = $stmt->get_result()->fetch_assoc();
    
    if (sendReminderEmail($member['email'], $member['first_name'] . ' ' . $member['last_name'])) {
        // Record the sent reminder
        $insertQuery = "INSERT INTO EmailReminders (member_id, sent_date, email_type, status) 
                       VALUES (?, NOW(), 'RENEWAL_REMINDER', 'SENT')";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        
        $success = "Reminder sent successfully!";
    } else {
        $error = "Failed to send reminder email.";
    }
}

// Get expired memberships
$expiredQuery = "SELECT m.member_id, m.first_name, m.last_name, m.email, m.end_date,
                 (SELECT MAX(sent_date) FROM EmailReminders 
                  WHERE member_id = m.member_id AND email_type = 'RENEWAL_REMINDER') as last_reminder
                 FROM Member m
                 WHERE m.end_date < CURDATE()
                 ORDER BY m.end_date DESC";
$expired = $conn->query($expiredQuery);

// Get reminder history
$reminderQuery = "SELECT er.*, m.first_name, m.last_name, m.email 
                 FROM EmailReminders er
                 JOIN Member m ON er.member_id = m.member_id
                 ORDER BY er.sent_date DESC";
$reminders = $conn->query($reminderQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Membership Reminders - Admin Dashboard</title>
    <style>
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        .section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background: #f8f9fa; }
        .btn { padding: 5px 10px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Membership Reminders</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="section">
            <h2>Expired Memberships</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Member Name</th>
                        <th>Email</th>
                        <th>Expiry Date</th>
                        <th>Last Reminder</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($member = $expired->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($member['end_date'])); ?></td>
                            <td><?php echo $member['last_reminder'] ? date('Y-m-d', strtotime($member['last_reminder'])) : 'Never'; ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="member_id" value="<?php echo $member['member_id']; ?>">
                                    <button type="submit" name="send_reminder" class="btn">Send Reminder</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>Reminder History</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Member Name</th>
                        <th>Email</th>
                        <th>Sent Date</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reminder = $reminders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reminder['first_name'] . ' ' . $reminder['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($reminder['email']); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($reminder['sent_date'])); ?></td>
                            <td><?php echo htmlspecialchars($reminder['email_type']); ?></td>
                            <td><?php echo htmlspecialchars($reminder['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="../admin/dashboard.php" class="btn" style="background: #6c757d;">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
