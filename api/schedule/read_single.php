<?php
/**
 * Read Single Schedule API Endpoint
 * GET /api/schedule/read_single.php?id=1
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Schedule.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

use App\Models\Schedule;
use App\Middleware\AuthMiddleware;

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Require authentication
$userId = AuthMiddleware::requireAuth();

// Get schedule ID from query string
$scheduleId = $_GET['id'] ?? null;

if (empty($scheduleId)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Schedule ID is required'
    ]);
    exit();
}

// Find schedule
$schedule = Schedule::where('id', $scheduleId)
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

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $schedule
]);
