<?php

header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
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

function is_valid_callback($input)
{
    $identifier_syntax
      = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

    $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
      'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 
      'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 
      'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 
      'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 
      'private', 'public', 'yield', 'interface', 'package', 'protected', 
      'static', 'null', 'true', 'false');

    return preg_match($identifier_syntax, $input)
        && ! in_array(mb_strtolower($input, 'UTF-8'), $reserved_words);
}

function is_valid_callback2($input){
    return !preg_match( '/[^0-9a-zA-Z\$_]|^(abstract|boolean|break|byte|case|catch|char|class|const|continue|debugger|default|delete|do|double|else|enum|export|extends|false|final|finally|float|for|function|goto|if|implements|import|in|instanceof|int|interface|long|native|new|null|package|private|protected|public|return|short|static|super|switch|synchronized|this|throw|throws|transient|true|try|typeof|var|volatile|void|while|with|NaN|Infinity|undefined)$/', $input);
}

if (!is_valid_callback($_GET['callback']) || !is_valid_callback2($_GET['callback'])) {
	header('status: 400 Bad Request', true, 400);
} else {
	header('content-type: application/json; charset=utf-8');
}

if (!file_exists('../config.php')) die('include/[proactive_cross.php] config.php not exist');
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

// Get referrer
$referrer = selfURL();

$dep = '';
if (is_numeric($_GET['did'])) $dep = '&amp;dep='.$_GET['did'];
if (is_numeric($_GET['oid'])) $dep .= '&amp;opid='.$_GET['oid'];

switch ($_GET['page']) {

	case 'check':
	
		$proactive = true;
		$lvs_departments = true;
		$newConv = 0;
		$newMSG = '';
		
		if ($_GET['slide']) $lvs_departments = (online_operators($LV_DEPARTMENTS, $_GET['did'], $_GET['oid']) ? true : false);
		
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
			
			$result = $lsdb->query('SELECT t1.message FROM '.DB_PREFIX.'autoproactive AS t1 LEFT JOIN '.DB_PREFIX.'buttonstats AS t2 ON (t1.path = t2.referrer) WHERE t2.session = "'.smartsql($_SESSION['rlbid']).'"');
			
			$result = $lsdb->query('SELECT t1.message, t1.showalert, t1.wayin, t1.wayout FROM '.DB_PREFIX.'autoproactive AS t1 LEFT JOIN '.DB_PREFIX.'buttonstats AS t2 ON (t1.path = t2.referrer) WHERE t2.session = "'.smartsql($_SESSION['rlbid']).'" AND t2.hits >= t1.visitedsites AND UNIX_TIMESTAMP(t2.lasttime) <= (UNIX_TIMESTAMP() - t1.timeonsite)');
			
			if ($lsdb->affected_rows > 0) {
			
				$row = $result->fetch_assoc();
				
				setcookie("proactive", 1, time() + (86400 * 7), LS_COOKIE_PATH);
				
				$proactive = false;
				
				$result = $lsdb->query('UPDATE '.DB_PREFIX.'buttonstats SET proactive = 999, message = "'.$row['message'].'", readtime = 0  WHERE session = "'.smartsql($_SESSION['rlbid']).'"');
				
				$cdata = array('proactive' => true, 'offline' => false, 'message' => $row['message'], 'showalert' => $row['showalert'], 'wayin' => $row['wayin'], 'wayout' => $row['wayout'], "newmsg" => $newConv, "newmsghtml" => $newMSG);
				
				exit($_GET['callback'] . '('.json_encode($cdata).')');
				
			}
		}
		
		if ($proactive) {
		
			// Check if we have an manual proactive
			$result = $lsdb->query('SELECT message FROM '.DB_PREFIX.'buttonstats WHERE proactive != 0 AND session = "'.smartsql($_SESSION['rlbid']).'" AND readtime = 0');
			
			if ($lsdb->affected_rows > 0) {
			
				$row = $result->fetch_assoc();
				
				$cdata = array('proactive' => true, 'offline' => false, 'message' => $row['message'], 'showalert' => LS_PRO_ALERT, 'wayin' => LS_PRO_WAYIN, 'wayout' => LS_PRO_WAYOUT, "newmsg" => $newConv, "newmsghtml" => $newMSG);
				
				exit($_GET['callback'] . '('.json_encode($cdata).')');
				
			} else {
				
				$cdata = array('proactive' => false, 'offline' => false, "newmsg" => $newConv, "newmsghtml" => $newMSG);
				
				exit($_GET['callback'] . '('.json_encode($cdata).')');
				
			}
		}
		
		} else {
			
			$cdata = array('proactive' => false, 'offline' => true, "newmsg" => $newConv, "newmsghtml" => $newMSG);
			
			exit($_GET['callback'] . '('.json_encode($cdata).')');
			
		}
	
	break;
	
	case 'close':
	
		$result = $lsdb->query('UPDATE '.DB_PREFIX.'buttonstats SET readtime = 1 WHERE session = "'.smartsql($_SESSION['rlbid']).'" AND readtime = 0');
		
		if ($result) {
			$cldata = 1;
			exit($_GET['callback'] . '('.json_encode($cldata).')');
		} else {
			$cldata = 0;
			exit($_GET['callback'] . '('.json_encode($cldata).')');
		}
	
	break;
	
	case 'open':
	
		$result = $lsdb->query('UPDATE '.DB_PREFIX.'buttonstats SET readtime = 2 WHERE session = "'.smartsql($_SESSION['rlbid']).'" AND readtime = 0');
		
		if ($result) {
		
			$web_url = str_replace('include/', '', BASE_URL);
			
			$odata = array('openchat' => true, 'url' => $web_url, 'windowname' => LS_TITLE, 'form' => '<iframe seamless="seamless" class="jrc_ichat" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=start&amp;lang='.$lang.'&amp;slide='.$_GET['slide'].$dep.'"></iframe>');
			exit($_GET['callback'] . '('.json_encode($odata).')');
		} else {
			$odata = array('openchat' => false);
			exit($_GET['callback'] . '('.json_encode($odata).')');
		}
	
	break;
	
	default:
	
		$cdata = array('proactive' => false, 'offline' => true, "newmsg" => $newConv, "newmsghtml" => $newMSG);
	
		exit($_GET['callback'] . '('.json_encode($cdata).')');
	
}
?>