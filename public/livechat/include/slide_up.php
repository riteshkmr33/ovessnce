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

if (!file_exists('../config.php')) die('include/[slide_up.php] config.php not exist');
require_once '../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if (!is_numeric($_POST['uid'])) die("There is no such thing!");

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

// Get the department
$dep = '';
if (is_numeric($_POST['did'])) $dep = '&amp;dep='.$_POST['did'];
if (is_numeric($_POST['oid'])) $dep .= '&amp;opid='.$_POST['oid'];

// Now let's check if we want to hide the chat when offline
$chi = false;
$onoff = (online_operators($LV_DEPARTMENTS, $_POST['did'], $_POST['oid']) ? true : false);
if ($_POST['chi'] == 1) $chi = !$onoff;
	
switch ($_POST['page']) {
	
	case 'status_maxim':
		
		if (isset($_SESSION['convid']) && isset($_SESSION['jrc_userid'])) {
		
			die(json_encode(array("status" => true, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=chat&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>')));
		} else {
		
			if ($onoff) {
			
				die(json_encode(array("status" => false, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=start&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>')));
			
			} else {
			
				die(json_encode(array("status" => false, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=contact&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>')));
			
			}
		}
	
	break;
	
	case 'load-popup':
		
		if (isset($_SESSION['chatbox_redirected'])) {
			unset($_SESSION['chatbox_redirected']);
		} else {
	
			if (isset($_SESSION['convid']) && isset($_SESSION['jrc_userid'])) {
			
				die(json_encode(array("status" => true, "onoff" => $onoff, "chi" => $chi, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=chat&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>')));
				
				
			} else {
			
				if ($onoff) {
				
					die(json_encode(array("status" => false, "onoff" => $onoff, "chi" => $chi, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=start&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>')));
				
				} else {
				
					die(json_encode(array("status" => false, "onoff" => $onoff, "chi" => $chi, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace('include/', '', BASE_URL).'index.php?p=contact&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>')));
				
				}
			
			}
		
		}
	
	break;
	
	default:
	
		return false;

}