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

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['rlbid']) && is_numeric($_POST['check_proactive'])) die("Nothing to see here");

if (!file_exists('../config.php')) die('include/[proactive.php] config.php not exist');
require_once '../config.php';

// Import the language file
if ($BT_LANGUAGE && file_exists(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini')) {
    $tl = parse_ini_file(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini', true);
    $lang = $BT_LANGUAGE;
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'lang/'.LS_LANG.'.ini', true);
	$lang = LS_LANG;
} else {
    $tl = parse_ini_file(APP_PATH.'lang/en.ini', true);
    $lang = 'en';
}

$dep = '';
if (is_numeric($_POST['did'])) $dep = '&amp;dep='.$_POST['did'];
if (is_numeric($_POST['oid'])) $dep .= '&amp;opid='.$_POST['oid'];

switch ($_POST['page']) {

	case 'check':
	
		$proactive = true;
		$lvs_departments = true;
		$newConv = 0;
		$newMSG = '';
		
		if ($_POST['slide']) $lvs_departments = (online_operators($LV_DEPARTMENTS, $_POST['did'], $_POST['oid']) ? true : false);
		
		if (isset($_SESSION['convid']) && isset($_SESSION['jrc_userid'])) {
		
			// Update the status for better user handling
			$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET u_status = "'.time().'" WHERE userid = "'.smartsql($_SESSION['jrc_userid']).'" AND status != "closed"');
			
			$result = $lsdb->query('SELECT t1.answered, t1.updated FROM '.DB_PREFIX.'sessions AS t1 WHERE userid = "'.smartsql($_SESSION['jrc_userid']).'" AND status != "closed"');
			
			$newConv = 0;
			
			if ($lsdb->affected_rows > 0) {
			
				$row = $result->fetch_assoc();
				
				if ($row['answered'] > $row['updated']) {
					$newConv = 1;
					$newMSG = $tl["general"]["g22"];
				}
			}
			
		}
		
		if ($lvs_departments) {
		
		// Check if we have an auto proactive
		if (!isset($_COOKIE['proactive'])) {
			
			$result = $lsdb->query('SELECT t1.message, t1.showalert, t1.wayin, t1.wayout FROM '.DB_PREFIX.'autoproactive AS t1 LEFT JOIN '.DB_PREFIX.'buttonstats AS t2 ON (t1.path = t2.referrer) WHERE t2.session = "'.smartsql($_SESSION['rlbid']).'" AND t2.hits >= t1.visitedsites AND UNIX_TIMESTAMP(t2.lasttime) <= (UNIX_TIMESTAMP() - t1.timeonsite)');
			
			if ($lsdb->affected_rows > 0) {
			
				$row = $result->fetch_assoc();
				
				setcookie("proactive", 1, time() + (86400 * 3), LS_COOKIE_PATH);
				
				$proactive = false;
				
				$result = $lsdb->query('UPDATE '.DB_PREFIX.'buttonstats SET proactive = 999, message = "'.smartsql($row['message']).'", readtime = 0  WHERE session = "'.smartsql($_SESSION['rlbid']).'"');
				
				exit(json_encode(array('proactive' => true, 'offline' => false, 'message' => $row['message'], 'showalert' => $row['showalert'], 'wayin' => $row['wayin'], 'wayout' => $row['wayout'], "newmsg" => $newConv, "newmsghtml" => $newMSG)));
				
			}
		}
		
		if ($proactive) {
		
			// Check if we have an manual proactive
			$result = $lsdb->query('SELECT message FROM '.DB_PREFIX.'buttonstats WHERE proactive = 1 AND session = "'.smartsql($_SESSION['rlbid']).'" AND readtime = 0');
			
			if ($lsdb->affected_rows > 0) {
			
				$row = $result->fetch_assoc();
				
				exit(json_encode(array('proactive' => true, 'offline' => false, 'message' => $row['message'], 'showalert' => LS_PRO_ALERT, 'wayin' => LS_PRO_WAYIN, 'wayout' => LS_PRO_WAYOUT, "newmsg" => $newConv, "newmsghtml" => $newMSG)));
				
			} else {
				
				exit(json_encode(array('proactive' => false, 'offline' => false, "newmsg" => $newConv, "newmsghtml" => $newMSG)));
				
			}
		}
		
		} else {
			
			exit(json_encode(array('proactive' => false, 'offline' => true, "newmsg" => $newConv, "newmsghtml" => $newMSG)));
			
		}
	
	break;
	
	case 'close':
	
		setcookie("proactive", 1, time() + (86400 * 3), LS_COOKIE_PATH);
	
		$result = $lsdb->query('UPDATE '.DB_PREFIX.'buttonstats SET readtime = 1 WHERE session = "'.smartsql($_SESSION['rlbid']).'" AND readtime = 0');
		
		if ($result) {
			exit(json_encode(array('proactive' => true)));
		} else {
			exit(json_encode(array('proactive' => false)));
		}
	
	break;
	
	case 'open':
	
		setcookie("proactive", 1, time() + (86400 * 3), LS_COOKIE_PATH);
	
		$result = $lsdb->query('UPDATE '.DB_PREFIX.'buttonstats SET readtime = 2 WHERE session = "'.smartsql($_SESSION['rlbid']).'" AND readtime = 0');
		
		if ($result) {
		
			$web_url = str_replace('include/', '', BASE_URL);
			
			exit(json_encode(array('openchat' => true, 'url' => $web_url, 'windowname' => LS_TITLE, "form" => '<iframe seamless="seamless" class="jrc_ichat" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=start&amp;lang='.$lang.'&amp;slide='.$_POST['slide'].$dep.'"></iframe>')));
		} else {
			exit(json_encode(array('openchat' => false)));
		}
	
	break;
	
	default:
	
		exit(json_encode(array('proactive' => false, 'offline' => true)));
	
}
?>