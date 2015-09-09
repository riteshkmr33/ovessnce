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

if (is_numeric($_POST['available'])) {
	
	$lsdb->query('UPDATE '.DB_PREFIX.'user SET available = "'.smartsql($_POST['available']).'" WHERE session = "'.smartsql(session_id()).'"');
		
	die(json_encode(array('status' => $_POST['available'])));
}
?>