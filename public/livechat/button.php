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

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('LS_PREVENT_ACCESS')) die('You cannot access this file directly.');

$referrer = selfURL();

if ($referrer) {

$image = verifyparam(isset($_GET['image']) ? "image" : "i", "/^\w+$/", "blue");
$lang = verifyparam(isset($_GET['language']) ? "language" : "lang", "/^[\w-]{2,5}$/", "");

$image_postfix = (isset($lv_users) && is_array($lv_users) && array_key_exists($_GET['user'], $lv_users) == true) ? "on" : "off";
$filename_png = "./".LS_FILES_DIRECTORY."/buttons/".$image."_".$image_postfix.".png";
$filename_gif = "./".LS_FILES_DIRECTORY."/buttons/".$image."_".$image_postfix.".gif";
$filename_jpg = "./".LS_FILES_DIRECTORY."/buttons/".$image."_".$image_postfix.".jpg";

if (file_exists($filename_gif)) {
	$filename = $filename_gif;
} elseif (file_exists($filename_jpg)) {
	$filename = $filename_jpg;
} else {
	$filename = $filename_png;
}

// Now start the session for better user controll
session_start();

// Set the cookie
setcookie("activation", "visited", time() + LS_COOKIE_TIME, LS_COOKIE_PATH);

if (isset($_COOKIE["activation"]) || session_id()) {

if (!isset($_SESSION['rlbid'])) {

	if (isset($_COOKIE['rlbid'])){
	   $_SESSION['rlbid'] = $_COOKIE['rlbid'];
	} else {
		$salt = rand(100, 99999);
		$rlbid = $salt.time();
		setcookie("rlbid", $rlbid, time() + 31536000, LS_COOKIE_PATH);
		$_SESSION['rlbid'] = $rlbid;
	}
	
}

$depid = 0;
$opid = 0;
if (is_numeric($page3)) $depid = $page3;
if (is_numeric($page4)) $opid = $page4;
	
	// Update database first to see who is online!
	$lsdb->query('UPDATE '.DB_PREFIX.'buttonstats SET depid = "'.smartsql($depid).'", opid = "'.smartsql($opid).'", hits = hits + 1, referrer = "'.smartsql($referrer).'", sessionid = "'.smartsql(session_id()).'", ip = "'.smartsql($ipa).'", lasttime = NOW() WHERE session = "'.smartsql($_SESSION['rlbid']).'" LIMIT 1');
	
	if ($lsdb->affected_rows == 0) {
		
		// get client information
		$depid = 0;
		$ua = getBrowser($_SERVER['HTTP_USER_AGENT']);
		$clientsystem = $ua['platform'].' - '.$ua['name'] . " " . $ua['version'];
	
		$lsdb->query('INSERT INTO '.DB_PREFIX.'buttonstats SET 
		depid = "'.smartsql($depid).'",
		opid = "'.smartsql($opid).'",
		referrer = "'.smartsql($referrer).'",
		firstreferrer = "'.smartsql($referrer).'",
		agent = "'.smartsql($clientsystem).'",
		hits = 1,
		ip = "'.smartsql($ipa).'",
		session = "'.smartsql($_SESSION['rlbid']).'",
		sessionid = "'.smartsql(session_id()).'",
		time = NOW(),
		lasttime = NOW()');
	
	}
	
	if (isset($_SESSION['jrc_userid']) && isset($_SESSION['convid'])) {
		// insert new referrer
		$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
		name = "'.$tl["general"]["g56"].'",
		message = "'.$tl["general"]["g55"].smartsql($referrer).'",
		convid = "'.smartsql($_SESSION['convid']).'",
		time = NOW(),
		class = "notice",
		plevel = 2');
		
		$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
		creferrer = "'.smartsql($referrer).'",
		updated = "'.time().'",
		u_typing = 0
		WHERE session = "'.smartsql($_SESSION['rlbid']).'"');
	}

}

ob_clean();
$fp = fopen($filename, 'rb') or die("no image");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: image/png");
header("Content-Length: ".filesize($filename));
if(function_exists('fpassthru')){
	@fpassthru($fp);
} else {
	while( (!feof($fp)) && (connection_status()==0)){
		print(fread($fp, 1024*8));
		flush();
	}
	fclose($fp);
}
exit;
}
?>