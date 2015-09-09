<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1980 03:10:00 GMT");

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

if (!file_exists('../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] || !isset($_SESSION['jrc_userid'])) die("Nothing to see here");

// Import the language file
if (file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
    $tl = parse_ini_file(APP_PATH.'lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'lang/en.ini', true);
}

if (!$_POST['msg']) die(json_encode(array("status" => 0, "html" => $tl['error']['e2'])));

if (is_numeric($_POST['conv'])) {

$result = $lsdb->query('SELECT * FROM '.DB_PREFIX.'jrc_sessions WHERE userid = "'.smartsql($_POST['userid']).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();
	
		define('BASE_URL_IMG', str_replace('include/', '', BASE_URL));
		
		$message = $_POST['msg'];
		
		$message = filter_var($message, FILTER_SANITIZE_STRING);
		
		$message = trim($message);
		
		$message = replace_urls($message);
		
		if (LS_SMILIES) {
	
			require_once '../class/class.smileyparser.php';	
			
			// More dirty custom work and smiley parser
			$smileyparser = new LS_smiley(); 
			$message = $smileyparser->parseSmileytext($message);
		}
		
		if ($row['status'] && $message != "") {
		
			$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET 
			name = "'.smartsql($_POST['name']).'",
			message = "'.smartsql($message).'",
			user = "'.smartsql($_POST['userid']).'",
			convid = "'.smartsql($_POST['conv']).'",
			time = NOW(),
			class = "user"');
			
			$lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET
			updated = "'.time().'",
			u_typing = 0
			WHERE userid = "'.smartsql($_POST['userid']).'"');
			
			echo json_encode(array("status" => 1, "html" => ""));
		
		} elseif (!$row['status'] && !$row['hide']) {
		
			$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET 
			name = "'.smartsql($_POST['name']).'",
			message = "'.smartsql($message).'",
			user = "'.smartsql($_POST['userid']).'",
			convid = "'.smartsql($_POST['conv']).'",
			time = NOW(),
			class = "user"');
			
			$lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET
			updated = "'.time().'",
			ended = 0,
			status = 1,
			u_typing = 0
			WHERE userid = "'.smartsql($_POST['userid']).'"');
			
			echo json_encode(array("status" => 1, "html" => ""));
			
		} elseif (!$row['status']) {
		
			$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET 
			name = "'.smartsql($_POST['name']).'",
			message = "'.smartsql($tl['general']['g13']).'",
			user = "'.smartsql($_POST['userid']).'",
			convid = "'.smartsql($_POST['conv']).'",
			time = NOW(),
			class = "notice"');
			
			$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
			updated = "'.time().'",
			ended = 0,
			u_typing = 0
			WHERE userid = "'.smartsql($_POST['userid']).'"');
			
			echo json_encode(array("status" => 1, "html" => ""));
			
		} else {
		
			echo json_encode(array("status" => 0, "html" => ""));
		}
		
		
	}
}
?>