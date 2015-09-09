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
if (!LS_USERID_RHINO || !LS_ADMINACCESS) ls_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'sessions';

// Let's go on with the script
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$defaults = $_POST;
	    
	$result = $lsdb->query('UPDATE '.$lstable.' SET 
	notes = "'.smartsql($defaults['note']).'"
	WHERE id = '.smartsql($defaults['convid']));
	
	if ($result) {
		
		// Ajax Request
		if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
		
			header('Cache-Control: no-cache');
			die(json_encode(array('status' => 1, 'label' => "note")));
			
		} else {
		
	        ls_redirect($_SERVER['HTTP_REFERER']);
	    
	    }
	}

}

$LS_FORM_DATA = ls_get_data($page1, $lstable);

		
// Call the template
$template = 'notes.php';

?>