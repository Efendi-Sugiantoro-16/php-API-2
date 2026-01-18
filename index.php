<?php
/**
 * Main Entry Point for Railway Deployment
 * Simple router for PHP built-in server
 */

// Get the request URI
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// If file exists, serve it directly
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false;
}

// Route API requests
if (preg_match('/^\/api\//', $path)) {
    $file = __DIR__ . $path;
    if (file_exists($file)) {
        require $file;
        exit;
    }
}

// Default response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Manajemen Waktu Belajar API',
    'version' => '1.0.0',
    'endpoints' => [
        'POST /api/auth/register.php' => 'Register new user',
        'POST /api/auth/login.php' => 'Login user',
        'GET /api/dashboard/index.php' => 'Get dashboard stats',
        'GET /api/schedule/read.php' => 'Get all schedules',
        'GET /api/schedule/read_single.php?id=X' => 'Get single schedule',
        'POST /api/schedule/create.php' => 'Create schedule',
        'PUT /api/schedule/update.php' => 'Update schedule',
        'DELETE /api/schedule/delete.php' => 'Delete schedule',
        'GET /api/user/profile.php' => 'Get user profile',
        'POST /api/user/update_profile.php' => 'Update profile'
    ]
]);
