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

	case 'status':
	
		if (isset($_POST['chatbox_status'])) {
			$_SESSION['chatbox_status'] = $_POST['chatbox_status'];
		} else { 
			unset($_SESSION['chatbox_status']);
		}
	
	break;
	
	case 'load-popup':
		
		//check if there is an unreceived message for current user
		$result = $lsdb->query('SELECT t1.fromid, t2.username FROM '.DB_PREFIX.'operatorchat AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON (t1.fromid = t2.id) WHERE t1.toid = "'.smartsql($_POST['uid']).'" AND t1.received = 0 GROUP BY t1.fromid LIMIT 0,1');
		
		if ($lsdb->affected_rows > 0) {
		
			$row = $result->fetch_assoc();		
			
			echo $row['fromid'].':#:'.$row['username'];
		
		} else {
			echo '0';
		}
	
	break;
	
	case 'typing':
	
		$result = $lsdb->query('SELECT typingstatus FROM '.DB_PREFIX.'operatortyping WHERE typingfrom = "'.smartsql($_POST['partner_id']).'" AND typingto = "'.smartsql($_POST['uid']).'" AND typingstatus = 1');
		
		if ($lsdb->affected_rows > 0) {
			echo $tl["general"]["g143"];
		} else {
			echo 0;
		}
	
	break;
	
	case 'load-msg':
		
			// Check if the Operater is still available
			$result = $lsdb->query('SELECT available FROM '.DB_PREFIX.'user WHERE id = "'.smartsql($_POST['partner_id']).'" AND available = 1');
			
			if ($lsdb->affected_rows == 0) {
				$print_offline = '<li class="list-group-item error">'.$tl["general"]["g139"].'</li>';
			}
		
		
			// Set typing status
			if (isset($_POST['is_typing'])) {
				
				$lsdb->query('SELECT typingfrom FROM '.DB_PREFIX.'operatortyping WHERE typingfrom = "'.smartsql($_POST['uid']).'" AND typingto = "'.smartsql($_POST['partner_id']).'"');
				
				if ($lsdb->affected_rows > 0) {
				
					$lsdb->query('UPDATE '.DB_PREFIX.'operatortyping SET typingstatus = "'.smartsql($_POST['is_typing']).'" WHERE typingfrom = "'.smartsql($_POST['uid']).'" AND typingto = "'.smartsql($_POST['partner_id']).'"');
					
				} else {
					
					$lsdb->query('INSERT INTO '.DB_PREFIX.'operatortyping SET typingstatus = "'.smartsql($_POST['is_typing']).'", typingfrom = "'.smartsql($_POST['uid']).'", typingto = "'.smartsql($_POST['partner_id']).'"');
				
				}
			}
		
		
			//check if current user has unreceived messages which are older than limit, if yes, display it with date
			$resultu = $lsdb->query('SELECT id FROM '.DB_PREFIX.'operatorchat WHERE fromid = "'.smartsql($_POST['partner_id']).'" AND toid = "'.($_POST['uid']).'" AND sent < "'.$sent_time.'" AND received = 0 AND msgpublic = 0 ORDER BY id ASC');
			
			if ($lsdb->affected_rows > 0) {
			
				while ($rowu = $resultu->fetch_assoc()) {
				
					$lsdb->query('UPDATE '.DB_PREFIX.'operatorchat SET received = 1, sent = "'.time().'" WHERE id = "'.smartsql($rowu['id']).'" AND received = 0');
					
				}
				
				$lsdb->query('INSERT INTO '.DB_PREFIX.'operatorchat SET fromid = "'.smartsql($_POST['partner_id']).'", toid = "'.smartsql($_POST['uid']).'", message = "'.smartsql($tl["general"]["g142"]).'", sent = "'.time().'", system_message = "yes"');
			}
		
			// Load Messages
			$resultm = $lsdb->query('SELECT t1.id, t1.fromid, t1.toid, t1.message, t1.system_message, t1.sent, t2.username FROM '.DB_PREFIX.'operatorchat AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON (t1.fromid = t2.id) WHERE msgpublic = 0 AND ((fromid = "'.smartsql($_POST['uid']).'" AND toid = "'.smartsql($_POST['partner_id']).'" AND sent > "'.$sent_time.'") OR (fromid = "'.smartsql($_POST['partner_id']).'" AND toid = "'.($_POST['uid']).'" AND sent > "'.$sent_time.'")) ORDER BY sent ASC');	
			
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
					
					
					// If message has been received mark it!
					if ($rowm['toid'] == $_POST['uid']) {
						$lsdb->query('UPDATE '.DB_PREFIX.'operatorchat SET received = 1 WHERE id = "'.smartsql($rowm['id']).'" AND received = 0');
					}		
					
					$last_msg = $rowm['sent'];
				}
				
				
				
				//print last message time if older than 2 mins
				$math = time() - $last_msg;
				if ($math > 120) {
					$chatmsg .= '<li class="list-group-item system">'.str_replace("%s", date("H:i", $last_msg), $tl["general"]["g141"]).'</li>';
				}
				
				$chatmsg .= $print_offline;
				
			} else {
				$chatmsg .= $print_offline;
			}
			
				$chatmsg .= '</ul>';
				
			echo $chatmsg;
	
	break;
	
	case 'send-msg':
	
		if (empty($_POST['message'])) {
			echo $tl['error']['e1'];
		} else {
	
			$result = $lsdb->query('SELECT available FROM '.DB_PREFIX.'user WHERE id = "'.smartsql($_POST['to_id']).'" AND available = 1');	
			
			if ($lsdb->affected_rows > 0) {
			
				$message = trim($_POST['message']);
				
				$message = filter_var($message, FILTER_SANITIZE_STRING);
			
				$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'operatorchat SET fromid = "'.smartsql($_POST['uid']).'", toid = "'.smartsql($_POST['to_id']).'", message = "'.smartsql($message).'", sent = "'.time().'"');
			
				if ($result) {
					echo '1';
				} else {
					echo 'error';
				}
			
			}
		
		}
	
	break;
	
	default:
	
		return false;

}