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

if (!$_SERVER['HTTP_X_REQUESTED_WITH'] && !isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if (is_numeric($_POST['conv'])) {

if ($_POST['status'] == 1) {

$result = $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET o_typing = 1 WHERE id = "'.smartsql($_POST['conv']).'"');

} else {

$result = $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET o_typing = 0 WHERE id = "'.smartsql($_POST['conv']).'"');

}

if ($result) {
	die(json_encode(array('tid' => 1)));
}

} else {
	die(json_encode(array('tid' => 0)));
}
?>