<?php
require_once __DIR__ . "/config/mail_config.php";

// Enable error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $to = "triguy024@gmail.com"; // Your receiving email
    $subject = "Test Email from Gym Management System";
    $body = "
        <html>
        <body>
            <h2>Test Email</h2>
            <p>This is a test email to verify the mail configuration is working properly.</p>
            <p>If you received this email, your mail settings are configured correctly.</p>
            <p>Sent at: " . date('Y-m-d H:i:s') . "</p>
        </body>
        </html>";

    $mailer = MailConfig::getInstance();
    if($mailer->sendMail($to, $subject, $body)) {
        echo "<h2 style='color: green;'>Test email sent successfully!</h2>";
        echo "<p>Please check your inbox (and spam folder) for the test email.</p>";
    } else {
        echo "<h2 style='color: red;'>Failed to send test email.</h2>";
        echo "<p>Error: " . $mailer->getError() . "</p>";
    }
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error occurred:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>