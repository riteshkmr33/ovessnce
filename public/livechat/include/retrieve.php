<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 6 May 1980 03:10:00 GMT");

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

if (!file_exists('../config.php')) die('ajax/[retrieve.php] config.php not exist');
require_once '../config.php';

// Import the language file
if ($BT_LANGUAGE && file_exists(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini')) {
    $tl = parse_ini_file(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'lang/en.ini', true);
}

// Get the special lang var once for the time
define('LS_DAY', $tl['general']['g17']);
define('LS_HOUR', $tl['general']['g18']);
define('LS_MINUTE', $tl['general']['g19']);
define('LS_MULTITIME', $tl['general']['g20']);
define('LS_AGO', $tl['general']['g21']);

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] || !isset($_SESSION['jrc_userid'])) die(json_encode(array("status" => 0, "html" => "")));

if (is_numeric($_POST['id']) && ($_SESSION['jrc_userid'] == $_POST['userid'])) {

$result = $lsdb->query('SELECT * FROM '.DB_PREFIX.'transcript WHERE convid = "'.smartsql($_POST['id']).'" AND plevel = 1 ORDER BY time ASC');

if ($lsdb->affected_rows > 0) {

$chat = '<ul class="list-group">';

	while ($row = $result->fetch_assoc()) {

		$chat .= '<li class="list-group-item '.$row['class'].'"><span class="response_sum">'.LS_base::lsTimesince($row['time'], LS_DATEFORMAT, LS_TIMEFORMAT).' '.$row['name'].' '.$tl['general']['g14'].' :</span><br />'.stripcslashes($row['message']);	
	}
	
	$chat .= "</ul>";
	
	echo json_encode(array("status" => 1, "html" => $chat));
}
}
?>