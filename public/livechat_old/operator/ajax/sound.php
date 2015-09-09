<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1998 03:10:00 GMT");

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

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['idhash'])) die("Nothing to see here");

if (!is_numeric($_POST['id'])) die("There is no such user!");

$result = $lsdb->query('SELECT answered, updated FROM '.DB_PREFIX.'jrc_sessions WHERE status = 1');

if ($lsdb->affected_rows > 0) {

	while ($row = $result->fetch_assoc()) {
			
			$newConv = 0;
		
			// check for new conversations
			if($row['answered'] == 0) {
				$newConv = 1;
			}
			if($row['updated'] > $row['answered']) {
				$newConv = 2;
			}		
	}
	
	echo json_encode(array('newc' => $newConv));
} else {

	echo json_encode(array('newc' => 0));
}
?>