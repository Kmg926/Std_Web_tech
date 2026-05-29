<?php
/**
 * SLEEVE - configuration template
 *
 * Copy this file to `config.php` in the same directory and fill in
 * real values. Never commit `config.php` to version control.
 *
 * In production:
 *   - keep display_errors Off (set in php.ini or .htaccess on the host)
 *   - this file must live under htdocs/ to satisfy open_basedir
 */

// -------- Database --------
define('DB_HOST',    'localhost');
define('DB_NAME',    'your_database_name');
define('DB_USER',    'your_username');
define('DB_PASS',    'your_password');
define('DB_CHARSET', 'utf8mb4');

// -------- Site --------
define('SITE_NAME', 'SLEEVE');
define('BASE_URL',  'https://yourdomain.infinityfree.net');
