<?php
/**
 * Update User Profile API Endpoint
 * POST /api/user/update_profile.php
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
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
    exit();
}

// Get form data (supports both JSON and form-data)
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($contentType, 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

// Validation
$errors = [];

if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = ['Invalid email format'];
}

// Check if email is taken by another user
if (!empty($data['email']) && $data['email'] !== $user->email) {
    $existingUser = User::where('email', $data['email'])
        ->where('id', '!=', $userId)
        ->first();
    
    if ($existingUser) {
        $errors['email'] = ['Email already taken'];
    }
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

// Update user profile
try {
    $user->update([
        'name' => $data['name'] ?? $user->name,
        'email' => $data['email'] ?? $user->email,
        'npm' => $data['npm'] ?? $user->npm
    ]);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update profile'
    ]);
}
