<?php           
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

require_once '../blog/wp-load.php';
 
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}


// Setup autoloading
require 'init_autoloader.php';

define('DOMPDF_ENABLE_AUTOLOAD', false);
require_once './vendor/dompdf/dompdf/dompdf_config.inc.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();

