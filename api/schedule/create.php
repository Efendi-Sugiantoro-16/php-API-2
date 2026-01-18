<?php
/**
 * Create Schedule API Endpoint
 * POST /api/schedule/create.php
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Schedule.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

use App\Models\Schedule;
use App\Middleware\AuthMiddleware;

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Require authentication
$userId = AuthMiddleware::requireAuth();

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validation
$errors = [];

if (empty($data['title'])) {
    $errors['title'] = ['Title is required'];
}

if (empty($data['subject'])) {
    $errors['subject'] = ['Subject is required'];
}

if (empty($data['start_time'])) {
    $errors['start_time'] = ['Start time is required'];
}

if (empty($data['end_time'])) {
    $errors['end_time'] = ['End time is required'];
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

// Create schedule
try {
    $schedule = Schedule::create([
        'user_id' => $userId,
        'title' => $data['title'],
        'description' => $data['description'] ?? null,
        'subject' => $data['subject'],
        'start_time' => $data['start_time'],
        'end_time' => $data['end_time'],
        'is_completed' => false
    ]);
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Schedule created successfully',
        'data' => ['id' => $schedule->id]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create schedule'
    ]);
}
