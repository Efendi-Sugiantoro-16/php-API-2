<?php
/**
 * Update Schedule API Endpoint
 * PUT /api/schedule/update.php
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Schedule.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

use App\Models\Schedule;
use App\Middleware\AuthMiddleware;

// Only accept PUT requests
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Require authentication
$userId = AuthMiddleware::requireAuth();

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validation
if (empty($data['id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Schedule ID is required'
    ]);
    exit();
}

// Find schedule
$schedule = Schedule::where('id', $data['id'])
    ->where('user_id', $userId)
    ->first();

if (!$schedule) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Schedule not found'
    ]);
    exit();
}

// Update schedule
try {
    $schedule->update([
        'title' => $data['title'] ?? $schedule->title,
        'description' => $data['description'] ?? $schedule->description,
        'subject' => $data['subject'] ?? $schedule->subject,
        'start_time' => $data['start_time'] ?? $schedule->start_time,
        'end_time' => $data['end_time'] ?? $schedule->end_time,
        'category' => $data['category'] ?? $schedule->category,
        'is_completed' => $data['is_completed'] ?? $schedule->is_completed
    ]);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Schedule updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update schedule'
    ]);
}
