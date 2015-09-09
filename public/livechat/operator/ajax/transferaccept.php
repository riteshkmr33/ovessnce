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

if (!file_exists('../../config.php')) {
    die('ajax/[available.php] config.php not exist');
}
require_once '../../config.php';

if(!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) {
	die("Nothing to see here");
}

if (!is_numeric($_POST['convid']) && !is_numeric($_POST['userid'])) die("There is no such conversation!");

$result = $lsdb->query('SELECT id FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($_POST['convid']).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();
	
	if ($_POST['accept'] == 1) {
	
	// Now show the avatar from the operator
	$oimage = '';
	$result = $lsdb->query('SELECT picture FROM '.DB_PREFIX.'user WHERE id = "'.smartsql($_POST['userid']).'"');
	if ($lsdb->affected_rows > 0) {
	
		$row = $result->fetch_assoc();
		
		$oimage = '<img src="'.str_replace('operator/ajax/', '', BASE_URL).LS_FILES_DIRECTORY.$LS_FORM_DATA["picture"].'" alt="avatar" class="avatar" />';
	
		$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
		name = "'.smartsql($_POST['oname']).'",
		message = "'.smartsql($oimage.$_POST['oname'].' '.$tl["general"]["g121"]).'",
		user = "'.smartsql($_POST['userid'].'::'.$_POST['uname']).'",
		convid = "'.$_POST['id'].'",
		time = NOW(),
		class = "admin"');
	
	}

	$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET operatorid = "'.smartsql($_POST['userid']).'", transferid = 0, transfermsg = NULL  WHERE id = "'.smartsql($_POST['convid']).'"');
	
	} else {
		
		$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET transferid = 0, transfermsg = NULL  WHERE id = "'.smartsql($_POST['convid']).'"');
	
	}

	echo json_encode(array('status' => 1));

}
?>