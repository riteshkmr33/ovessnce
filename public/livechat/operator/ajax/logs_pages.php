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

$result = $lsdb->query('SELECT * FROM '.DB_PREFIX.'loginlog ORDER BY time DESC LIMIT '.$position.', 10');
	
	while ($row = $result->fetch_assoc()) {
		
		// refresh vars
		$class = 'lock';
		
		if ($row["access"]) $class = 'ok';
	
		$loadcontent .= '<tr>
		<td>'.$row["id"].'</td>
		<td><input type="checkbox" name="ls_delete_log[]" class="highlight" value="'.$row["id"].'" /></td>
		<td>'.$row["name"].'</td>
		<td>'.$row["fromwhere"].'</td>
		<td>'.$row["ip"].'</td>
		<td>'.$row["usragent"].'</td>
		<td>'.LS_base::lsTimesince($row["time"], LS_DATEFORMAT, LS_TIMEFORMAT).'</td>
		<td><span class="glyphicon glyphicon-'.$class.'"></span></td>
		<td></td>
		<td><a class="btn btn-default btn-xs" href="index.php?p=logs&amp;sp=delete&amp;ssp='.$row["id"].'" onclick="if(!confirm('.$tl["error"]["e33"].'))return false;"><span class="glyphicon glyphicon-trash"></span></a></td>
		</tr>';
	
	}
	
echo $loadcontent;	
?>