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

if (!file_exists('../../config.php')) die('ajax/[response.php] config.php not exist');
require_once '../../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if (!is_numeric($_POST['id'])) die(json_encode(array('status' => 0, "html" => "")));

$result = $lsdb->query('SELECT message FROM '.DB_PREFIX.'responses WHERE id = "'.smartsql($_POST['id']).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();
	
	if (is_numeric($_POST['conv'])) {
	
		$resultc = $lsdb->query('SELECT name FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($_POST['conv']).'"');
		$rowc = $resultc->fetch_assoc();
	
		$phold = array("%operator%","%client%","%email%");
		$replace   = array($_POST["oname"], $rowc["name"], LS_EMAIL);
		
		$message = str_replace($phold, $replace, $row['message']);
	
	} else {
		$message = $row['message'];
	}
	
	echo json_encode(array('status' => 1, "html" => $message));

}

?>