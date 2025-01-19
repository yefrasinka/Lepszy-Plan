<?php

require_once __DIR__ . '/autoload.php';
use App\Service\Router;

// Database Configuration
$config = [
    'db_dsn' => 'sqlite:' . __DIR__ . '/../data.db',
    'db_user' => '',
    'db_pass' => '',
];

// Establish SQLite Connection
try {
    $pdo = new \PDO($config['db_dsn'], $config['db_user'], $config['db_pass']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Routing Logic
$action = $_GET['action'] ?? null;

if ($action === 'generate_ical') {
    require __DIR__ . '/generate_ical.php';
    exit;
}

if ($action === 'seed') {
    require __DIR__ . '/../src/Service/Seeder.php';
    $seeder = new \App\Service\Seeder();
    $seeder->seed();
    echo "Database seeding complete!";
}

// Serve index.html for all other requests
$indexPath = __DIR__ . '/index.html';
if (file_exists($indexPath)) {
    readfile($indexPath);
    exit;
}


// Return JSON response if index.html is not found
header('Content-Type: application/json');
echo json_encode(['error' => 'index.html not found']);
http_response_code(404);
exit;
