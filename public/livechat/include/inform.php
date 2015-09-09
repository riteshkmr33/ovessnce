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

if (!file_exists('../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH']) die("Nothing to see here");

if (!isset($_POST['id']) && !isset($_SESSION['jrc_userid'])) die("There is no such user!");

// Import the language file
if ($BT_LANGUAGE && file_exists(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini')) {
    $tl = parse_ini_file(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'lang/en.ini', true);
}

$otyping = false;
$knockknock = false;
$opern = $tl['general']['g59'];

$result = $lsdb->query('SELECT t1.id, t1.operatorid, t1.initiated, t1.answered, t1.updated, t1.knockknock, t1.sendfiles, t1.o_typing, t1.denied, t2.name, t2.picture FROM '.DB_PREFIX.'sessions AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON(t1.operatorid = t2. id) WHERE userid = "'.smartsql($_POST['id']).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();
	
	// Get the knock knock
	if ($row['knockknock'] == 1) $knockknock = $tl["general"]["g22"];
	
	// Update the status for better user handling
	$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET u_status = "'.time().'", knockknock = 0 WHERE id = "'.$row['id'].'"');
	
	if ($row['denied'] == 1) {
		
		$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
		name = "'.smartsql($_SESSION['jrc_name']).'",
		message = "'.smartsql($tl['general']['g57']).'",
		user = "'.smartsql($_SESSION['jrc_userid']).'",
		convid = "'.$row['id'].'",
		time = NOW(),
		class = "ended"');
		
		die(json_encode(array('redirect_c' => true)));
		
	}
	
	if ($row['answered'] == 0 && !$_SESSION['chat_wait'] && $row['initiated'] < (time() - 60)) {
		
		$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
		name = "'.smartsql($tl["general"]["g56"]).'",
		message = "'.smartsql($tl["general"]["g69"]).'",
		convid = "'.$row['id'].'",
		time = NOW(),
		class = "admin"');
		
		// session that we sent the waiting message
		$_SESSION['chat_wait'] = 'sent';
		
	}
	
	if ($row['answered'] == 0 && $_SESSION['chat_wait'] == "sent" && $row['initiated'] < (time() - 180)) {
		
		$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
		name = "'.smartsql($tl["general"]["g56"]).'",
		message = "'.smartsql($tl["general"]["g70"]).'",
		convid = "'.$row['id'].'",
		time = NOW(),
		class = "admin"');
		
		$_SESSION['chat_wait'] = 'sent2';
		
	}
	
	if ($row['answered'] == 0 && $_SESSION['chat_wait'] == "sent2" && $row['initiated'] < (time() - 480) && LS_WAIT_MESSAGE3 == 1) {
	
		$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 0, fcontact = 1, ended = "'.time().'"  WHERE id = "'.$row['id'].'"');
		
		$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
		name = "'.smartsql($_SESSION['jrc_name']).'",
		message = "'.smartsql($tl['general']['g57']).'",
		user = "'.smartsql($_SESSION['jrc_userid']).'",
		convid = "'.$row['id'].'",
		time = NOW(),
		class = "ended"');
		
		die(json_encode(array('redirect_c' => true)));
		
	}
			
			$newConv = 0;
			$scrollNow = 0;
			$operatorid = 0;
			$showinput = 0;
		
			if ($row['answered'] > $row['updated']) $newConv = 1;
			
			if ($row['answered'] > (time() - 6)) $scrollNow = 1;
			
			if ($row['operatorid']) $operatorid = 1;
			
			if ($row['o_typing']) $otyping = str_replace("%s", $row['name'], $tl["general"]["g37"]);
			
			if ($row['name']) $opern = $tl["general"]["g52"].': '.$row['name'];
			
			if ($row['answered'] != 0) $showinput = 1;
	
	die(json_encode(array('redirect_c' => false, 'knockknock' => $knockknock, 'operator' => $operatorid, 'newmsg' => $newConv, 'scrollnow' => $scrollNow, 'files' => $row['sendfiles'], 'typing' => $otyping, 'oname' => $opern, 'opicture' => $row['picture'], 'showinput' => $showinput)));
} else {

	die(json_encode(array('redirect_c' => false, 'knockknock' => $knockknock, 'operator' => 0, 'newmsg' => 0, 'scrollnow' => 0, 'files' => 0, 'typing' => $otyping, 'oname' => false, 'opicture' => false, 'showinput' => false)));
}
?>