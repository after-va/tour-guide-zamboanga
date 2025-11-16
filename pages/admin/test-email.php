<?php
require_once __DIR__ . "/../../assets/vendor/autoload.php";
require_once __DIR__ . "/../../config/mail-trap-config.php"; // Make sure path is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(
        dirname(__DIR__, 2),
        'mailtrap.env'
    );
    $dotenv->load();
try {
    // --- Create Mailtrap configuration object ---
    $config = new MailtrapConfig(); // THIS WAS MISSING

    // --- PHPMailer Setup ---
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $config->getHost();
    $mail->SMTPAuth   = true;
    $mail->Username   = $config->getUsername();
    $mail->Password   = $config->getPassword();
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $config->getPort();

    // --- Recipients ---
    $mail->setFrom('notifications@myapp.dev', 'My App');
    $mail->addAddress('alelybitun.school@gmail.com', 'Real Recipient');

    // --- Content ---
    $mail->isHTML(true);
    $mail->Subject = 'Test Email to Real Address';
    $mail->Body    = '<h2>Hello!</h2><p>This is a test email sent via Mailtrap Production SMTP.</p>';
    $mail->AltBody = 'Hello! This is a test email sent via Mailtrap Production SMTP.';

    // --- Send Email ---
    $mail->send();
    echo "✅ Email sent to real address!";

} catch (Exception $e) {
    echo "❌ Mailer Error: " . $mail->ErrorInfo;
} catch (\Throwable $e) {
    echo "❌ General Error: " . $e->getMessage();
}