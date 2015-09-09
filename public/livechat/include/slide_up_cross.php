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

if (!file_exists('../config.php')) die('include/[slide_up.php] config.php not exist');
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
if (is_numeric($_GET['did'])) $dep = '&amp;dep='.$_GET['did'];
if (is_numeric($_GET['oid'])) $dep .= '&amp;opid='.$_GET['oid'];

// Now let's check if we want to hide the chat when offline
$chi = false;
$onoff = (online_operators($LV_DEPARTMENTS, $_GET['did'], $_GET['oid']) ? true : false);
if ($_GET['chi'] == 1) $chi = !$onoff;

switch ($_GET['page']) {
	
	case 'status_maxim':
		
		if (isset($_SESSION['convid']) && isset($_SESSION['jrc_userid'])) {
		
			$lpdata = array("status" => true, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=chat&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>');
		
		} else {
		
			if ($onoff) {
			
				$lpdata = array("status" => false, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=start&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>');
			
			} else {
			
				$lpdata = array("status" => false, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=contact&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>');
			
			}
		
		}
		
		
		
		exit($_GET['callback'] . '('.json_encode($lpdata).')');
	
	break;
	
	case 'load-popup':
	
		$visited = false;
	
		if (isset($_COOKIE["activation"])) $visited = true;
		
		if (isset($_SESSION['chatbox_redirected'])) {
			unset($_SESSION['chatbox_redirected']);
			$lpdata = '';
		} else {
	
			if (!isset($_SESSION['convid']) && !isset($_SESSION['jrc_userid'])) {
			
				if ($onoff) {
				
					$lpdata = array("status" => false, "onoff" => $onoff, "chi" => $chi, "visited" => $visited, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=start&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>');
				
				} else {
				
					$lpdata = array("status" => false, "onoff" => $onoff, "chi" => $chi, "visited" => $visited, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=contact&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>');
				
				}
				
			} else {
			
				$lpdata = array("status" => true, "onoff" => $onoff, "chi" => $chi, "visited" => $visited, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=chat&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>');
			
			}
		
		}
		
		exit($_GET['callback'] . '('.json_encode($lpdata).')');
	
	break;
	
	case 'get-session':
	
		$result = $lsdb->query('SELECT session FROM '.DB_PREFIX.'buttonstats WHERE sessionid = "'.session_id().'" LIMIT 1');
		$row = $result->fetch_assoc();
	
		exit($_GET['callback'] . '('.json_encode(array("session" => $row["session"])).')');
	
	default:
	
		return false;

}