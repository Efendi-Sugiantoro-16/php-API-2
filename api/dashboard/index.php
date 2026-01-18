<?php
/**
 * Dashboard API Endpoint
 * GET /api/dashboard/index.php
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

// Get dashboard statistics
$totalSchedules = Schedule::where('user_id', $userId)->count();
$completed = Schedule::where('user_id', $userId)->where('is_completed', true)->count();
$pending = Schedule::where('user_id', $userId)->where('is_completed', false)->count();

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => [
        'total_schedules' => $totalSchedules,
        'completed' => $completed,
        'pending' => $pending
    ]
]);
