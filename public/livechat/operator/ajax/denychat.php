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

$JAK_CALL_ADMIN_USER = true;

if (!file_exists('../../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if (is_numeric($_POST['id']) && is_numeric($_POST['userid'])) {

// Now cancel the chat
$result = $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET denied = 1, deniedoid = "'.smartsql($_POST['userid']).'", status = 0, ended = "'.time().'" WHERE id = "'.smartsql($_POST['id']).'"');

if ($result) {
	echo json_encode(array('cid' => $_POST['id']));
}

} else {
	echo json_encode(array('cid' => 0));
}
?>