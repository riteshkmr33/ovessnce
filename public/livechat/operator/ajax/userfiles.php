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

if (!file_exists('../../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../../config.php';

if(!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if (!is_numeric($_POST['id'])) die("There is no such user!");

$result = $lsdb->query('SELECT sendfiles FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($_POST['id']).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();

	$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET sendfiles = IF (sendfiles = 1, 0, 1)  WHERE id = "'.smartsql($_POST['id']).'"');
	
	if ($row['sendfiles'] == 1) {

		echo json_encode(array('status' => 1));
		
	} else {
	
		echo json_encode(array('status' => 0));
	}

}

?>