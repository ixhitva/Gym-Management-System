<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class MailConfig {
    private static $instance = null;
    private $mailer;
    private $error = null;

    private function __construct() {
        try {
            $this->mailer = new PHPMailer(true);
            
            // Enable debug mode (0 = off, 2 = client and server)
            $this->mailer->SMTPDebug = 0;
            
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'triguy024@gmail.com'; // Replace with your Gmail
            $this->mailer->Password = 'exaz iphe jvzy oybb'; // Replace with your app password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 587;
            
            // Default settings
            $this->mailer->isHTML(true);
            $this->mailer->setFrom('triguy024@gmail.com', 'Gym Management'); // Replace with your Gmail
            $this->mailer->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log("Mail initialization error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sendMail($to, $subject, $body, $altBody = '') {
        if ($this->error) {
            return false;
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = $altBody ?: strip_tags($body);
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log("Mail sending error: " . $e->getMessage());
            return false;
        }
    }

    public function getError() {
        return $this->error;
    }
}