<?php

require_once __DIR__ . "/../../config/mail-trap-config.php";

require_once __DIR__ .  "/../../assets/vendor/autoload.php";

// 2. Load environment variables using Dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// --- Application Logic Starts Here ---

try {
    // 3. Instantiate the configuration class. It pulls the key from $_ENV internally.
    $config = new MailtrapConfig();

    // 4. Use the configuration details
    $host = $config->getHost();
    $port = $config->getPort();
    $apiKey = $config->getApiKey();

    echo "✅ Configuration loaded successfully!\n";
    echo "   Host: {$host}\n";
    echo "   Port: {$port}\n";
    echo "   API Key (First 5 chars): " . substr($apiKey, 0, 5) . "...\n";

    // In a real application, you would pass these credentials to your mailer library:
    // $mailer = new \MailerLibrary\Client($host, $port, $apiKey);
    // $mailer->send($email);

} catch (Exception $e) {
    // Handle configuration errors (e.g., missing .env file or key)
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    // Exit with an error code
    exit(1);
}

// NOTE: To run this successfully, ensure you have:
// 1. 'src/MailtrapConfig.php' created.
// 2. Composer installed and 'composer require vlucas/phpdotenv'.
// 3. A '.env' file in the root directory containing: MAILTRAP_API_KEY="your-actual-mailtrap-key"