<?php
require_once "../config/config.php";
require_once "../config/mail_config.php";

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

// Get expired memberships that haven't received a reminder in the last 7 days
$query = "SELECT m.* FROM Member m
          LEFT JOIN EmailReminders er ON m.member_id = er.member_id 
          AND er.sent_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
          WHERE m.end_date < CURDATE()
          AND er.reminder_id IS NULL";

$result = $conn->query($query);

while ($member = $result->fetch_assoc()) {
    if (sendReminderEmail($member['email'], $member['first_name'] . ' ' . $member['last_name'])) {
        $insertQuery = "INSERT INTO EmailReminders (member_id, sent_date, email_type, status) 
                       VALUES (?, NOW(), 'RENEWAL_REMINDER', 'SENT')";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("i", $member['member_id']);
        $stmt->execute();
    }
}
?>
