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

// Start the session
session_start();

if (!file_exists('../../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../../config.php';

if(!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['idhash'])) die("Nothing to see here");

if (!is_numeric($_POST['id'])) die("There is no such user!");

$result = $lsdb->query('SELECT available FROM '.DB_PREFIX.'jrc_user WHERE session = "'.smartsql(session_id()).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();

	$lsdb->query('UPDATE '.DB_PREFIX.'jrc_user SET available = IF (available = 1, 0, 1)  WHERE session = "'.smartsql(session_id()).'"');
	
	if ($row['available'] == 1) {

		echo json_encode(array('status' => 1));
		
	} else {
	
		echo json_encode(array('status' => 0));
	}

}

?>