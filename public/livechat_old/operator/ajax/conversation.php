<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1998 03:10:00 GMT");

/*======================================================================*\
|| #################################################################### ||
|| # Rhino 2.5                                                        # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2014 Rhino All Rights Reserved.                        # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
|| #                  http://www.livesupportrhino.com                 # ||
|| #################################################################### ||
\*======================================================================*/

// Start the session
session_start();

if (!file_exists('../../config.php')) die('ajax/[response.php] config.php not exist');
require_once '../../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['idhash'])) die("Nothing to see here");

if (file_exists(APP_PATH.'operator/lang/'.LS_LANG.'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    trigger_error('Translation file not found');
}

if (!is_numeric($_POST['id'])) die("There is no such message!");

// Get the sound first, we need that always
$newConv = 0;
$statusc = 0;
$scrollNow = 0;

$result = $lsdb->query('SELECT answered, updated FROM '.DB_PREFIX.'jrc_sessions WHERE status = 1');

if ($lsdb->affected_rows > 0) {

	while ($row = $result->fetch_assoc()) {
		
			// check for new conversations
			if($row['answered'] == 0) {
				$newConv = 1;
			}
			if($row['updated'] > $row['answered']) {
				$newConv = 2;
			}
			
			if ($row['updated'] > (time() - 6)) $scrollNow = 1;
	}
}

if ($_POST['clist'] == 1) {

// remove timeout- prevents session duplication
$timeout_remove = 43200;

	$new = array();
	$updated = array();
	$current = array();
	$closed = array();
	$count = 0;
	$convlist = '';
	
	$result = $lsdb->query('SELECT * FROM '.DB_PREFIX.'jrc_sessions WHERE status = 1');
	
	if ($lsdb->affected_rows > 0) {
		
		while ($row = $result->fetch_assoc()) {
			
			if ($row['status'] == 1) {
			
				if (((time() - $row['initiated']) > $timeout_remove) && $row['answered'] == 0) {
					
					$lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET status = 0, ended = "'.time().'" WHERE id = "'.$row['id'].'"');
					
					$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET
					name = "System",
					message = "'.smartsql($tl['general']['g72']).'",
					convid = "'.$row['id'].'",
					time = NOW(),
					class = "notice"');
				}
				
				if ($row['answered'] > $row['updated']) {
					
					if ((time() - $row['answered']) > 600) {
						
						$lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET status = 0, ended = "'.time().'" WHERE id = "'.$row['id'].'"');
						
						$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET
						name = "System",
						message = "'.smartsql($tl['general']['g72']).'",
						convid = "'.$row['id'].'",
						time = NOW(),
						class = "notice"');
					}
				}
		
		
		if ($row['updated'] > $row['answered']) {
			if(($row['updated'] == 0) && ($row['answered'] == 0)) {
				$new[$count]["name"] = $row['name'];		
				$new[$count]["convid"] = $row['convid'];
				if ($row['u_typing']) $new[$count]["typing"] = '<img src="../img/typing.png" width="16" height="16" alt="typing" /> ';	
			} else {
				$updated[$count]["name"] = $row['name'];
                $updated[$count]["convid"] = $row['convid'];
                if ($row['u_typing']) $updated[$count]["typing"] = '<img src="../img/typing.png" width="16" height="16" alt="typing" /> ';
			}
			
		} elseif (($row['updated'] == 0) && ($row['answered'] == 0)) {
			$new[$count]["name"] = $row['name'];
            $new[$count]["convid"] = $row['convid'];
            if ($row['u_typing']) $new[$count]["typing"] = '<img src="../img/typing.png" width="16" height="16" alt="typing" /> ';
            
		} else {
			$current[$count]["name"] = $row['name'];
            $current[$count]["convid"] = $row['convid'];
            if ($row['u_typing']) $current[$count]["typing"] = '<img src="../img/typing.png" width="16" height="16" alt="typing" /> ';
	}
	}
	
	if ($row['status'] == 0) {
		if (((time() - $row['ended']) > 300) && !$row['hide']) {
		
			$lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET hide = 1 WHERE id = "'.$row['id'].'"');
			
			$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET 
			name = "System",
			message = "'.smartsql($tl['general']['g73']).'",
			convid = "'.$row['id'].'",
			class = "notice"');
			
		} else if (!$row['hide']) {
			$closed[$count]["name"] = $row['name'];
		    $closed[$count]["convid"] = $row['convid'];
		}
	}
	
	if ($row['hide']) {
		if((time() - $row['ended']) > $timeout_remove) {
		
			$lsdb->query('DELETE FROM '.DB_PREFIX.'jrc_transcript WHERE convid = "'.$row['convid'].'"');
			$lsdb->query('DELETE FROM '.DB_PREFIX.'jrc_sessions WHERE id = "'.$row['id'].'"');
			
		}
	}
$count = $count + 1;
}

	shuffle($new);
	shuffle($updated);
	shuffle($current);
	shuffle($closed);
	sort($new);
	sort($updated);
	sort($current);
	sort($closed);
	$newTotal = count($new);
	$updatedTotal = count($updated);
	$currentTotal = count($current);
	$closedTotal = count($closed);
	if (($newTotal + $updatedTotal + $currentTotal + $closedTotal) == 0) { 
	
		$statusc = 0;
		
	} else {
		
		for($i = 0; $i < $newTotal; $i ++ ) {
			$convlist .= '<div class="alert alert-block alert-success" onclick="if(confirm(\''.$tl["general"]["g100"].'\')){ls.activeConv = '.$new[$i]["convid"].';takeChat(ls.activeConv, '.$_POST['uid'].');}">';
			$convlist .= '<i class="icon-user"></i> <a href="javascript:;">'.$new[$i]["typing"].$new[$i]["name"].'</a>';
		    $convlist .= '</div>';
		}
		for($i = 0; $i < $updatedTotal; $i ++ ) {
			$convlist .= '<div class="alert alert-block" onclick="activeConversation=true;loadchat=true;ls.activeConv='.$updated[$i]["convid"].';jrc_getInfo(ls.activeConv);jrc_getInput(ls.activeConv);">';
			$convlist .= '<i class="icon-user"></i> <a href="javascript:;">'.$updated[$i]["typing"].$updated[$i]["name"].'</a>';
			$convlist .= $transfer_name;
		    $convlist .= '</div>';
		}
		for($i = 0; $i < $currentTotal; $i ++ ) {
			$convlist .= '<div class="alert alert-block alert-info" onclick="activeConversation=true;loadchat=true;ls.activeConv='.$current[$i]["convid"].';jrc_getInfo(ls.activeConv);jrc_getInput(ls.activeConv);">';
			$convlist .= '<i class="icon-user"></i> <a href="javascript:;">'.$current[$i]["typing"].$current[$i]["name"].'</a>';
			$convlist .= $transfer_name;
		    $convlist .= '</div>';
		}
		for($i = 0; $i < $closedTotal; $i ++ ) {
			$convlist .= '<div class="alert alert-block alert-error" onclick="activeConversation=true;loadchat=true;ls.activeConv='.$closed[$i]["convid"].';jrc_getInfo(ls.activeConv);jrc_getInput(ls.activeConv);">';
			$convlist .= '<i class="icon-user"></i> <a href="javascript:;">'.$closed[$i]["typing"].$closed[$i]["name"].'</a>';
		    $convlist .= '</div>';
		}
	
		$statusc = 1;
		
	}
	
}

}

echo json_encode(array('status' => $statusc, "html" => $convlist, 'newc' => $newConv, 'scrollnow' => $scrollNow));
?>