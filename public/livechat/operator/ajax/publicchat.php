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

if (!file_exists('../../config.php')) die('ajax/[usronline.php] config.php not exist');
require_once '../../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if ($_SESSION['lc_ulang'] && file_exists(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'operator/lang/en.ini', true);
}

if (!is_numeric($_POST['uid'])) die("There is no such thing!");

$sent_time = time() - OPERATOR_CHAT_EXPIRE;
	
switch ($_POST['page']) {
	
	case 'load-msg':
		
			// Load Messages
			$resultm = $lsdb->query('SELECT t1.id, t1.fromid, t1.message, t1.system_message, t1.sent, t2.username FROM '.DB_PREFIX.'operatorchat AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON (t1.fromid = t2.id) WHERE msgpublic = 1 AND sent > "'.$sent_time.'" ORDER BY sent ASC');	
			
			if ($lsdb->affected_rows > 0) {
			
				$chatmsg = '<ul class="list-group">';
			
				while ($rowm = $resultm->fetch_assoc()) {
				
					//print messages
					if ($rowm['system_message'] != 'no') {
						
						$chatmsg .= '<li class="list-group-item system">'. stripcslashes($rowm['message']).'</li>';
															
					} elseif ($rowm['fromid'] != $_POST['uid']) {
					
						$chatmsg .= '<li class="list-group-item"><strong>'.$rowm['username'].':</strong> '.$rowm['message'].'</li>';
					
					} else {
						$chatmsg .= '<li class="list-group-item me"><strong>'.$tl["general"]["g140"].'</strong>'.$rowm['message'].'</li>';
					}	
					
					$last_msg = $rowm['sent'];
				}	
				
				//print last message time if older than 2 mins
				$math = time() - $last_msg;
				if ($math > 120) {
					$chatmsg .= '<li class="list-group-item system">'.str_replace("%s", date("H:i", $last_msg), $tl["general"]["g141"]).'</li>';
				}
					
			}
			
			$chatmsg .= '</ul>';
					
		echo $chatmsg;
	
	break;
	
	case 'send-msg':
	
		if (empty($_POST['message'])) {
			echo $tl['error']['e1'];
		} else {
		
			$message = trim($_POST['message']);
			
			$message = filter_var($message, FILTER_SANITIZE_STRING);
		
			$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'operatorchat SET fromid = "'.smartsql($_POST['uid']).'", toid = 0, message = "'.smartsql($message).'", sent = "'.time().'", received = 1, msgpublic = 1');
		
			if ($result) {
				echo 'success';
			} else {
				echo $tl['error']['e1'];
			}
			
		}
				
	break;
	
	default:
	
		return false;

}