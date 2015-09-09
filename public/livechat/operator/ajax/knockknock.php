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

if (!is_numeric($_GET['id'])) die("There is no such thing!");

if ($_SESSION['lc_ulang'] && file_exists(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'operator/lang/en.ini', true);
}
	
	$result = $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET knockknock = 1  WHERE id = "'.smartsql($_GET['id']).'"');
	
	if ($result) {
		echo '<div class="modal-header">
		      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		      <h4 class="modal-title">'.$tl['general']['g223'].'</h4>
		    </div>
		    <div class="modal-body"><div class="padded-box"><div class="alert alert-success">'.$tl['general']['g14'].'</div></div></div>
		        	<div class="modal-footer">
		        		<button type="button" class="btn btn-default" data-dismiss="modal">'.$tl["general"]["g180"].'</button>
			  </div>';
	} else {
		echo '<div class="modal-header">
		      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		      <h4 class="modal-title">'.$tl['general']['g223'].'</h4>
		    </div>
		    <div class="modal-body"><div class="padded-box"><div class="alert alert-error">'.$tl['errorpage']['sql'].'</div></div></div>
		        	<div class="modal-footer">
		        		<button type="button" class="btn btn-default" data-dismiss="modal">'.$tl["general"]["g180"].'</button>
			  </div>';
	}
?>