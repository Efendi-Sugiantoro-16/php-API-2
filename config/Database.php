<?php
/**
 * Database Configuration using Eloquent ORM (PostgreSQL)
 * Supports both local .env and Railway environment variables
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

// Try to load .env file (for local development)
// On Railway, environment variables are set directly
$envPath = __DIR__ . '/../';
if (file_exists($envPath . '.env')) {
    $dotenv = Dotenv::createImmutable($envPath);
    $dotenv->load();
}

// Get environment variables (works for both local and production)
$dbHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
$dbPort = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '5432';
$dbName = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'manajemen_waktu';
$dbUser = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'postgres';
$dbPass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';

// Initialize Eloquent ORM
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => $dbHost,
    'port'      => $dbPort,
    'database'  => $dbName,
    'username'  => $dbUser,
    'password'  => $dbPass,
    'charset'   => 'utf8',
    'prefix'    => '',
    'schema'    => 'public',
]);

// Make this Capsule instance available globally
$capsule->setAsGlobal();

// Boot Eloquent
$capsule->bootEloquent();
