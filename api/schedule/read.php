<?php
/**
 * Read All Schedules API Endpoint
 * GET /api/schedule/read.php
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

// Get all schedules for this user
$schedules = Schedule::where('user_id', $userId)
    ->orderBy('start_time', 'desc')
    ->get();

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $schedules
]);
