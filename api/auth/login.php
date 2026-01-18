<?php
/**
 * Login API Endpoint
 * POST /api/auth/login.php
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

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validation
$errors = [];

if (empty($data['username'])) {
    $errors['username'] = ['Username is required'];
}

if (empty($data['password'])) {
    $errors['password'] = ['Password is required'];
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

// Find user by username
$user = User::where('username', $data['username'])->first();

if (!$user) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
    exit();
}

// Verify password
if (!password_verify($data['password'], $user->password)) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid password'
    ]);
    exit();
}

// Generate JWT token
$token = AuthMiddleware::generateToken($user->id, $user->username);

// Return success response
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'data' => [
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email
        ]
    ]
]);
