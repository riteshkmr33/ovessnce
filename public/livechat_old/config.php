<?php

/*======================================================================*\
|| #################################################################### ||
|| # Rhino 2.5                                                        # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2014 Rhino All Rights Reserved.                        # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
|| #                  http://www.livesupportrhino.com                 # ||
|| #################################################################### ||
\*======================================================================*/

// Error reporting:
error_reporting(E_ALL^E_NOTICE);

// The DB connections data
require_once 'include/db.php';

// Do not go any further if install folder still exists
if (is_dir('install')) die('Please delete or rename install folder.');

if (!LS_CACHE_DIRECTORY) die('Please define a cache directory in the db.php.');

// Absolute Path
define('APP_PATH', dirname(__file__) . DIRECTORY_SEPARATOR);

if (isset($_SERVER['SCRIPT_NAME'])) {

    # on Windows _APP_MAIN_DIR becomes \ and abs url would look something like HTTP_HOST\/restOfUrl, so \ should be trimed too
    # @modified Chis Florinel <chis.florinel@candoo.ro>
    $app_main_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    define('_APP_MAIN_DIR', $app_main_dir);
} else {
    die('[config.php] Cannot determine APP_MAIN_DIR, please set manual and comment this line');
}

// Get the ls DB class
if (LS_MYSQL_CONNECTION == 1) {
	require_once 'class/class.db.php';
} else {
	require_once 'class/class.dbn.php';
}

// MySQLi connection
$lsdb = new ls_mysql(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$lsdb->set_charset("utf8");

// All important files
include_once 'include/functions.php';
include_once 'class/class.lsbase.php';
include_once 'class/class.postmail.php';
include_once 'class/class.userlogin.php';
include_once 'class/class.user.php';

// Now launch the rewrite class, depending on the settings in db.
$getURL = New LS_rewrite($_SERVER['REQUEST_URI']);

// We are not using apache so take the ugly urls
$tempp = $getURL->lsGetseg(0);
$tempp1 = $getURL->lsGetseg(1);
$tempp2 = $getURL->lsGetseg(2);
$tempp3 = $getURL->lsGetseg(3);
$tempp4 = $getURL->lsGetseg(4);
$tempp5 = $getURL->lsGetseg(5);
$tempp6 = $getURL->lsGetseg(6);

// Check if we want caching
if (!is_dir(APP_PATH.LS_CACHE_DIRECTORY)) mkdir(APP_PATH.LS_CACHE_DIRECTORY, 0755);

// define file better for caching
$cachedefinefile = APP_PATH.LS_CACHE_DIRECTORY.'/define.php';

if (!file_exists($cachedefinefile)) {

$allsettings = "<?php\n";

// Get the general settings out the database
$result = $lsdb->query('SELECT varname, value FROM '.DB_PREFIX.'jrc_setting');
    while ($row = $result->fetch_assoc()) {
    	// collect each record into a define
    	
    	// Now check if sting contains html and do something about it!
    	if (strlen($row['value']) != strlen(filter_var($row['value'], FILTER_SANITIZE_STRING))) {
    		$defvar  = 'htmlspecialchars_decode("'.htmlspecialchars($row['value']).'")';
    	} else {
    		$defvar = "'".$row["value"]."'";
    	}
    	
        $allsettings .= "define('LS_".strtoupper($row['varname'])."', ".$defvar.");\n";
    }
    
$allsettings .= "?>";
        
LS_base::lsWriteinCache($cachedefinefile, $allsettings, '');

}

// Now include the created definefile
include_once $cachedefinefile;

// define file better for caching
$cachestufffile = APP_PATH.LS_CACHE_DIRECTORY.'/stuff.php';

if (!file_exists($cachestufffile)) {

$allstuff = "<?php\n";

// Get the general settings out the database
$resultf = $lsdb->query('SELECT id, path, name FROM '.DB_PREFIX.'jrc_files');
    while ($rowf = $resultf->fetch_assoc()) {
    	// collect each record into a define
    	
        $filesgrid[] = $rowf;
    }
    
// Get the general settings out the database
$resultr = $lsdb->query('SELECT id, title FROM '.DB_PREFIX.'jrc_responses');
    while ($rowr = $resultr->fetch_assoc()) {
    	// collect each record into a define
    	
        $responsegrid[] = $rowr;
    }
    
$allstuff .= "\$responsegserialize = '".base64_encode(gzcompress(serialize($responsegrid)))."';\n\n\$LV_RESPONSES = unserialize(gzuncompress(base64_decode(\$responsegserialize)));\n\n";
    
$allstuff .= "\$filesgserialize = '".base64_encode(gzcompress(serialize($filesgrid)))."';\n\n\$LV_FILES = unserialize(gzuncompress(base64_decode(\$filesgserialize)));\n\n";
    
$allstuff .= "?>";
        
LS_base::lsWriteinCache($cachestufffile, $allstuff, '');

}

// Now include the created definefile
include_once $cachestufffile;

// timezone from server
date_default_timezone_set(LS_TIMEZONESERVER);
$lsdb->query('SET time_zone = "'.date("P").'"');

// Check if https is activated
if (LS_SITEHTTPS) {
	define('BASE_URL', 'https://' . FULL_SITE_DOMAIN . _APP_MAIN_DIR . '/');
} else {
	define('BASE_URL', 'http://' . FULL_SITE_DOMAIN . _APP_MAIN_DIR . '/');
}
?>