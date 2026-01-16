<?php
/**
 * CORS Configuration
 * 
 * Enables Cross-Origin Resource Sharing for React frontend
 */

// Allow requests from React frontend
// Update this with your actual frontend domain
$allowedOrigins = [
    // Development
    'http://localhost:3000',
    'http://localhost:5173',
    'http://127.0.0.1:3000',
    'http://127.0.0.1:5173',
    'http://localhost',
    // Production
    'https://blogs.indiapropertys.com',
    'https://www.blogs.indiapropertys.com',
    'https://indiapropertys.com',
    'https://www.indiapropertys.com',
];

// Get the origin from the request
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Set CORS headers if origin is allowed
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Allow all origins in development (remove in production)
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 3600");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
