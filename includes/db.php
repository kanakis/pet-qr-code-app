<?php
/**
 * Database Connection Configuration
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'pet_qr_code_app');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Create database connection
 * @return PDO|null
 */
function getDbConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Generate a unique token for secure access
 * @return string
 */
function generateUniqueToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}
?>
