<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1980 03:10:00 GMT");

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

if (!file_exists('../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../config.php';

if(!$_SERVER['HTTP_X_REQUESTED_WITH'] || !isset($_SESSION['jrc_userid'])) die("Nothing to see here");

// Import the language file
if ($BT_LANGUAGE && file_exists(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini')) {
    $tl = parse_ini_file(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'lang/en.ini', true);
}

// Get the special lang var once for the time
define('LS_DAY', $tl['general']['g17']);
define('LS_HOUR', $tl['general']['g18']);
define('LS_MINUTE', $tl['general']['g19']);
define('LS_MULTITIME', $tl['general']['g20']);
define('LS_AGO', $tl['general']['g21']);

if (!$_POST['msg']) die(json_encode(array("status" => 0, "html" => $tl['error']['e2'])));

if (is_numeric($_POST['conv'])) {

$result = $lsdb->query('SELECT * FROM '.DB_PREFIX.'sessions WHERE userid = "'.smartsql($_POST['userid']).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();
	
		define('BASE_URL_IMG', str_replace('include/', '', BASE_URL));
		
		$message = strip_tags($_POST['msg']);
		
		$message = filter_var($message, FILTER_SANITIZE_STRING);
		
		$message = trim($message);
		
		$message = nl2br(replace_urls($message));
		
		if (LS_SMILIES) {
	
			require_once '../class/class.smileyparser.php';	
			
			// More dirty custom work and smiley parser
			$smileyparser = new LS_smiley(); 
			$message = $smileyparser->parseSmileytext($message);
		
		}
		
		if ($row['status'] && $message != "") {
		
			$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
			name = "'.smartsql($_POST['name']).'",
			message = "'.smartsql($message).'",
			user = "'.smartsql($_POST['userid']).'",
			convid = "'.smartsql($_POST['conv']).'",
			time = NOW(),
			class = "user"');
			
			$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
			updated = "'.time().'",
			u_typing = 0
			WHERE userid = "'.smartsql($_POST['userid']).'"');
			
			die(json_encode(array("status" => 1, "html" => '<li class="list-group-item user"><span class="response_sum">'.LS_base::lsTimesince(time(), LS_DATEFORMAT, LS_TIMEFORMAT).' '.$_POST['name'].' '.$tl['general']['g14'].' :</span><br />'.stripcslashes($message).'</li>')));
		
		} elseif (!$row['status'] && !$row['hide']) {
		
			$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
			name = "'.smartsql($_POST['name']).'",
			message = "'.smartsql($message).'",
			user = "'.smartsql($_POST['userid']).'",
			convid = "'.smartsql($_POST['conv']).'",
			time = NOW(),
			class = "user"');
			
			$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
			updated = "'.time().'",
			ended = 0,
			status = 1,
			u_typing = 0
			WHERE userid = "'.smartsql($_POST['userid']).'"');
			
			die(json_encode(array("status" => 1, "html" => '<li class="list-group-item user"><span class="response_sum">'.LS_base::lsTimesince(time(), LS_DATEFORMAT, LS_TIMEFORMAT).' '.$_POST['name'].' '.$tl['general']['g14'].' :</span><br />'.stripcslashes($message).'</li>')));
			
		} elseif (!$row['status']) {
		
			$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
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
			
			die(json_encode(array("status" => 1, "html" => '<li class="list-group-item notice"><span class="response_sum">'.LS_base::lsTimesince(time(), LS_DATEFORMAT, LS_TIMEFORMAT).' '.$_POST['name'].' '.$tl['general']['g14'].' :</span><br />'.stripcslashes($tl['general']['g13']).'</li>')));
			
		} else {
		
			die(json_encode(array("status" => 0, "html" => $tl['error']['e2'])));
		}
		
		
	}
}
?>