#!/usr/bin/env php
<?php

// Set time limit to zero
set_time_limit(0);

require_once('websockets.php');

class echoServer extends WebSocketServer {
  //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.
  
  protected function process ($user, $message) {
  
  global $socket_url;
  global $final_url;
  global $lsdb;
  
  // What we have to do
  $obj = json_decode($message, true);
  
  if ($obj['user_area'] == 'admin') {
  	
  	$result = $lsdb->query('SELECT id FROM '.DB_PREFIX.'user WHERE idhash = "'.smartsql($obj['ohash']).'"');
  	if ($lsdb->affected_rows == 1) {
  	
  	$row = $result->fetch_assoc();
  	if (!is_numeric($obj['uid'])) $obj['uid'] = $row['id'];
  	
  	if ($obj['oplang'] && file_exists($final_url.'operator/lang/'.$obj['oplang'].'.ini')) {
  	    $tl = parse_ini_file($final_url.'operator/lang/'.$obj['oplang'].'.ini', true);
  	} elseif (!$BT_LANGUAGE && file_exists($final_url.'lang/'.LS_LANG.'.ini')) {
  		$tl = parse_ini_file($final_url.'operator/lang/'.LS_LANG.'.ini', true);
  	} else {
  	    $tl = parse_ini_file($final_url.'operator/lang/en.ini', true);
  	}
  	
  	// Get the special lang var once for the time
  	define('LS_DAY', $tl['general']['g74']);
  	define('LS_HOUR', $tl['general']['g75']);
  	define('LS_MINUTE', $tl['general']['g76']);
  	define('LS_MULTITIME', $tl['general']['g77']);
  	define('LS_AGO', $tl['general']['g78']);
  	
  	switch ($obj['job']) {
  	
  		case 'check_status':
  	
		  	// Now only get the department for the user
		  	if ($obj['odep'] && is_numeric($obj['odep'])) {
		  		$sqluo = ' AND depid = '.smartsql($obj['odep']);
		  		$sqlw = 'department = '.smartsql($obj['odep']).' AND status = 1 AND operatorid = 0 OR ';
		  		$sqlwc = 't1.department = '.smartsql($obj['odep']).' AND t1.status = 1 AND t1.operatorid = 0 OR ';
		  	}
		  	if ($obj['odep']) {
		  		$sqluo = ' AND depid IN('.smartsql($obj['odep']).')';
		  		$sqlw = 'department IN('.smartsql($obj['odep']).') AND status = 1 AND operatorid = 0 OR ';
		  		$sqlwc = 't1.department IN('.smartsql($obj['odep']).') AND t1.status = 1 AND t1.operatorid = 0 OR ';
		  	}
		  	if ($obj['odep'] == 0) {
		  		$sqluo = ' AND depid >= 0';
		  		$sqlw = 'department >= 0 AND status = 1 AND operatorid = 0 OR ';
		  		$sqlwc = 't1.department >= 0 AND t1.status = 1 AND t1.operatorid = 0 OR ';
		  	}
		  	
		  	$useronline = false;
		  	
		  		if ($obj['advanceduo']) {
		  		
		  			$result = $lsdb->query('SELECT t1.id, t1.referrer, t1.firstreferrer, t1.agent, t1.hits, t1.ip, t1.lasttime, t1.time, t1.proactive, t1.readtime, t2.initiated, t2.ended FROM '.DB_PREFIX.'buttonstats AS t1 LEFT JOIN '.DB_PREFIX.'sessions AS t2 ON (t1.session = t2.session) WHERE t1.lasttime > (NOW() - INTERVAL 5 MINUTE)'.$sqluo.' AND (opid = 0 OR opid = "'.smartsql($obj['uid']).'") GROUP BY t1.session ORDER BY t1.lasttime DESC LIMIT 50');
		  			
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
		  		
		  			$result = $lsdb->query('SELECT t1.id, t1.referrer, t1.proactive, t1.readtime, t1.agent, t1.ip, t2.initiated, t2.ended FROM '.DB_PREFIX.'buttonstats t1 LEFT JOIN '.DB_PREFIX.'sessions AS t2 ON (t1.session = t2.session) WHERE t1.lasttime > (NOW() - INTERVAL 5 MINUTE)'.$sqluo.' AND (opid = 0 OR opid = "'.smartsql($obj['uid']).'") GROUP BY t1.session ORDER BY t1.lasttime DESC LIMIT 5');
		  			
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
		  		
		  		if ($obj['olist']) {
		  		
		  		$result = $lsdb->query('SELECT id, username, name, operatorchat FROM '.DB_PREFIX.'user WHERE available = 1 AND id != "'.smartsql($obj['uid']).'" LIMIT 20');
		  		
		  		if ($lsdb->affected_rows > 0) {
		  		
		  			$oponline = '<ul class="list-group">';
		  			
		  			while ($row = $result->fetch_assoc()) {
		  			
		  				$opchat = '';
		  				if ($obj['opcheck'] && $row['operatorchat']) $opchat = ' <a href="javascript:void(0)" class="btn btn-info btn-xs rhino-oponline" data-user="'.$row['id'].':#:'.$row['username'].'"><span class="glyphicon glyphicon-user"></span></a>';
		  			
		  				$oponline .= '<li class="list-group-item">'.$row['name'].' <span class="pull-right"><a href="index.php?p=uonline&amp;sp=opstat&amp;ssp='.$row['id'].'" data-toggle="modal" data-target="#generalModal" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-stats"></span></a>'.$opchat.'</span></li>'; 
		  				
		  			}
		  			
		  			$oponline .= '</ul>';
		  			
		  		}
		  		
		  		} elseif (!$obj['olist'] && $obj['opcheck']) {
		  		
		  			$result = $lsdb->query('SELECT id, username, name FROM '.DB_PREFIX.'user WHERE available = 1 AND operatorchat = 1 AND id != "'.smartsql($obj['uid']).'" LIMIT 20');
		  			
		  			if ($lsdb->affected_rows > 0) {
		  			
		  				$oponline = '<ul class="list-group">';
		  				
		  				while ($row = $result->fetch_assoc()) {
		  				
		  					$oponline .= '<li class="list-group-item">'.$row['name'].' <span class="pull-right"><a href="javascript:void(0)" class="btn btn-info btn-xs rhino-oponline" data-user="'.$row['id'].':#:'.$row['username'].'"><span class="glyphicon glyphicon-user"></span></a></span></li>'; 
		  					
		  				}
		  				
		  				$oponline .= '</ul>';
		  				
		  			}
		  		
		  		}
		  		
		  		// Check if there is a new client, message or a transfer is awaiting for approval.
		  		$result = $lsdb->query('SELECT id, operatorid, answered, updated, transferid, transfermsg FROM '.DB_PREFIX.'sessions WHERE '.$sqlw.'operatorid = '.smartsql($obj['uid']).' AND status = 1 OR department = 0 AND status = 1 AND operatorid = 0 OR transferid = '.smartsql($obj['uid']).' AND status = 1');
		  		
		  		if ($lsdb->affected_rows > 0) {
		  		
		  			while ($row = $result->fetch_assoc()) {
		  				
		  				// We have a transfer, need to display it!
		  				if ($row['transferid'] == $obj['uid']) {
		  					
		  					if ($row["transfermsg"]) $split_transfer_msg = explode(':#:', $row["transfermsg"]);
		  					
		  					// Display underneath the button
		  					$transfer_msg = '<div class="alert alert-danger"><span class="pull-right"><a href="javascript:void(0)" class="btn btn-xs btn-danger" onclick="acceptTransfer(0, '.$row['transferid'].', '.$row['id'].');"><span class="glyphicon glyphicon-remove"></span></a> <a href="javascript:void(0)" class="btn btn-xs btn-success" onclick="acceptTransfer(1, '.$row['transferid'].', '.$row['id'].');"><span class="glyphicon glyphicon-ok"></span></a></span><p>'.$tl['general']['g110'].' '.$tl['general']['g12'].': '.$split_transfer_msg[0].'</p><p>'.$split_transfer_msg[1].'</p></div>';
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
		  		if ($obj['convlist'] == 1) {
		  		
		  		// Now let's get the conversation list
		  		// remove timeout- prevents session duplication
		  		$timeout_remove = 43200;
		  		
		  			$new = array();
		  			$updated = array();
		  			$current = array();
		  			$closed = array();
		  			$count = 0;
		  			
		  			$result = $lsdb->query('SELECT t1.*, t2.title AS dep_title FROM '.DB_PREFIX.'sessions AS t1 LEFT JOIN '.DB_PREFIX.'departments AS t2 ON (t1.department = t2.id) WHERE '.$sqlwc.'operatorid = '.smartsql($obj['uid']).' AND t1.status = 1 OR t1.department = 0 AND t1.status = 1 AND t1.operatorid = 0 OR t1.transferid = '.smartsql($obj['uid']).' AND t1.status = 1  AND t1.operatorid != '.smartsql($obj['uid']).' ORDER BY answered ASC');
		  			
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
		  			if ($row['transferid'] == $obj['uid']) {
		  				
		  				if ($row["transfermsg"]) $split_transfer_msg = explode(':#:', $row["transfermsg"]);
		  				
		  				// Display underneath the button
		  				$transfer_name = '<p>'.$tl['general']['g110'].' '.$tl['general']['g12'].': '.$split_transfer_msg[0].'</p>';
		  			}
		  			
		  			if ($row['transferid'] != 0 && $row['transferid'] != $obj['uid']) $transfer_name = '<p>'.$tl['general']['g117'].'</p>';
		  			
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
		  		if (($newTotal + $updatedTotal + $currentTotal) == 0 ) $this->send($user, json_encode(array("job" => "check_status", 'status' => 0, "html" => "")));
		  			
		  		for($i = 0; $i < $newTotal; $i ++ ) {
		  			$convlist .= '<div class="panel panel-success">';
		  			$convlist .= '<div class="panel-heading"><img src="img/country/'.$new[$i]['countrycode'].'.gif" /> <a href="javascript:void(0)" class="alert-link">'.$new[$i]["name"].$new[$i]["typing"].'</a> <div class="pull-right"><a href="javascript:void(0)" onclick="if(confirm(\''.$tl["general"]["g203"].'\')){ls.activeConv = '.$new[$i]["convid"].';denyChat(ls.activeConv, '.$obj['uid'].');}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-ban-circle"></span></a> <a href="javascript:void(0)" onclick="ls.activeConv = '.$new[$i]["convid"].';takeChat(ls.activeConv, '.$obj['uid'].');" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-comment"></span></a></div></div><div class="panel-body">';
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
		  		
		  	$this->send($user, json_encode(array("job" => "check_status", "useronline" => $useronline, "oponline" => $oponline, 'newc' => $newConv, 'scrollnow' => $scrollNow, 'tid' => $transferid, 'tmsg' => $transfer_msg, "conversation" => $convlist, "noconv" => $tl['general']['g79'])));
		  	break;
		  	case 'receive_messages':
		  	
		  		if (!is_numeric($obj['conv'])) {
		  		
		  			$chatmsg = '';
		  			$statusmsg = false;
		  			$chatended = false;
		  			
		  		} else {
		  		
		  		$result = $lsdb->query('SELECT id, class, name, message, time FROM '.DB_PREFIX.'transcript WHERE convid = "'.smartsql($obj['conv']).'" ORDER BY time ASC');
		  		
		  		if ($lsdb->affected_rows > 0) {
		  		
		  			$chatmsg = '<ul class="list-group">';
		  		
		  			while ($row = $result->fetch_assoc()) {
		  			
		  				$chatended = false;
		  		
		  				if ($row['class'] == "notice") {
		  				
		  					$chatmsg .= '<li class="list-group-item '.$row['class'].'"><span class="user_said"><strong>'.$row['name'].'</strong> '.$tl['general']['g66'].':</span><p>'. stripcslashes($row['message']).'</p></li>';
		  					
		  				} elseif ($row['class'] == "ended") {
		  				
		  					$chatmsg .= '<li class="list-group-item '.$row['class'].'"><span class="user_said"><strong>'.$row['name'].'</strong> '.$tl['general']['g66'].':</span><p>'. stripcslashes($row['message']).'</p></li>';
		  					
		  					$chatended = true;
		  				
		  				} else {
		  				
		  					$chatmsg .= '<li class="list-group-item '.$row['class'].'"><span class="user_said">'.LS_base::lsTimesince($row['time'], LS_DATEFORMAT, LS_TIMEFORMAT).' - <strong>'.$row['name'].'</strong> '.$tl['general']['g66'].':</span><p>'.stripcslashes($row['message']).'</p></li>';  	
		  				}		
		  			}
		  			
		  			$chatmsg .= "</ul>";
		  			$statusmsg = true;
		  		}
		  		}
		  		
		  		$this->send($user, json_encode(array("job" => "receive_messages", 'status' => $statusmsg, 'chatended' => $chatended, 'chat' => $chatmsg)));
		  		
		  	break;
		  	case 'send_message':
		  	
		  		if ($obj['conv'] == "open" || (!is_numeric($obj['id']) && !is_numeric($obj['uid']))) {
		  		
		  			$this->send($user, json_encode(array("job" => "send_message", 'status' => 0, "html" => $tl['general']['g79'])));
		  		
		  		} else {
		  		
		  			$message = trim($obj['msg']);
		  		
		  			if (empty($message)) {
		  				$this->send($user, json_encode(array("job" => "send_message", 'status' => 0, "html" => $tl['error']['e1'])));
		  			} else {
		  		
			  		$result = $lsdb->query('SELECT * FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($obj['id']).'"');
			  		
			  		if ($lsdb->affected_rows > 0) {
			  		
			  			$row = $result->fetch_assoc();
			  			
			  				define('BASE_URL_IMG', str_replace($socket_url, SOCKET_SUBFOLDER_IF, BASE_URL));
			  				
			  				$message = strip_tags($message);
			  				
			  				$message = filter_var($message, FILTER_SANITIZE_STRING);
			  				
			  				$message = trim($message);
			  				
			  				$message = replace_urls(nl2br($message));
			  				
			  				if (LS_SMILIES) {
			  			
			  					require_once $final_url.'class/class.smileyparser.php';	
			  					
			  					// More dirty custom work and smiley parser
			  					$smileyparser = new LS_smiley(); 
			  					$message = $smileyparser->parseSmileytext($message);
			  					
			  				}
			  		
			  				if ($row['status'] == "closed" && !$row['hide']) {
			  					$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 1, updated = "'.$row['updated'].'" WHERE id = "'.$obj['id'].'"');
			  				}
			  				
			  				if (!$row['hide']) {
			  				
			  					$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
			  					name = "'.smartsql($obj['oname']).'",
			  					message = "'.smartsql($message).'",
			  					user = "'.smartsql($obj['uid'].'::'.$obj['uname']).'",
			  					convid = "'.$obj['id'].'",
			  					time = NOW(),
			  					class = "admin"');
			  					
			  					$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
			  					answered = "'.time().'",
			  					o_typing = 0
			  					WHERE id = "'.$obj['id'].'"');
			  					
			  					$this->send($user, json_encode(array("job" => "send_message", 'status' => 1, 'conv' => $row['id'])));
			  					
			  				} elseif ($row['hide']) {
			  				
			  					$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
			  					name = "'.smartsql($obj['oname']).'",
			  					message = "'.smartsql($tl['general']['g64']).'",
			  					convid = "'.$obj['id'].'",
			  					class = "notice"');
			  					
			  					$this->send($user, json_encode(array("job" => "send_message", 'status' => 1, 'conv' => $row['id'])));
			  					
			  				} else {
			  			
			  					$this->send($user, json_encode(array("job" => "send_message", 'status' => 0, "html" => $tl['error']['e1'])));
			  				}
			  			}
			  			
			  		}
			  	}
		  	
		  	break;
		  	case 'op_typing':
		  	
		  		if (is_numeric($obj['conv'])) {
		  	
		  		if ($obj['status'] == 1) {
		  			$result = $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET o_typing = 1 WHERE id = "'.smartsql($obj['conv']).'"');
		  		} else {
		  			$result = $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET o_typing = 0 WHERE id = "'.smartsql($obj['conv']).'"');
		  		}
		  		
		  		if ($result) {
		  			$this->send($user, json_encode(array('job' => 'op_typing', 'tid' => 1)));
		  		} else {
		  			$this->send($user, json_encode(array('job' => 'op_typing', 'tid' => 0)));
		  		}
		  		
		  		} else {
		  			$this->send($user, json_encode(array('job' => 'op_typing', "tid" => 0)));
		  		}
		  		
		  	break;
		  	default:
		  	
		 }
		 
		 }
  
  // User Area
  } else {
  
	  // Import the language file
	  if ($BT_LANGUAGE && file_exists($final_url.'lang/'.$BT_LANGUAGE.'.ini')) {
	  	$tl = parse_ini_file($final_url.'lang/'.$BT_LANGUAGE.'.ini', true);
	    $lang = $BT_LANGUAGE;
	  } elseif (!$BT_LANGUAGE && file_exists($final_url.'lang/'.LS_LANG.'.ini')) {
	  	$tl = parse_ini_file($final_url.'lang/'.LS_LANG.'.ini', true);
	  	$lang = LS_LANG;
	  } else {
	  	$tl = parse_ini_file($final_url.'lang/en.ini', true);
	    $lang = 'en';
	  }
	  
	  global $LV_DEPARTMENTS;
	  
	  // Get the user session id
	  $rlbid = $obj['sessid'];
	  
	  // Get the proactive cookie
	  $cookproactive = "run";
	  if ($obj['proact'] == 1) $cookproactive = "donotrun";
	  
	  // Check if user is chatting...
	  $result = $lsdb->query('SELECT id, userid FROM '.DB_PREFIX.'sessions WHERE session = "'.smartsql($rlbid).'" AND status = 1 LIMIT 1');
	  if ($lsdb->affected_rows == 1) {
	  	$row = $result->fetch_assoc();
	  	$usrid = $row['userid'];
	  	$usr_status = true;
	  } else {
	  	$usrid = 0;
	  	$usr_status = false;
	  }
	  
	  	switch ($obj['job']) {
	  	
	  		case 'check_slide_up':
	  		
	  			// Get the department
	  			$dep = '';
	  			if (is_numeric($obj['did'])) $dep = '&amp;dep='.$obj['did'];
	  			if (is_numeric($obj['opid'])) $dep .= '&amp;opid='.$obj['opid'];
	  			
	  			// Now let's check if we want to hide the chat when offline
	  			$chi = false;
	  			$onoff = (online_operators($LV_DEPARTMENTS, $obj['did'], $obj['opid']) ? true : false);
	  			if ($obj['chi'] == 1) $chi = !$onoff;
	  		
	  			if ($usr_status) {
	  			
	  				$this->send($user, json_encode(array("job" => "check_slide_up", "status" => true, "onoff" => $onoff, "chi" => $chi, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace($socket_url, SOCKET_SUBFOLDER_IF, BASE_URL).'/index.php?p=chat&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>')));
	  				
	  				
	  			} else {
	  			
	  				if ($onoff) {
	  				
	  					$this->send($user, json_encode(array("job" => "check_slide_up", "status" => false, "onoff" => $onoff, "chi" => $chi, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace($socket_url, SOCKET_SUBFOLDER_IF, BASE_URL).'/index.php?p=start&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>')));
	  				
	  				} else {
	  				
	  					$this->send($user, json_encode(array("job" => "check_slide_up","status" => false, "onoff" => $onoff, "chi" => $chi, "form" => '<iframe seamless="seamless" class="jrc_ichat" scrolling="no" frameborder="0" src="'.str_replace($socket_url, SOCKET_SUBFOLDER_IF, BASE_URL).'/index.php?p=contact&amp;slide=1&amp;lang='.$lang.$dep.'"></iframe>')));
	  				
	  				}
	  			
	  			}
	  		
	  		break;
	  		case 'check_proactive':
	  			
	  			$proactive = true;
	  			$lvs_departments = true;
	  			$newConv = 0;
	  			$newMSG = '';
	  			
	  			if ($obj['slide']) $lvs_departments = (online_operators($LV_DEPARTMENTS, $obj['did'], $obj['opid']) ? true : false);
	  			
	  			if ($usr_status) {
	  			
	  				// Update the status for better user handling
	  				$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET u_status = "'.time().'" WHERE session = "'.smartsql($rlbid).'" AND status != "closed"');
	  				
	  				$result = $lsdb->query('SELECT id FROM '.DB_PREFIX.'sessions AS t1 WHERE session = "'.smartsql($rlbid).'" AND (answered > updated) AND status != "closed"');
	  				
	  				if ($lsdb->affected_rows > 0) {
	  					$newConv = 1;
	  					$newMSG = $tl["general"]["g22"];
	  				}
	  				
	  			}
	  			
	  			if ($lvs_departments) {
	  			
	  			if ($cookproactive == "run") {
	  				
		  			// Check if we have an auto proactive
		  			$result = $lsdb->query('SELECT t1.message, t1.showalert, t1.wayin, t1.wayout FROM '.DB_PREFIX.'autoproactive AS t1 LEFT JOIN '.DB_PREFIX.'buttonstats AS t2 ON (t1.path = t2.referrer) WHERE t2.readtime = 0 AND t2.session = "'.smartsql($rlbid).'" AND t2.hits >= t1.visitedsites AND UNIX_TIMESTAMP(t2.lasttime) <= (UNIX_TIMESTAMP() - t1.timeonsite)');
		  			
		  			if ($lsdb->affected_rows > 0) {
		  			
		  				$row = $result->fetch_assoc();
		  				
		  				$proactive = false;
		  				
		  				$result = $lsdb->query('UPDATE '.DB_PREFIX.'buttonstats SET proactive = 999, message = "'.smartsql($row['message']).'", readtime = 0  WHERE session = "'.smartsql($rlbid).'"');
		  				
		  				$this->send($user, json_encode(array('job' => 'check_proactive', 'proactive' => true, "offline" => false, 'message' => $row['message'], 'showalert' => $row['showalert'], 'wayin' => $row['wayin'], 'wayout' => $row['wayout'], "newmsg" => $newConv, "newmsghtml" => $newMSG)));
		  				
		  			}
		  		}
	  			
	  			if ($proactive) {
	  			
	  				// Check if we have an manual proactive
	  				$result = $lsdb->query('SELECT message FROM '.DB_PREFIX.'buttonstats WHERE proactive = 1 AND session = "'.smartsql($rlbid).'" AND readtime = 0');
	  				
	  				if ($lsdb->affected_rows > 0) {
	  				
	  					$row = $result->fetch_assoc();
	  					
	  					$this->send($user, json_encode(array('job' => 'check_proactive', 'proactive' => true, "offline" => false, 'message' => $row['message'], 'showalert' => LS_PRO_ALERT, 'wayin' => LS_PRO_WAYIN, 'wayout' => LS_PRO_WAYOUT, "newmsg" => $newConv, "newmsghtml" => $newMSG)));
	  					
	  				} else {
	  					
	  					$this->send($user, json_encode(array('job' => 'check_proactive', 'proactive' => false, "offline" => false, "newmsg" => $newConv, "newmsghtml" => $newMSG)));
	  					
	  				}
	  			}
	  			
	  			} else {
	  				
	  				$this->send($user, json_encode(array('job' => 'check_proactive', 'proactive' => false, "offline" => true, "newmsg" => $newConv, "newmsghtml" => $newMSG)));
	  				
	  			}
	  			
	  		break;
	  		case 'receive_messages':
	  		
	  			if (is_numeric($obj['sid']) && $obj['uid'] == $usrid) {
	  			
	  			// Get the special lang var once for the time
	  			define('LS_DAY', $tl['general']['g17']);
	  			define('LS_HOUR', $tl['general']['g18']);
	  			define('LS_MINUTE', $tl['general']['g19']);
	  			define('LS_MULTITIME', $tl['general']['g20']);
	  			define('LS_AGO', $tl['general']['g21']);
	  			
	  			$result = $lsdb->query('SELECT * FROM '.DB_PREFIX.'transcript WHERE convid = "'.smartsql($obj['sid']).'" AND plevel = 1 ORDER BY time ASC');
	  			
	  			if ($lsdb->affected_rows > 0) {
	  			
	  			$chat = '<ul class="list-group">';
	  			
	  				while ($row = $result->fetch_assoc()) {
	  			
	  					$chat .= '<li class="list-group-item '.$row['class'].'"><span class="response_sum">'.LS_base::lsTimesince($row['time'], LS_DATEFORMAT, LS_TIMEFORMAT).' '.$row['name'].' '.$tl['general']['g14'].' :</span><br />'.stripcslashes($row['message']).'</li>';	
	  				}
	  				
	  				$chat .= "</ul>";
	  				
	  				$this->send($user, json_encode(array('job' => 'receive_messages', "status" => 1, "html" => $chat)));
	  			}
	  			
	  			} else {
	  				$this->send($user, json_encode(array('job' => 'receive_messages', "status" => 0, "html" => "")));
	  			}
	  			
	  		break;
	  		case 'check_chat_status':
	  		
	  			if (is_numeric($obj['sid']) && $obj['uid'] == $usrid) {
	  			
	  			$otyping = false;
	  			$knockknock = false;
	  			$opern = $tl['general']['g59'];
	  			
	  			$result = $lsdb->query('SELECT t1.id, t1.operatorid, t1.initiated, t1.answered, t1.updated, t1.sendfiles, t1.o_typing, t1.msg_status, t1.denied, t2.name, t2.picture FROM '.DB_PREFIX.'sessions AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON(t1.operatorid = t2. id) WHERE userid = "'.smartsql($obj['uid']).'"');
	  			
	  			if ($lsdb->affected_rows > 0) {
	  			
	  				$newConv = 0;
	  				$scrollNow = 0;
	  				$operatorid = 0;
	  				$showinput = 0;
	  			
	  				$row = $result->fetch_assoc();
	  				
	  				// Get the knock knock
	  				if ($row['knockknock'] == 1) $knockknock = $tl["general"]["g22"];
	  				
	  				// Update the status for better user handling
	  				$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET u_status = "'.time().'", knockknock = 0 WHERE id = "'.$row['id'].'"');
	  				
	  				if ($row['denied'] == 1) {
	  					
	  					$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
	  					name = "'.smartsql($obj['una']).'",
	  					message = "'.smartsql($tl['general']['g57']).'",
	  					user = "'.smartsql($obj['uid']).'",
	  					convid = "'.$row['id'].'",
	  					time = NOW(),
	  					class = "ended"');
	  					
	  					$this->send($user, json_encode(array('job' => 'check_chat_status', 'redirect_c' => true)));
	  					
	  				}
	  					  				
	  				if ($row['answered'] == 0 && $row['msg_status'] == 0 && $row['initiated'] < (time() - 60)) {
	  					
	  					$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
	  					name = "'.smartsql($tl["general"]["g56"]).'",
	  					message = "'.smartsql($tl["general"]["g69"]).'",
	  					convid = "'.$row['id'].'",
	  					time = NOW(),
	  					class = "admin"');
	  					
	  					// update db that we sent the waiting message
	  					$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET msg_status = 1 WHERE id = "'.$row['id'].'"');
	  					
	  					$newConv = 1;
	  					$scrollNow = 1;
	  					
	  				}
	  				
	  				if ($row['answered'] == 0 && $row['msg_status'] == 1 && $row['initiated'] < (time() - 180)) {
	  					
	  					$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
	  					name = "'.smartsql($tl["general"]["g56"]).'",
	  					message = "'.smartsql($tl["general"]["g70"]).'",
	  					convid = "'.$row['id'].'",
	  					time = NOW(),
	  					class = "admin"');
	  					
	  					// update db that we sent the waiting message
	  					$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET msg_status = 2 WHERE id = "'.$row['id'].'"');
	  					
	  					$newConv = 1;
	  					$scrollNow = 1;
	  					
	  				}
	  				
	  				if ($row['answered'] == 0 && $row['msg_status'] == 2 && $row['initiated'] < (time() - 480) && LS_WAIT_MESSAGE3 == 1) {
	  				
	  					$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 0, fcontact = 1, ended = "'.time().'"  WHERE id = "'.$row['id'].'"');
	  					
	  					$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
	  					name = "'.smartsql($obj['una']).'",
	  					message = "'.smartsql($tl['general']['g57']).'",
	  					user = "'.smartsql($obj['uid']).'",
	  					convid = "'.$row['id'].'",
	  					time = NOW(),
	  					class = "ended"');
	  					
	  					$this->send($user, json_encode(array('job' => 'check_chat_status', 'redirect_c' => true)));
	  					
	  				}
	  				
	  				// Check the rest
	  				if ($row['answered'] > $row['updated']) $newConv = 1;
	  					
	  				if ($row['answered'] > (time() - 6)) $scrollNow = 1;
	  					
	  				if ($row['operatorid']) $operatorid = 1;
	  					
	  				if ($row['o_typing']) $otyping = str_replace("%s", $row['name'], $tl["general"]["g37"]);
	  					
	  				if ($row['name']) $opern = $tl["general"]["g52"].': '.$row['name'];
	  					
	  				if ($row['answered'] != 0) $showinput = 1;
	  				
	  				$this->send($user, json_encode(array('job' => 'check_chat_status', 'redirect_c' => false, 'knockknock' => $knockknock, 'operator' => $operatorid, 'newmsg' => $newConv, 'scrollnow' => $scrollNow, 'files' => $row['sendfiles'], 'typing' => $otyping, 'oname' => $opern, 'opicture' => $row['picture'], 'showinput' => $showinput)));
	  			} else {
	  			
	  				$this->send($user, json_encode(array('job' => 'check_chat_status', 'redirect_c' => false, 'knockknock' => $knockknock, 'operator' => 0, 'newmsg' => 0, 'scrollnow' => 0, 'files' => 0, 'typing' => $otyping, 'oname' => false, 'opicture' => false, 'showinput' => false)));
	  			}
	  			
	  			} else {
	  				
	  				$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 0, ended = "'.time().'"  WHERE id = "'.$row['id'].'"');
	  				
	  				$this->send($user, json_encode(array('job' => 'check_chat_status', "redirect_c" => false)));
	  			}
	  			
	  		break;
	  		case 'send_message':
	  		
	  			if (is_numeric($obj['sid']) && $obj['uid'] == $usrid) {
	  			
	  			$message = strip_tags($obj['msg']);
	  			
	  			if ($message) {
	  			
	  			$result = $lsdb->query('SELECT * FROM '.DB_PREFIX.'sessions WHERE userid = "'.smartsql($obj['uid']).'"');
	  			
	  			if ($lsdb->affected_rows > 0) {
	  			
	  				$row = $result->fetch_assoc();
	  				
	  					define('BASE_URL_IMG', str_replace($socket_url, SOCKET_SUBFOLDER_IF, BASE_URL));
	  					
	  					$message = filter_var($message, FILTER_SANITIZE_STRING);
	  					
	  					$message = trim($message);
	  					
	  					$message = nl2br(replace_urls($message));
	  					
	  					if (LS_SMILIES) {
	  				
	  						require_once $final_url.'class/class.smileyparser.php';
	  						
	  						// More dirty custom work and smiley parser
	  						$smileyparser = new LS_smiley(); 
	  						$message = $smileyparser->parseSmileytext($message);
	  					
	  					}
	  					
	  					if ($row['status'] && $message != "") {
	  					
	  						$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
	  						name = "'.smartsql($obj['una']).'",
	  						message = "'.smartsql($message).'",
	  						user = "'.smartsql($obj['uid']).'",
	  						convid = "'.smartsql($obj['sid']).'",
	  						time = NOW(),
	  						class = "user"');
	  						
	  						$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
	  						updated = "'.time().'",
	  						u_typing = 0
	  						WHERE userid = "'.smartsql($obj['uid']).'"');
	  						
	  						$this->send($user, json_encode(array('job' => 'send_message', "status" => 1, "msg" => '<li class="list-group-item user"><span class="response_sum">'.LS_base::lsTimesince(time(), LS_DATEFORMAT, LS_TIMEFORMAT).' '.$obj['una'].' '.$tl['general']['g14'].' :</span><br />'.stripcslashes($message).'</li>')));
	  					
	  					} elseif (!$row['status'] && !$row['hide']) {
	  					
	  						$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
	  						name = "'.smartsql($obj['una']).'",
	  						message = "'.smartsql($message).'",
	  						user = "'.smartsql($obj['uid']).'",
	  						convid = "'.smartsql($obj['sid']).'",
	  						time = NOW(),
	  						class = "user"');
	  						
	  						$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
	  						updated = "'.time().'",
	  						ended = 0,
	  						status = 1,
	  						u_typing = 0
	  						WHERE userid = "'.smartsql($obj['uid']).'"');
	  						
	  						$this->send($user, json_encode(array('job' => 'send_message', "status" => 1, "msg" => '<li class="list-group-item user"><span class="response_sum">'.LS_base::lsTimesince(time(), LS_DATEFORMAT, LS_TIMEFORMAT).' '.$obj['una'].' '.$tl['general']['g14'].' :</span><br />'.stripcslashes($message).'</li>')));
	  						
	  					} elseif (!$row['status']) {
	  					
	  						$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
	  						name = "'.smartsql($obj['una']).'",
	  						message = "'.smartsql($tl['general']['g13']).'",
	  						user = "'.smartsql($obj['uid']).'",
	  						convid = "'.smartsql($obj['sid']).'",
	  						time = NOW(),
	  						class = "notice"');
	  						
	  						$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
	  						updated = "'.time().'",
	  						ended = 0,
	  						u_typing = 0
	  						WHERE userid = "'.smartsql($obj['uid']).'"');
	  						
	  						$this->send($user, json_encode(array('job' => 'send_message', "status" => 1, "msg" => '<li class="list-group-item notice"><span class="response_sum">'.LS_base::lsTimesince(time(), LS_DATEFORMAT, LS_TIMEFORMAT).' '.$obj['una'].' '.$tl['general']['g14'].' :</span><br />'.stripcslashes($tl['general']['g13']).'</li>')));
	  						
	  					} else {
	  					
	  						$this->send($user, json_encode(array('job' => 'send_message', "status" => 0, "msg" => $tl['error']['e2'])));
	  					}
	  					
	  					
	  				}
	  				
	  				} else {
	  					$this->send($user, json_encode(array('job' => 'send_message', "status" => 0, "msg" => $tl['error']['e2'])));
	  				}
	  				
	  				} else {
	  					$this->send($user, json_encode(array('job' => 'send_message', "status" => 0, "msg" => $tl['error']['e2'])));
	  				}
	  			
	  		break;
	  		case 'usr_typing':
	  		
	  			if (is_numeric($obj['sid']) && $obj['uid'] == $usrid) {
	  		
	  			if ($obj['status'] == 1) {
	  				$result = $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET u_typing = 1 WHERE id = "'.smartsql($obj['sid']).'"');
	  			} else {
	  				$result = $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET u_typing = 0 WHERE id = "'.smartsql($obj['sid']).'"');
	  			}
	  			
	  			if ($result) {
	  				$this->send($user, json_encode(array('job' => 'usr_typing', 'tid' => 1)));
	  			} else {
	  				$this->send($user, json_encode(array('job' => 'usr_typing', 'tid' => 0)));
	  			}
	  			
	  			} else {
	  				$this->send($user, json_encode(array('job' => 'usr_typing', "tid" => 0)));
	  			}
	  			
	  		break;
	  		default:
	  		
	  	}
	 }
  }
  
  protected function connected ($user) {
    // Do nothing: This is just an echo server, there's no need to track the user.
    // However, if we did care about the users, we would probably have a cookie to
    // parse at this step, would be looking them up in permanent storage, etc.
  }
  
  protected function closed ($user) {
    // Do nothing: This is where cleanup would go, in case the user had any sort of
    // open files or other objects associated with them.  This runs after the socket 
    // has been closed, so there is no need to clean up the socket itself here.
  }
}

$echo = new echoServer('0.0.0.0', 9000);

try {
  $echo->run();
}
catch (Exception $e) {
  $echo->stdout($e->getMessage());
}
?>