<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env file manually
$env = parse_ini_file(__DIR__ . '/../.env');

function sendTicketNotification($to, $subject, $message) {
    global $env;
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $env['MAIL_USERNAME'];
        $mail->Password   = $env['MAIL_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom($env['MAIL_USERNAME'], 'IT Help Desk');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #0d6efd; padding: 20px; text-align: center;'>
                    <h2 style='color: white; margin: 0;'>🖥️ IT Help Desk</h2>
                </div>
                <div style='padding: 30px; background: #f8f9fa;'>
                    <p style='font-size: 16px;'>$message</p>
                </div>
                <div style='padding: 15px; background: #dee2e6; text-align: center;'>
                    <small style='color: #6c757d;'>This is an automated message from IT Help Desk. Please do not reply.</small>
                </div>
            </div>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>