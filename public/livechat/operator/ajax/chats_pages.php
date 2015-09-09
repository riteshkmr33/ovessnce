<?php

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

if(!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

// Filter numbers
$page_number = filter_var($_POST["pagenumber"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);

if (!is_numeric($page_number)) die('Wrong page number');

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

//get current starting point of records
$position = ($page_number * 10);

// Operator Access
$sqlw = '';
$loadcontent = '';
if (!ls_get_access("ochat_all", $lsuser->getVar("permissions"), $_SESSION['ls_superoperator'])) $sqlw = ' WHERE fromid = "'.smartsql($_SESSION['ls_opid']).'" OR toid = "'.smartsql($_SESSION['ls_opid']).'"';

if ($_POST["page2"] && ($_POST["page3"] == "ASC" || $_POST["page3"] == "DESC")) {
	$sqlorderby = $_POST["page2"].' '.$_POST["page3"];
} else {
	$sqlorderby = 'sent DESC';
}

$result = $lsdb->query('SELECT t1.id, t1.fromid, t1.toid, t1.message, t1.sent, t2.username, t3.username AS touser FROM '.DB_PREFIX.'operatorchat AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON (t1.fromid = t2.id) LEFT JOIN '.DB_PREFIX.'user AS t3 ON (t1.toid = t3.id)'.$sqlw.' ORDER BY '.$sqlorderby.' LIMIT '.$position.', 10');
	
	while ($row = $result->fetch_assoc()) {
		
		// refresh vars
		$superop = '';
		
		if (LS_SUPERADMINACCESS) $superop = '<a class="btn btn-default btn-xs" href="index.php?p=chats&amp;sp=delete&amp;ssp='.$row["id"].'" onclick="if(!confirm('.$tl["error"]["e33"].'))return false;"><span class="glyphicon glyphicon-trash"></span></a>';
	
		$loadcontent .= '<tr>
		<td>'.$row["id"].'</td>
		<td><input type="checkbox" name="ls_delete_chats[]" class="highlight" value="'.$row["id"].'" /></td>
		<td>'.$row["username"].'</td>
		<td>'.$row["touser"].'</td>
		<td class="span8">'.$row["message"].'</td>
		<td>'.LS_base::lsTimesince($row['sent'], LS_DATEFORMAT, LS_TIMEFORMAT).'</td>
		<td>'.$superop.'</td>
		</tr>';
	
	}
	
echo $loadcontent;	
?>