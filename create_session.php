<?php
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = trim((string) ($_POST['session_name'] ?? ''));
if ($name === '' || mb_strlen($name) > 100) {
    http_response_code(400);
    exit('Please enter a session name of 1 to 100 characters.');
}

$id = bin2hex(random_bytes(16));
$adminKey = bin2hex(random_bytes(24));

try {
    $statement = db()->prepare('INSERT INTO sessions (id, name, admin_key_hash) VALUES (?, ?, ?)');
    $statement->execute([$id, $name, hash('sha256', $adminKey)]);
} catch (Throwable $error) {
    showDatabaseError($error);
}

header('Location: dashboard.php?id=' . urlencode($id) . '&key=' . urlencode($adminKey));
exit;
