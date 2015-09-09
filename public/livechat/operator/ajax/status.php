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

// Get the special lang var once for the time
define('LS_DAY', $tl['general']['g74']);
define('LS_HOUR', $tl['general']['g75']);
define('LS_MINUTE', $tl['general']['g76']);
define('LS_MULTITIME', $tl['general']['g77']);
define('LS_AGO', $tl['general']['g78']);

if (!is_numeric($_POST['id']) && !is_numeric($_POST['uid'])) die("There is no such thing!");

// Now only get the department for the user
if ($_SESSION['usr_department'] && is_numeric($_SESSION['usr_department'])) {
	$sqluo = ' AND depid = '.smartsql($_SESSION['usr_department']);
	$sqlw = 'department = '.smartsql($_SESSION['usr_department']).' AND status = 1 AND operatorid = 0 OR ';
	$sqlwc = 't1.department = '.smartsql($_SESSION['usr_department']).' AND t1.status = 1 AND t1.operatorid = 0 OR ';
}
if ($_SESSION['usr_department']) {
	$sqluo = ' AND depid IN('.smartsql($_SESSION['usr_department']).')';
	$sqlw = 'department IN('.smartsql($_SESSION['usr_department']).') AND status = 1 AND operatorid = 0 OR ';
	$sqlwc = 't1.department IN('.smartsql($_SESSION['usr_department']).') AND t1.status = 1 AND t1.operatorid = 0 OR ';
}
if ($_SESSION['usr_department'] == 0) {
	$sqluo = ' AND depid >= 0';
	$sqlw = 'department >= 0 AND status = 1 AND operatorid = 0 OR ';
	$sqlwc = 't1.department >= 0 AND t1.status = 1 AND t1.operatorid = 0 OR ';
}

	$useronline = false;

	if ($_POST['advanceduo']) {
	
		$result = $lsdb->query('SELECT t1.id, t1.referrer, t1.firstreferrer, t1.agent, t1.hits, t1.ip, t1.lasttime, t1.time, t1.proactive, t1.readtime, t2.initiated, t2.ended FROM '.DB_PREFIX.'buttonstats AS t1 LEFT JOIN '.DB_PREFIX.'sessions AS t2 ON (t1.session = t2.session) WHERE t1.lasttime > (NOW() - INTERVAL 5 MINUTE)'.$sqluo.' AND (opid = 0 OR opid = "'.smartsql($_POST['uid']).'") GROUP BY t1.session ORDER BY t1.lasttime DESC LIMIT 50');
		
		if ($lsdb->affected_rows > 0) {
		
			$useronline = '<table class="table table-striped"><thead><th>'.$tl["general"]["g169"].'</th><th>'.$tl["general"]["g170"].'</th><th>'.$tl["general"]["g171"].'</th><th>'.$tl["general"]["g172"].'</th><th>'.$tl["general"]["g11"].'</th><th>'.$tl["general"]["g173"].'</th><th>'.$tl["general"]["g174"].'</th></thead>';
			
			while ($row = $result->fetch_assoc()) {
				
				// Convert time to minutes and hours
				$row['lasttime'] = LS_base::lsTimesince($row['lasttime'], LS_DATEFORMAT, LS_TIMEFORMAT);
				$row['time'] = LS_base::lsTimesince($row['time'], LS_DATEFORMAT, LS_TIMEFORMAT);
			
				if ($row['proactive'] != 0) {
					$icon = '<span class="glyphicon glyphicon-bell"></span>';
					$uclass = ' class="warning"';
				} else {
					$icon = '<span class="glyphicon glyphicon-user"></span>';
					$uclass = '';
				}
				
				$button = '<a href="javascript:void(0)" id="usero-'.$row['id'].'" class="btn btn-default btn-xs rhino-online-user">'.$icon.'</a>';
				
				if ($row['readtime'] == 1) $uclass = ' class="danger"';
				
				if ($row['readtime'] == 2) $uclass = ' class="success"';
				
				if ($row['initiated'] && $row['ended'] == 0) $button = '<span class="glyphicon glyphicon-comment"></span>'; 
			
				$useronline .= '<tr'.$uclass.'><td>'.$row['referrer'].'</td><td>'.$row['firstreferrer'].'</td><td>'.$row['agent'].'</td><td>'.$row['hits'].'</td><td>'.$row['ip'].'</td><td>'.$row['time'].'</td><td>'.$row['lasttime'].'</td><td>'.$button.'</td></tr>'; 
				
			}
			
			$useronline .= '</table>';
			
		}
	
	} else {
	
		$result = $lsdb->query('SELECT t1.id, t1.referrer, t1.proactive, t1.readtime, t1.agent, t1.ip, t2.initiated, t2.ended FROM '.DB_PREFIX.'buttonstats t1 LEFT JOIN '.DB_PREFIX.'sessions AS t2 ON (t1.session = t2.session) WHERE t1.lasttime > (NOW() - INTERVAL 5 MINUTE)'.$sqluo.' AND (opid = 0 OR opid = "'.smartsql($_POST['uid']).'") GROUP BY t1.session ORDER BY t1.lasttime DESC LIMIT 5');
		
		if ($lsdb->affected_rows > 0) {
		
			$useronline = '<ul class="list-group">';
			
			while ($row = $result->fetch_assoc()) {
			
				if ($row['proactive'] != 0) {
					$icon = '<span class="glyphicon glyphicon-bell"></span>';
					$uclass = ' list-group-item-warning';
				} else {
					$icon = '<span class="glyphicon glyphicon-user"></span>';
					$uclass = '';
				}
				
				$button = '<div class="pull-right"><a href="javascript:void(0)" id="usero-'.$row['id'].'" class="btn btn-default btn-xs rhino-online-user" title="'.$row['agent'].'/'.$row['ip'].'">'.$icon.'</a></div>';
				
				if ($row['readtime'] == 1) $uclass = ' list-group-item-danger';
				
				if ($row['readtime'] == 2) $uclass = ' list-group-item-success';
				
				if ($row['initiated'] && $row['ended'] == 0) $button = '<div class="pull-right"><a href="javascript:void(0)" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-comment"></span></a></div>';
				
				$useronline .= '<li class="list-group-item'.$uclass.'" title="'.$row['referrer'].'">'.ls_cut_text($row["referrer"],35,'...').$button.'</li>';
				
			}
			
			$useronline .= '</ul>';
			
		}
	
	}
	
	$oponline = false;
	
	if ($_POST['olist']) {
	
	$result = $lsdb->query('SELECT id, username, name, operatorchat FROM '.DB_PREFIX.'user WHERE available = 1 AND id != "'.smartsql($_POST['uid']).'" LIMIT 20');
	
	if ($lsdb->affected_rows > 0) {
	
		$oponline = '<ul class="list-group">';
		
		while ($row = $result->fetch_assoc()) {
		
			$opchat = '';
			if ($_POST['opcheck'] && $row['operatorchat']) $opchat = ' <a href="javascript:void(0)" class="btn btn-info btn-xs rhino-oponline" data-user="'.$row['id'].':#:'.$row['username'].'"><span class="glyphicon glyphicon-user"></span></a>';
		
			$oponline .= '<li class="list-group-item">'.$row['name'].' <span class="pull-right"><a href="index.php?p=uonline&amp;sp=opstat&amp;ssp='.$row['id'].'" data-toggle="modal" data-target="#generalModal" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-stats"></span></a>'.$opchat.'</span></li>'; 
			
		}
		
		$oponline .= '</ul>';
		
	}
	
	} elseif (!$_POST['olist'] && $_POST['opcheck']) {
	
		$result = $lsdb->query('SELECT id, username, name FROM '.DB_PREFIX.'user WHERE available = 1 AND operatorchat = 1 AND id != "'.smartsql($_POST['uid']).'" LIMIT 20');
		
		if ($lsdb->affected_rows > 0) {
		
			$oponline = '<ul class="list-group">';
			
			while ($row = $result->fetch_assoc()) {
			
				$oponline .= '<li class="list-group-item">'.$row['name'].' <span class="pull-right"><a href="javascript:void(0)" class="btn btn-info btn-xs rhino-oponline" data-user="'.$row['id'].':#:'.$row['username'].'"><span class="glyphicon glyphicon-user"></span></a></span></li>'; 
				
			}
			
			$oponline .= '</ul>';
			
		}
	
	}
	
	// Check if there is a new client, message or a transfer is awaiting for approval.
	$result = $lsdb->query('SELECT id, operatorid, answered, updated, transferid, transfermsg FROM '.DB_PREFIX.'sessions WHERE '.$sqlw.'operatorid = '.smartsql($_POST['uid']).' AND status = 1 OR department = 0 AND status = 1 AND operatorid = 0 OR transferid = '.smartsql($_POST['uid']).' AND status = 1');
	
	if ($lsdb->affected_rows > 0) {
	
		while ($row = $result->fetch_assoc()) {
			
			// We have a transfer, need to display it!
			if ($row['transferid'] == $_POST['uid']) {
				
				if ($row["transfermsg"]) $split_transfer_msg = explode(':#:', $row["transfermsg"]);
				
				// Display underneath the button
				$transfer_msg = '<div class="alert alert-info"><span class="pull-right"><a href="javascript:void(0)" class="btn btn-xs btn-danger" onclick="acceptTransfer(0, '.$row['transferid'].', '.$row['id'].');"><span class="glyphicon glyphicon-remove"></span></a> <a href="javascript:void(0)" class="btn btn-xs btn-success" onclick="acceptTransfer(1, '.$row['transferid'].', '.$row['id'].');"><span class="glyphicon glyphicon-ok"></span></a></span><p>'.$tl['general']['g110'].' '.$tl['general']['g12'].': '.$split_transfer_msg[0].'</p><p>'.$split_transfer_msg[1].'</p></div>';
				$transferid = $row['transferid'];
			}
				
				$newConv = 0;
				$scrollNow = 0;
			
				// check for new conversations
				if ($row['operatorid'] == 0) {
					$newConv = 1;
				}
				if ($row['operatorid'] > 0 && ($row['updated'] > $row['answered'])) {
					$newConv = 2;
				}
				
				if ($row['updated'] > (time() - 6)) $scrollNow = 1;
		} 
		
	} else {
		$newConv = 0;
		$scrollNow = 0;
		$transferid = 0;
		$transfer_msg = 0;
		
	}
	
	// Only go for it if we want to
	if ($_POST['convlist'] == 1) {
	
	// Now let's get the conversation list
	// remove timeout- prevents session duplication
	$timeout_remove = 43200;
	
		$new = array();
		$updated = array();
		$current = array();
		$closed = array();
		$count = 0;
		
		$result = $lsdb->query('SELECT t1.*, t2.title AS dep_title FROM '.DB_PREFIX.'sessions AS t1 LEFT JOIN '.DB_PREFIX.'departments AS t2 ON (t1.department = t2.id) WHERE '.$sqlwc.'operatorid = '.smartsql($_POST['uid']).' AND t1.status = 1 OR t1.department = 0 AND t1.status = 1 AND t1.operatorid = 0 OR t1.transferid = '.smartsql($_POST['uid']).' AND t1.status = 1 AND t1.operatorid != '.smartsql($_POST['uid']).' ORDER BY answered ASC');
		
		if ($lsdb->affected_rows > 0) {
			
			while ($row = $result->fetch_assoc()) {
				
				if ($row['status']) {
				
					if (((time() - $row['initiated']) > $timeout_remove) && $row['answered'] == 0) {
						
						$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 0, ended = "'.time().'" WHERE id = "'.$row['id'].'"');
						
						$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET
						name = "System",
						message = "'.smartsql($tl['general']['g72']).'",
						convid = "'.$row['id'].'",
						time = NOW(),
						class = "notice"');
						
					} elseif ($row['u_status'] && (time() - $row['u_status']) > 30) {
						
						$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 0, ended = "'.time().'" WHERE id = "'.$row['id'].'"');
						
						$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET
						name = "System",
						message = "'.$row['name'].' '.smartsql($tl['general']['g168']).'",
						convid = "'.$row['id'].'",
						time = NOW(),
						class = "notice"');
					
					} elseif ($row['answered'] > $row['updated']) {
						
						if ((time() - $row['u_status']) > 600) {
							
							$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 0, ended = "'.time().'" WHERE id = "'.$row['id'].'"');
							
							$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET
							name = "System",
							message = "'.smartsql($tl['general']['g72']).'",
							convid = "'.$row['id'].'",
							time = NOW(),
							class = "notice"');
						}
					}
					
			// Get all available chats
			if ($row['updated'] > $row['answered']) {
				if(($row['operatorid'] == 0) && ($row['answered'] == 0)) {
					$new[$count]["name"] = $row['name'];		
					$new[$count]["convid"] = $row['id'];
					$new[$count]["countrycode"] = $row['countrycode'];
					$new[$count]["referrer"] = $row['referrer'];
					$new[$count]["department"] = $row['dep_title'];
					$new[$count]["ip"] = $row['ip'];
					if ($row['u_typing']) $new[$count]["typing"] = ' <span class="glyphicon glyphicon-pencil"></span>';
				} else {
					$updated[$count]["name"] = $row['name'];
	                $updated[$count]["convid"] = $row['id'];
	                $updated[$count]["countrycode"] = $row['countrycode'];
	                $updated[$count]["referrer"] = ($row['creferrer'] ? $row['creferrer'] : $row['referrer']);
	                $updated[$count]["department"] = $row['dep_title'];
	                $updated[$count]["updated"] = LS_base::lsTimesince($row['updated'], LS_DATEFORMAT, LS_TIMEFORMAT);
	                if ($row['u_typing']) $updated[$count]["typing"] = ' <span class="glyphicon glyphicon-pencil"></span>';
				}
				
			} elseif (($row['updated'] == 0) && ($row['answered'] == 0)) {
				$new[$count]["name"] = $row['name'];
	            $new[$count]["convid"] = $row['id'];
	            $new[$count]["countrycode"] = $row['countrycode'];
	            $new[$count]["referrer"] = $row['referrer'];
	            $new[$count]["department"] = $row['dep_title'];
	            $new[$count]["ip"] = $row['ip'];
	            if ($row['u_typing']) $new[$count]["typing"] = ' <span class="glyphicon glyphicon-pencil"></span>';
	            
			} else {
				$current[$count]["name"] = $row['name'];
	            $current[$count]["convid"] = $row['id'];
	            $current[$count]["countrycode"] = $row['countrycode'];
	            $current[$count]["referrer"] = ($row['creferrer'] ? $row['creferrer'] : $row['referrer']);
	            $current[$count]["department"] = $row['dep_title'];
	            $current[$count]["updated"] = LS_base::lsTimesince($row['answered'], LS_DATEFORMAT, LS_TIMEFORMAT);
	            if ($row['u_typing']) $current[$count]["typing"] = ' <span class="glyphicon glyphicon-pencil"></span>';
		}
		}
		
		$transfer_name = '';
		
		// We have a transfer, need to display it!
		if ($row['transferid'] == $_POST['uid']) {
			
			if ($row["transfermsg"]) $split_transfer_msg = explode(':#:', $row["transfermsg"]);
			
			// Display underneath the button
			$transfer_name = '<p>'.$tl['general']['g110'].' '.$tl['general']['g12'].': '.$split_transfer_msg[0].'</p>';
		}
		
		if ($row['transferid'] != 0 && $row['transferid'] != $_POST['uid']) $transfer_name = '<p>'.$tl['general']['g117'].'</p>';
		
		if (!$row['status']) {
			if (((time() - $row['ended']) > 300) && !$row['hide']) {
			
				$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET hide = 1 WHERE id = "'.$row['id'].'"');
				
				$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
				name = "System",
				message = "'.smartsql($tl['general']['g73']).'",
				convid = "'.$row['id'].'",
				class = "notice"');
				
			} else if (!$row['hide']) {
				$closed[$count]["name"] = $row['name'];
			    $closed[$count]["convid"] = $row['id'];
			}
		}
		
		if ($row['hide']) {
			if((time() - $row['ended']) > $timeout_remove) {
			
				$lsdb->query('DELETE FROM '.DB_PREFIX.'transcript WHERE convid = "'.$row['id'].'"');
				$lsdb->query('DELETE FROM '.DB_PREFIX.'sessions WHERE id = "'.$row['id'].'"');
				
			}
		}
	$count = $count + 1;
	}
	
		shuffle($new);
		shuffle($updated);
		shuffle($current);
		sort($new);
		sort($updated);
		sort($current);
		$newTotal = count($new);
		$updatedTotal = count($updated);
		$currentTotal = count($current);
		if (($newTotal + $updatedTotal + $currentTotal) == 0 ) die(json_encode(array('status' => 0, "html" => "")));
		
		for($i = 0; $i < $newTotal; $i ++ ) {
			$convlist .= '<div class="panel panel-success">';
			$convlist .= '<div class="panel-heading"><img src="img/country/'.$new[$i]['countrycode'].'.gif" /> <a href="javascript:void(0)" class="alert-link">'.$new[$i]["name"].$new[$i]["typing"].'</a> <div class="pull-right"><a href="javascript:void(0)" onclick="if(confirm(\''.$tl["general"]["g203"].'\')){ls.activeConv = '.$new[$i]["convid"].';denyChat(ls.activeConv, '.$_POST['uid'].');}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-ban-circle"></span></a> <a href="javascript:void(0)" onclick="ls.activeConv = '.$new[$i]["convid"].';takeChat(ls.activeConv, '.$_POST['uid'].');" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-comment"></span></a></div></div><div class="panel-body">';
			$convlist .= $tl["general"]["g120"].$new[$i]["department"].'<br />'.$tl["general"]["g156"].$new[$i]["referrer"].'<br /><strong>'.$tl["general"]["g11"].'</strong>: '.$new[$i]["ip"];
		    $convlist .= '</div></div>';
		}
		for($i = 0; $i < $updatedTotal; $i ++ ) {
			$convlist .= '<div class="panel panel-warning" onclick="activeConversation=true;loadchat=true;scrollchat=true;ls.activeConv='.$updated[$i]["convid"].';getInfo(ls.activeConv);getInput(ls.activeConv);">';
			$convlist .= '<div class="panel-heading"><img src="img/country/'.$updated[$i]['countrycode'].'.gif" /> <a href="javascript:void(0)" class="alert-link">'.$updated[$i]["name"].$updated[$i]["typing"].'</a> <span class="badge pull-right">'.$updated[$i]['updated'].'</span></div><div class="panel-body">';
			$convlist .= $transfer_name;
			$convlist .= $tl["general"]["g120"].$updated[$i]["department"].'<br />'.$tl["general"]["g156"].$updated[$i]["referrer"];
		    $convlist .= '</div></div>';
		}
		for($i = 0; $i < $currentTotal; $i ++ ) {
			$convlist .= '<div class="panel panel-info" onclick="activeConversation=true;loadchat=true;scrollchat=true;ls.activeConv='.$current[$i]["convid"].';getInfo(ls.activeConv);getInput(ls.activeConv);">';
			$convlist .= '<div class="panel-heading"><img src="img/country/'.$current[$i]['countrycode'].'.gif" /> <a href="javascript:void(0)">'.$current[$i]["name"].$current[$i]["typing"].'</a> <span class="badge pull-right">'.$current[$i]['updated'].'</span></div><div class="panel-body">';
			$convlist .= $transfer_name;
			$convlist .= $tl["general"]["g120"].$current[$i]["department"].'<br />'.$tl["general"]["g156"].$current[$i]["referrer"];
		    $convlist .= '</div></div>';
		}
	
	} else {
		$convlist = '';
	}
	
	} else {
		$convlist = '';
	}
	
	
	echo json_encode(array("useronline" => $useronline, "oponline" => $oponline, 'newc' => $newConv, 'scrollnow' => $scrollNow, 'tid' => $transferid, 'tmsg' => $transfer_msg, "conversation" => $convlist, "noconv" => $tl['general']['g79']));
?>