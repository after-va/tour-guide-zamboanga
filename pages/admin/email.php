<?php
require_once __DIR__ . "/../../config/mail-trap-config.php";
require_once __DIR__ . "/../../assets/vendor/autoload.php";
echo "ROOT PATH: " . realpath(__DIR__ . "/../../") . "<br>";
echo "ENV EXISTS? " . (file_exists(__DIR__ . "/../../mailtrap.env") ? "YES" : "NO");

// Correct Dotenv for version 4.x
$dotenv = Dotenv\Dotenv::createImmutable(
    dirname(__DIR__, 2),
    'mailtrap.env'
);

$dotenv->load();


try {
    $config = new MailtrapConfig();

    echo "HOST: " . $config->getHost();
    echo "PORT: " . $config->getPort();
    echo "KEY: "  . substr($config->getApiKey(), 0, 5) . "...";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage();
    exit;
}



?>