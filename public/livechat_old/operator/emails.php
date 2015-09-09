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

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('LS_OPERATOR_PREVENT_ACCESS')) die('You cannot access this file directly.');

// Check if the user has access to this file
if (!LS_USERID_RHINO || !LS_OPERATORACCESS) {
    ls_redirect(BASE_URL);
}

// The DB connections data
require_once '../class/class.export.php';

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'jrc_sessions';

switch ($page1) {
  	case 'export':
  	
  		$sql = 'SELECT name, email FROM '.$lstable.' WHERE email REGEXP "^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$" GROUP BY email ORDER BY id DESC';
  		$result = $lsdb->query($sql);
  		if ($lsdb->affected_rows > 0) {
  		while ($row = $result->fetch_assoc()) {
  		        // collect each record into $_data
  		        $lsdata[] = $row;
  		    }
  		}
  		    
  		emailExport::createFile($lsdata);
  	    
  	break;
	default:
	
		$sql = 'SELECT id, name, email FROM '.$lstable.' WHERE email REGEXP "^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$" GROUP BY email ORDER BY id DESC';
		$result = $lsdb->query($sql);
		if ($lsdb->affected_rows > 0) {
		while ($row = $result->fetch_assoc()) {
		        // collect each record into $_data
		        $lsdata[] = $row;
		    }
		}
		
		$CEMAILS_ALL = $lsdata;
		// Call the template
		$template = 'emails.php';
}
?>