<?php
/**
 * Get User Profile API Endpoint
 * GET /api/user/profile.php
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

use App\Models\User;
use App\Middleware\AuthMiddleware;

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Require authentication
$userId = AuthMiddleware::requireAuth();

// Get user profile
$user = User::find($userId);

if (!$user) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
    exit();
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => [
        'id' => $user->id,
        'username' => $user->username,
        'email' => $user->email,
        'name' => $user->name,
        'npm' => $user->npm,
        'photo' => $user->photo,
        'created_at' => $user->created_at
    ]
]);
