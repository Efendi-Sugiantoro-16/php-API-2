<?php
/**
 * Register API Endpoint
 * POST /api/auth/register.php
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/User.php';

use App\Models\User;

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
} elseif (strlen($data['username']) < 3) {
    $errors['username'] = ['Username must be at least 3 characters'];
}

if (empty($data['email'])) {
    $errors['email'] = ['Email is required'];
} elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = ['Invalid email format'];
}

if (empty($data['password'])) {
    $errors['password'] = ['Password is required'];
} elseif (strlen($data['password']) < 6) {
    $errors['password'] = ['Password must be at least 6 characters'];
}

if (empty($data['name'])) {
    $errors['name'] = ['Name is required'];
}

// Return validation errors
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit();
}

// Check if username or email already exists
$existingUser = User::where('username', $data['username'])
    ->orWhere('email', $data['email'])
    ->first();

if ($existingUser) {
    http_response_code(400);
    $field = $existingUser->username === $data['username'] ? 'username' : 'email';
    echo json_encode([
        'success' => false,
        'message' => ucfirst($field) . ' already exists',
        'errors' => [$field => [ucfirst($field) . ' already taken']]
    ]);
    exit();
}

// Create new user
try {
    $user = User::create([
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => password_hash($data['password'], PASSWORD_BCRYPT),
        'name' => $data['name'],
        'npm' => $data['npm'] ?? null,
        'photo' => 'default.png'
    ]);
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'data' => [
            'id' => $user->id,
            'username' => $user->username
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Registration failed',
        'errors' => ['server' => [$e->getMessage()]]
    ]);
}
