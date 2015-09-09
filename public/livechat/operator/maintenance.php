<?php

/*======================================================================*\
|| #################################################################### ||
|| # Rhino Socket 2.0                                                 # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2014 Rhino All Rights Reserved.                        # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
|| #                  http://www.livesupportrhino.com                 # ||
|| #################################################################### ||
\*======================================================================*/

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('LS_ADMIN_PREVENT_ACCESS')) die('You cannot access this file directly.');

// Check if the user has access to this file
if (!ls_get_access("maintenance", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) ls_redirect(BASE_URL);

include_once('dbbackup/class.dbie.php');

$dbimpexp = new dbimpexp();

// Flag to select step
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $defaults = $_POST;
    
if (isset($defaults['delCache'])) {
	
	// Now let us delete the all the cache file
	$cacheallfiles = '../'.LS_CACHE_DIRECTORY.'/';
	$msfi = glob($cacheallfiles."*.php");
	if ($msfi) foreach ($msfi as $filen) {
	    if (file_exists($filen)) unlink($filen);
	}
	
	ls_redirect(BASE_URL.'index.php?p=success');

}

if (isset($defaults['export'])) {
	
	require_once '../class/class.export.php';

    $result = $lsdb->query('SELECT name, email FROM '.DB_PREFIX.'sessions WHERE email LIKE "%@%.%" GROUP BY email ORDER BY id DESC');
    if ($lsdb->affected_rows > 0) {
    while ($row = $result->fetch_assoc()) {
            // collect each record into $_data
            $lsdata[] = $row;
        }
    }
        
    if (is_array($lsdata)) emailExport::createFile($lsdata);
}

// Execute Optinos
if (isset($defaults['download'])) {

    $dbimpexp->addValue('download_path', '')->addValue('download', true)->addValue('file_name', 'backup_jrl_'.date("y_m_d", time()).'.xml')->export();
}

if (isset($defaults['import'])) {
    
    	$xmlfiledb = $_FILES['uploaddb']['tmp_name'];
    	
    	$filename = $_FILES['uploaddb']['name']; // original filename
    	$tmpf = explode(".", $filename);
    	$ls_xtension = end($tmpf);
    	
    	if (!empty($xmlfiledb) && $ls_xtension == "xml") {
    		
    		$dbimpexp->addValue('import_path', $xmlfiledb)->import();
    		
    		ls_redirect(BASE_URL.'index.php?p=success');
    		
    	} else {		
    	    ls_redirect(BASE_URL.'index.php?p=error&sp=no-data');
    	}
    	
}

if (isset($defaults['optimize'])) {
	
	$dbimpexp->optimize();
	
	ls_redirect(BASE_URL.'index.php?p=success');

}

}

// Call the template
$template = 'maintenance.php';

?>