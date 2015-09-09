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

if (!file_exists('../config.php')) die('include/[typing.php] config.php not exist');
require_once '../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['jrc_userid'])) die("Nothing to see here");

if (is_numeric($_POST['conv'])) {

if ($_POST['status'] == 1) {
	$result = $lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET u_typing = 1 WHERE convid = "'.smartsql($_POST['conv']).'"');
} else {
	$result = $lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET u_typing = 0 WHERE convid = "'.smartsql($_POST['conv']).'"');
}

if ($result) {
	die(json_encode(array('status' => 1)));
}

} else {
	die(json_encode(array('status' => 0)));
}
?>