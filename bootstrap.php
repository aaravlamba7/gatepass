<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $configPath = __DIR__ . '/config.php';
    if (!is_file($configPath)) {
        throw new RuntimeException('Database setup is not complete. Create config.php from config.example.php and add your hosting database details.');
    }

    $config = require $configPath;
    $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['database'] . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function siteUrl(string $path): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $directory = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    return $scheme . '://' . $host . $directory . '/' . ltrim($path, '/');
}

function showDatabaseError(Throwable $error)
{
    error_log($error->getMessage());
    http_response_code(500);
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup needed</title><link rel="stylesheet" href="style.css"><main class="container"><section class="message-card"><h1>Database setup needed</h1><p>Check the database details in config.php and make sure schema.sql has been imported through phpMyAdmin.</p><p><a href="index.php">Back to home</a></p></section></main></html>';
    exit;
}
