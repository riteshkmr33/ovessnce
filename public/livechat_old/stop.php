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
if (!defined('LS_PREVENT_ACCESS')) die('You cannot access this file directly.');

// buffer flush
ob_start();

// Start the session
session_start();

if (!empty($page1) && is_numeric($page1)) {

	// check to see if conversation is to be stored
	$result = $lsdb->query('SELECT convid, name, email, contact FROM '.DB_PREFIX.'jrc_sessions WHERE convid = "'.smartsql($page1).'"');
	
	if ($lsdb->affected_rows > 0) {
	
		$row = $result->fetch_assoc();

		$lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET status = 0, ended = "'.time().'"  WHERE convid = "'.$row['convid'].'"');
		
		$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET 
		name = "'.smartsql($_SESSION['jrc_name']).'",
		message = "'.smartsql($tl['general']['g16']).'",
		user = "'.smartsql($_SESSION['jrc_userid']).'",
		convid = "'.$row['convid'].'",
		time = NOW(),
		class = "notice"');

	session_destroy();
	
	}
}

ob_flush();

?>

<script type="text/javascript">
javascript:window.close();
</script>