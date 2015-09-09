<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1998 03:10:00 GMT");

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

if (!file_exists('../../config.php')) die('ajax/[usronline.php] config.php not exist');
require_once '../../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if (!is_numeric($_POST['id']) && !is_numeric($_POST['uid'])) die("There is no such thing!");
	
	$result = $lsdb->query('UPDATE '.DB_PREFIX.'buttonstats SET proactive = "'.smartsql($_POST['uid']).'", message = "'.smartsql($_POST['msg']).'", readtime = 0  WHERE id = "'.smartsql($_POST['id']).'"');
	
	if ($result) {
		echo json_encode(array('status' => 1));
	} else {
		echo json_encode(array('status' => 0));
	}
?>