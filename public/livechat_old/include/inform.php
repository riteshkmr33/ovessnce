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

if (!file_exists('../config.php')) {
    die('ajax/[available.php] config.php not exist');
}
require_once '../config.php';

if(!$_SERVER['HTTP_X_REQUESTED_WITH']) {
	die("Nothing to see here");
}

if (!isset($_POST['id']) && !isset($_SESSION['jrc_userid'])) die("There is no such user!");

$result = $lsdb->query('SELECT answered, updated, o_typing FROM '.DB_PREFIX.'jrc_sessions WHERE status = 1 AND userid = "'.smartsql($_POST['id']).'"');

$row = $result->fetch_assoc();

if ($lsdb->affected_rows > 0) {
			
			$newConv = 0;
			$showinput = 0;
		
			if ($row['answered'] > $row['updated']) {
				$newConv = 1;
			}
			
			if ($row['answered'] != 0) $showinput = 1;
	
	echo json_encode(array('newmsg' => $newConv, 'typing' => $row['o_typing'], 'showinput' => $showinput));
} else {

	echo json_encode(array('newmsg' => 0, 'typing' => $row['o_typing'], 'showinput' => $showinput));
}
?>