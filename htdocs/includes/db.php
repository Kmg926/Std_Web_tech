<?php
/**
 * SLEEVE - PDO singleton connection
 *
 * Usage:
 *   require_once __DIR__ . '/db.php';
 *   $pdo = db();
 *
 * Notes:
 *   - Throws PDOException internally; never propagates DB details to the client.
 *   - On failure, logs to PHP error log and emits a generic HTTP 500 page.
 *   - Assumes config.php defines DB_HOST/DB_NAME/DB_USER/DB_PASS/DB_CHARSET.
 */

// -----------------------------------------------------------------------------
// Production hardening — must run BEFORE any code that could trigger an error
// or expose driver messages. InfinityFree does not allow global php.ini edits,
// so we enforce it here at the earliest possible point.
// -----------------------------------------------------------------------------
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
// Errors should be logged, not shown to the client.
ini_set('log_errors', '1');

// Pull in config (kept outside the public path tree by convention,
// but here we are within htdocs/ due to InfinityFree open_basedir).
$__sleeve_config_path = __DIR__ . '/../config/config.php';
if (!file_exists($__sleeve_config_path)) {
    // Do not reveal which file is missing in production output.
    error_log('[SLEEVE] config.php is missing. Copy config.example.php to config.php and fill in credentials.');
    http_response_code(500);
    echo 'Service temporarily unavailable.';
    exit;
}
require_once $__sleeve_config_path;

/**
 * Return a shared PDO instance. Lazily initialized on first call.
 *
 * @return PDO
 */
function db() {
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Never leak driver messages — they may contain host/user/db name.
        error_log('[SLEEVE] DB connection failed: ' . $e->getMessage());
        http_response_code(500);
        // Generic, safe message.
        echo 'Service temporarily unavailable. Please try again later.';
        exit;
    }

    return $pdo;
}
