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

if (!is_numeric($_POST['id'])) die("There is no such message!");

$result = $lsdb->query('SELECT name, path FROM '.DB_PREFIX.'files WHERE id = "'.smartsql($_POST['id']).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();
	
	$message = '<a href="'.str_replace('operator/ajax/', '', BASE_URL).$row['path'].'" target="blank">'.$row['name'].'</a>';
	
	$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
	name = "'.smartsql($_POST['oname']).'",
	message = "'.smartsql($message).'",
	user = "'.smartsql($_POST['uid'].'::'.$_POST['uname']).'",
	convid = "'.$_POST['conv'].'",
	time = NOW(),
	class = "download"');
	
	$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
	answered = "'.time().'",
	o_typing = 0
	WHERE id = "'.$_POST['conv'].'"');
	
	echo json_encode(array('status' => 1));

}
?>