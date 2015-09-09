<?php

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

if (!file_exists('../../config.php')) die('ajax/[available.php] config.php not exist');
require_once '../../config.php';

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['idhash'])) die("Nothing to see here");

if (file_exists(APP_PATH.'operator/lang/'.LS_LANG.'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    trigger_error('Translation file not found');
}

if (!is_numeric($_POST['id'])) die(json_encode(array('status' => 1, "html" => $tl['general']['g79'])));

$result = $lsdb->query('SELECT name, email, convid, initiated FROM '.DB_PREFIX.'jrc_sessions WHERE convID = "'.smartsql($_POST['id']).'"');

if ($lsdb->affected_rows > 0) {

	$row = $result->fetch_assoc();

	$ts = $row['initiated'];
	$ts = strftime("%X %P",$ts);

$userinfo = '<table class="table table-bordered">
<tr>
<th>'.$tl['user']['u'].'</th>
<th>'.$tl['user']['u1'].'</th>
<th>'.$tl['general']['g61'].'</th>
</tr><tr>
<td>'.$row['name'].'</td>
<td>'.$row['email'].'</td>
<td><a data-toggle="modal" href="ajax/delconv.php?id='.$row['convid'].'" data-target="#inchatModal">'.$tl['general']['g62'].'</a></td>
</tr>
</table>';

echo json_encode(array('status' => 1, "html" => $userinfo));

}
?>