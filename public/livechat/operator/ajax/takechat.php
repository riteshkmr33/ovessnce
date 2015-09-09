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

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if (is_numeric($_POST['id']) && is_numeric($_POST['userid'])) {

if ($_SESSION['lc_ulang'] && file_exists(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'operator/lang/en.ini', true);
}

// Now show the avatar from the operator
$oimage = '';
$result = $lsdb->query('SELECT picture FROM '.DB_PREFIX.'user WHERE id = "'.smartsql($_POST['userid']).'"');
if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();
	
	if ($row["picture"]) $oimage = '<img src="'.str_replace('operator/ajax/', '', BASE_URL).LS_FILES_DIRECTORY.$row["picture"].'" alt="avatar" class="img-circle avatar" />';
	
}

$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
name = "'.smartsql($_POST['oname']).'",
message = "'.smartsql($oimage.$_POST['oname'].' '.$tl["general"]["g121"]).'",
user = "'.smartsql($_POST['userid'].'::'.$_POST['uname']).'",
convid = "'.smartsql($_POST['id']).'",
time = NOW(),
class = "admin"');

$result = $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET operatorid = "'.smartsql($_POST['userid']).'", operatorname = "'.smartsql($_POST['oname']).'", answered = "'.time().'" WHERE id = "'.smartsql($_POST['id']).'"');

if ($result) {
	echo json_encode(array('cid' => $_POST['id']));
}

} else {
	echo json_encode(array('cid' => 0));
}
?>