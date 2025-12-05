<?php

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../src/autoload.php';

$config = require_once __DIR__ . '/../config.php';

if ($config['cors_enabled']) {
    header('Access-Control-Allow-Origin: ' . implode(', ', $config['allowed_origins']));
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

use App\Controllers\ContentGeneratorController;
use App\Core\Response;

try {
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    $path = parse_url($requestUri, PHP_URL_PATH);
    
    if ($path === '/api/generate-content' && $requestMethod === 'POST') {
        $controller = new ContentGeneratorController($config);
        $controller->generateContent();
    } elseif ($path === '/' || $path === '/api' || $path === '/api/') {
        Response::success(array(
            'message' => 'AI Website Content Generator API',
            'version' => $config['api_version'],
            'endpoints' => array(
                array(
                    'method' => 'POST',
                    'path' => '/api/generate-content',
                    'description' => 'Generate website content based on business details',
                    'required_fields' => array('business_name', 'category', 'city', 'services')
                )
            ),
            'documentation' => 'See README.md for usage'
        ));
    } else {
        Response::error('Endpoint not found', 404);
    }
    
} catch (Exception $e) {
    Response::error('Internal server error: ' . $e->getMessage(), 500);
}
