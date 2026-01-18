<?php
/**
 * Authentication Middleware using JWT
 */

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware
{
    private static $secretKey;
    
    public static function init()
    {
        self::$secretKey = $_ENV['JWT_SECRET'] ?? 'default_secret_key';
    }
    
    /**
     * Generate JWT token for user
     */
    public static function generateToken($userId, $username)
    {
        self::init();
        
        $payload = [
            'iss' => 'manajemen_waktu_app',
            'aud' => 'manajemen_waktu_app',
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24 hours
            'user_id' => $userId,
            'username' => $username
        ];
        
        return JWT::encode($payload, self::$secretKey, 'HS256');
    }
    
    /**
     * Verify JWT token and return user data
     */
    public static function verifyToken()
    {
        self::init();
        
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (empty($authHeader)) {
            return ['success' => false, 'message' => 'Authorization header missing'];
        }
        
        // Extract Bearer token
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return ['success' => false, 'message' => 'Invalid authorization format'];
        }
        
        $token = $matches[1];
        
        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));
            return [
                'success' => true,
                'user_id' => $decoded->user_id,
                'username' => $decoded->username
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid or expired token'];
        }
    }
    
    /**
     * Require authentication - returns user_id or sends 401 response
     */
    public static function requireAuth()
    {
        $result = self::verifyToken();
        
        if (!$result['success']) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => $result['message']
            ]);
            exit();
        }
        
        return $result['user_id'];
    }
}
