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

if (!file_exists('../../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../../config.php';

if ($_SESSION['lc_ulang'] && file_exists(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'operator/lang/en.ini', true);
}

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die(json_encode(array('status' => 0, "html" => $tl['general']['g79'])));

if ($_POST['conv'] == "open" || (!is_numeric($_POST['id']) && !is_numeric($_POST['uid']))) die(json_encode(array('status' => 0, "html" => $tl['general']['g79'])));

$message = trim($_POST['msg']);

if (empty($message)) die(json_encode(array('status' => 0, "html" => $tl['error']['e1'])));

$result = $lsdb->query('SELECT * FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($_POST['id']).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();
	
		define('BASE_URL_IMG', str_replace('operator/ajax/', '', BASE_URL));
		
		$message = strip_tags($message);
		
		$message = filter_var($message, FILTER_SANITIZE_STRING);
		
		$message = trim($message);
		
		$message = replace_urls(nl2br($message));
		
		if (LS_SMILIES) {
	
			require_once '../../class/class.smileyparser.php';	
			
			// More dirty custom work and smiley parser
			$smileyparser = new LS_smiley(); 
			$message = $smileyparser->parseSmileytext($message);
			
		}

		if ($row['status'] == "closed" && !$row['hide']) {
			$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 1, updated = "'.$row['updated'].'" WHERE id = "'.$_POST['id'].'"');
		}
		
		if (!$row['hide']) {
		
			$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
			name = "'.smartsql($_POST['oname']).'",
			message = "'.smartsql($message).'",
			user = "'.smartsql($_POST['uid'].'::'.$_POST['uname']).'",
			convid = "'.$_POST['id'].'",
			time = NOW(),
			class = "admin"');
			
			$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
			answered = "'.time().'",
			o_typing = 0
			WHERE id = "'.$_POST['id'].'"');
			
			die(json_encode(array('status' => 1)));
			
		} elseif ($row['hide']) {
		
			$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
			name = "'.smartsql($_POST['oname']).'",
			message = "'.smartsql($tl['general']['g64']).'",
			convid = "'.$_POST['id'].'",
			class = "notice"');
			
			die(json_encode(array('status' => 1)));
			
		} else {
	
			die(json_encode(array('status' => 0, "html" => $tl['error']['e1'])));
		}
	}
?>