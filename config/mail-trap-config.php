<?php

require_once __DIR__ .  "/../assets/vendor/autoload.php";


class MailtrapConfig
{
    private string $apiKey;
    private string $host = 'sandbox.smtp.mailtrap.io';
    private int $port = 2525;

    public function __construct()
    {
        $apiKey = $_ENV['MAILTRAP_API_KEY'] ?? null;

        if (empty($apiKey)) {
            throw new Exception("Mailtrap API Key is not set in environment variables. Check your .env file.");
        }

        $this->apiKey = $apiKey;
    }

    /**
     * Gets the configured API key for use in a mailer client (e.g., Symfony Mailer, SwiftMailer).
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Gets the configured SMTP host.
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Gets the configured SMTP port.
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }
}