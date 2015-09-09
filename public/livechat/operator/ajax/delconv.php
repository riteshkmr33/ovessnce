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

if(!isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if ($_SESSION['lc_ulang'] && file_exists(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'operator/lang/en.ini', true);
}

if (is_numeric($_GET['id'])) {

	echo '<div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	      <h4 class="modal-title">'.LS_TITLE.'</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="padded-box">
	    	<form method="post" role="form" action="index.php">';
	echo '<p>'.$tl['error']['e35'].'</p>';
	echo '<input type="hidden" name="id" id="id" value="'.$_GET['id'].'" />
	      <button class="btn btn-primary btn-block" type="submit" name="delete_conv" value="delete">'.$tl['general']['g19'].'</button>
	        
	        </form></div></div>
	        	<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal">'.$tl["general"]["g180"].'</button>
	        	</div>';
}
?>