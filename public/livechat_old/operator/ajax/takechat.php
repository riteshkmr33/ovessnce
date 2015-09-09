<?php

/*======================================================================*\
|| #################################################################### ||
|| # Rhino Business 1.5                                               # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2014 Rhino All Rights Reserved.                        # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
|| #                  http://www.livesupportrhino.com                 # ||
|| #################################################################### ||
\*======================================================================*/

// Start the session
session_start();

if (!file_exists('../../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['idhash'])) die("Nothing to see here");

if (is_numeric($_POST['id']) && is_numeric($_POST['userid'])) {

if (file_exists(APP_PATH.'operator/lang/'.LS_LANG.'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    trigger_error('Translation file not found');
}

$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET 
name = "'.smartsql($_POST['oname']).'",
message = "'.smartsql($_POST['oname'].' '.$tl["general"]["g101"]).'",
user = "'.smartsql($_POST['userid'].'::'.$_POST['uname']).'",
convid = "'.$_POST['id'].'",
time = NOW(),
class = "admin"');

$result = $lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET answered = "'.time().'" WHERE convid = "'.smartsql($_POST['id']).'"');

if ($result) {
	echo json_encode(array('cid' => $_POST['id']));
}

} else {
	echo json_encode(array('cid' => 0));
}
?>