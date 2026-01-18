<?php
/**
 * Database Migration Script
 * Run: php database/migrate.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

echo "=== Database Migration ===\n\n";

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? '5432';
$dbName = $_ENV['DB_NAME'] ?? 'manajemen_waktu';
$username = $_ENV['DB_USER'] ?? 'postgres';
$password = $_ENV['DB_PASS'] ?? '';

try {
    // Connect to PostgreSQL server (without database)
    echo "Connecting to PostgreSQL server...\n";
    $pdo = new PDO(
        "pgsql:host=$host;port=$port",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ Connected to PostgreSQL server\n\n";

    // Check if database exists
    $stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '$dbName'");
    $dbExists = $stmt->fetch();

    if (!$dbExists) {
        echo "Creating database '$dbName'...\n";
        $pdo->exec("CREATE DATABASE $dbName");
        echo "✓ Database '$dbName' created\n\n";
    } else {
        echo "✓ Database '$dbName' already exists\n\n";
    }

    // Connect to the database
    echo "Connecting to database '$dbName'...\n";
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbName",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ Connected to database\n\n";

    // Read and execute schema.sql
    echo "Running migrations...\n";
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // Remove comments for cleaner execution
    $sql = preg_replace('/^--.*$/m', '', $sql);
    
    $pdo->exec($sql);
    echo "✓ Schema migrations completed\n\n";

    echo "=== Migration Successful! ===\n";
    echo "Database: $dbName\n";
    echo "Host: $host:$port\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
