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

if(!isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if (is_numeric($_POST['sid'])) {

	// Now delete the record from the database
	$lsdb->query('DELETE FROM '.DB_PREFIX.'user_stats WHERE id = "'.smartsql($_POST['sid']).'"');

	echo json_encode(array('status' => 1));
} else {
	echo json_encode(array('status' => 0));
}

?>