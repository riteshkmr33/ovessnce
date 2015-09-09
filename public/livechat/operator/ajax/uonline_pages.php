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

// Now only get the department for the user
if ($_SESSION['usr_department'] && is_numeric($_SESSION['usr_department'])) {
	$sqluo = ' WHERE depid = '.smartsql($_SESSION['usr_department']);
}
if ($_SESSION['usr_department']) {
	$sqluo = ' WHERE depid IN('.smartsql($_SESSION['usr_department']).')';
}
if ($_SESSION['usr_department'] == 0) {
	$sqluo = ' WHERE depid >= 0';
}

//get current starting point of records
$position = ($page_number * 10);

$result = $lsdb->query('SELECT t1.id, t1.referrer, t1.firstreferrer, t1.agent, t1.hits, t1.ip, t1.lasttime, t1.time, t1.proactive, t1.readtime, t2.initiated, t2.ended FROM '.DB_PREFIX.'buttonstats AS t1 LEFT JOIN '.DB_PREFIX.'sessions AS t2 ON (t1.session = t2.session)'.$sqluo.' GROUP BY t1.session ORDER BY t1.lasttime DESC LIMIT '.$position.', 10');
	
	while ($row = $result->fetch_assoc()) {
		
		// refresh vars
		$superop = '';

		if ($_SESSION['ls_superoperator'] == false) $superop = '<a href="index.php?p=uonline&amp;sp=delete&amp;ssp='.$row["id"].'" class="btn btn-default btn-xs" onclick="if(!confirm('.$tl["error"]["e30"].'))return false;"><span class="glyphicon glyphicon-trash"></span></a>';
	
		$loadcontent .= '<tr>
		<td>'.$row["id"].'</td>
		<td>'.$row["referrer"].'</td>
		<td>'.$row["firstreferrer"].'</td>
		<td>'.$row["agent"].'</td>
		<td>'.$row["hits"].'</td>
		<td>'.$row["ip"].'</a></td>
		<td>'.LS_base::lsTimesince($row['time'], LS_DATEFORMAT, LS_TIMEFORMAT).'</td>
		<td>'.LS_base::lsTimesince($row['lasttime'], LS_DATEFORMAT, LS_TIMEFORMAT).'</td>
		<td>'.$superop.'</td>
		</tr>';
	
	}
	
echo $loadcontent;	
?>