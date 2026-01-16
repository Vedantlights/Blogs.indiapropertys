<?php
/**
 * Database Configuration
 * 
 * Loads database credentials from environment variables or uses defaults.
 * Create a .env file in the backend root directory with:
 * DB_HOST=localhost
 * DB_NAME=your_database_name
 * DB_USER=your_username
 * DB_PASS=your_password
 * DB_CHARSET=utf8mb4
 */

// Load environment variables from .env file if it exists
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue; // Skip comments and empty lines
        }
        if (strpos($line, '=') === false) {
            continue; // Skip invalid lines
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'blog_website');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

/**
 * Get PDO Database Connection
 * 
 * @return PDO
 * @throws PDOException
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $errorMsg = "Database Connection Error: " . $e->getMessage();
            error_log($errorMsg);
            error_log("DB_HOST: " . DB_HOST);
            error_log("DB_NAME: " . DB_NAME);
            error_log("DB_USER: " . DB_USER);
            throw new PDOException("Database connection failed: " . $e->getMessage(), 0, $e);
        }
    }
    
    return $pdo;
}
