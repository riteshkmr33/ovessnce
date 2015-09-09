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

if (!file_exists('../../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if (!is_numeric($_GET['id'])) die("There is no such user!");

$sqlw = '';

// Now only get the department for the user
if ($_SESSION['usr_department'] && is_numeric($_SESSION['usr_department'])) {
	$sqlw = 'department = '.smartsql($_SESSION['usr_department']).' AND status = 1 AND operatorid = 0 OR ';
}
if ($_SESSION['usr_department']) {
	$sqlw = 'department IN('.smartsql($_SESSION['usr_department']).') AND status = 1 AND operatorid = 0 OR ';
}
if ($_SESSION['usr_department'] == 0) {
	$sqlw = 'department >= 0 AND status = 1 AND operatorid = 0 OR ';
}

$result = $lsdb->query('SELECT id, operatorid, answered, updated, transferid, transfermsg FROM '.DB_PREFIX.'sessions WHERE '.$sqlw.'operatorid = '.smartsql($_GET['id']).' AND status = 1 OR department = 0 AND status = 1 AND operatorid = 0 OR transferid = '.smartsql($_GET['id']).' AND status = 1');

if ($lsdb->affected_rows > 0) {

	while ($row = $result->fetch_assoc()) {
		
		// We have a transfer, need to display it!
		if ($row['transferid'] == $_GET['id']) {
			
			if ($row["transfermsg"]) $split_transfer_msg = explode(':#:', $row["transfermsg"]);
			
			// Display underneath the button
			$transfer_msg = '<p>'.$split_transfer_msg[1].' <a href="javascript:void(0)" onclick="acceptTransfer(0, '.$row['transferid'].', '.$row['id'].');"><span class="glyphicon glyphicon-remove"></span></a> <a href="javascript:void(0)" onclick="acceptTransfer(1, '.$row['transferid'].', '.$row['id'].');"><span class="glyphicon glyphicon-ok"></span></a></p>';
			$transferid = $row['transferid'];
		}
			
			$newConv = 0;
		
			// check for new conversations
			if ($row['operatorid'] == 0) {
				$newConv = 1;
			}
			if ($row['operatorid'] > 0 && ($row['updated'] > $row['answered'])) {
				$newConv = 2;
			}		
	}
	
	echo json_encode(array('newc' => $newConv, 'tid' => $transferid, 'tmsg' => $transfer_msg));
} else {

	echo json_encode(array('newc' => 0, 'tid' => 0, 'tmsg' => 0));
}
?>