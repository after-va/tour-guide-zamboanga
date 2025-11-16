<?php
// Mailer.php

require_once __DIR__ . "/../assets/vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer {
    protected PHPMailer $mail;

    // Default configuration array for known providers
    const PROVIDER_CONFIGS = [
        'gmail' => [
            'Host'       => 'smtp.gmail.com',
            'Port'       => 587,
            'SMTPSecure' => PHPMailer::ENCRYPTION_STARTTLS,
        ],
        'yahoo' => [
            'Host'       => 'smtp.mail.yahoo.com',
            'Port'       => 587,
            'SMTPSecure' => PHPMailer::ENCRYPTION_STARTTLS,
        ],
        'office365' => [
            'Host'       => 'smtp.office365.com',
            'Port'       => 587,
            'SMTPSecure' => PHPMailer::ENCRYPTION_STARTTLS,
        ],
        // Add more providers here if you like
    ];

    /**
     * Initializes PHPMailer and sets common defaults.
     *
     * @param string $providerKey Optional key for predefined provider settings.
     * @param string $username    SMTP username (optional, can be set later).
     * @param string $password    SMTP password (optional, can be set later).
     */
    public function __construct(string $providerKey = '', string $username = '', string $password = '') {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->SMTPAuth   = true;
        $this->mail->isHTML(true);

        // Default placeholder – will be overwritten by setProvider()
        $this->mail->Host = 'smtp.placeholder.com';

        if ($providerKey) {
            $this->setProvider($providerKey);
        }

        if ($username && $password) {
            $this->setCredentials($username, $password);
        }
    }

    /**
     * Set a predefined provider (gmail, yahoo, office365, …)
     */
    public function setProvider(string $providerKey): bool {
        $providerKey = strtolower($providerKey);

        if (!isset(self::PROVIDER_CONFIGS[$providerKey])) {
            error_log("Mailer Error: Unknown provider key '{$providerKey}'.");
            return false;
        }

        $config = self::PROVIDER_CONFIGS[$providerKey];

        $this->mail->Host       = $config['Host'];
        $this->mail->Port       = $config['Port'];
        $this->mail->SMTPSecure = $config['SMTPSecure'];

        return true;
    }

    /**
     * Set SMTP credentials (username + password / app-password)
     */
    public function setCredentials(string $username, string $password): void {
        $this->mail->Username = $username;
        $this->mail->Password = $password;
    }

    /**
     * Set the From address
     */
    public function setFrom(string $email, string $name = 'Tourist Platform'): void {
        $this->mail->setFrom($email, $name);
    }

    /**
     * Add a recipient
     */
    public function addRecipient(string $email, string $name = ''): void {
        $this->mail->addAddress($email, $name);
    }

    /**
     * Add CC recipient
     */
    public function addCC(string $email, string $name = ''): void {
        $this->mail->addCC($email, $name);
    }

    /**
     * Add BCC recipient
     */
    public function addBCC(string $email, string $name = ''): void {
        $this->mail->addBCC($email, $name);
    }

    /**
     * Set subject + HTML body + optional plain-text fallback
     */
    public function setContent(string $subject, string $bodyHTML, string $bodyAltText = ''): void {
        $this->mail->Subject = $subject;
        $this->mail->Body    = $bodyHTML;
        $this->mail->AltBody = $bodyAltText ?: strip_tags($bodyHTML);
    }

    /**
     * Attach a file
     */
    public function addAttachment(string $filePath, string $fileName = ''): void {
        $this->mail->addAttachment($filePath, $fileName);
    }

    /**
     * Send the e-mail
     *
     * @return bool  true on success, false on failure
     */
    public function send(): bool {
        try {
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Get the last PHPMailer error message
     */
    public function getError(): string {
        return $this->mail->ErrorInfo;
    }
}