<?php
/**
 * Change Password API Endpoint
 * POST /api/user/change_password.php
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

use App\Models\User;
use App\Middleware\AuthMiddleware;

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Require authentication
$userId = AuthMiddleware::requireAuth();

// Get user
$user = User::find($userId);

if (!$user) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

// Get form data
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

// Validation
$errors = [];

if (empty($data['current_password'])) {
    $errors['current_password'] = ['Current password is required'];
}

if (empty($data['new_password'])) {
    $errors['new_password'] = ['New password is required'];
} elseif (strlen($data['new_password']) < 6) {
    $errors['new_password'] = ['Password must be at least 6 characters'];
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit();
}

// Verify current password
// Note: We need to make the password visible temporarily since it's hidden in the model
$user->makeVisible(['password']);

if (!password_verify($data['current_password'], $user->password)) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Incorrect current password'
    ]);
    exit();
}

// Update password
try {
    $user->password = password_hash($data['new_password'], PASSWORD_BCRYPT);
    $user->save();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to change password'
    ]);
}
