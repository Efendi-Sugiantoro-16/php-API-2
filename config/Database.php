<?php
/**
 * Database Configuration using Eloquent ORM (PostgreSQL)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Initialize Eloquent ORM
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => $_ENV['DB_HOST'] ?? 'localhost',
    'port'      => $_ENV['DB_PORT'] ?? '5432',
    'database'  => $_ENV['DB_NAME'] ?? 'manajemen_waktu',
    'username'  => $_ENV['DB_USER'] ?? 'postgres',
    'password'  => $_ENV['DB_PASS'] ?? '',
    'charset'   => 'utf8',
    'prefix'    => '',
    'schema'    => 'public',
]);

// Make this Capsule instance available globally
$capsule->setAsGlobal();

// Boot Eloquent
$capsule->bootEloquent();
