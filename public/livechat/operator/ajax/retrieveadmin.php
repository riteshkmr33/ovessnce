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

if (!file_exists('../../config.php')) {
    die('ajax/[available.php] config.php not exist');
}
require_once '../../config.php';

if(!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) {
	die("Nothing to see here");
}

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

if (!is_numeric($_POST['id'])) {

	$chatmsg = '';
	$statusmsg = false;
	$chatended = false;
	
} else {

$result = $lsdb->query('SELECT id, class, name, message, time FROM '.DB_PREFIX.'transcript WHERE convid = "'.smartsql($_POST['id']).'" ORDER BY time ASC');

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

echo json_encode(array('status' => $statusmsg, 'chatended' => $chatended, 'chat' => $chatmsg));

?>