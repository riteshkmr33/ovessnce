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

	$lstable = DB_PREFIX.'sessions';
	$lstable1 = DB_PREFIX.'transcript';
	$alluLeads = false;
	
	// Get the special lang var once for the time
	define('LS_DAY', $tl['general']['g74']);
	define('LS_HOUR', $tl['general']['g75']);
	define('LS_MINUTE', $tl['general']['g76']);
	define('LS_MULTITIME', $tl['general']['g77']);
	define('LS_AGO', $tl['general']['g78']);
	
	$result = $lsdb->query('SELECT name, email, ip FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($_GET['id']).'"');
	
	if ($lsdb->affected_rows > 0) {
	
		$row = $result->fetch_assoc();

	if (!LS_SUPERADMINACCESS) $sqlw = ' AND operatorid = "'.LS_USERID_RHINO.'"';
	
	$resultr = $lsdb->query('SELECT t1.id, t1.name, t1.email, t1.department, t1.operatorid, t1.initiated, t2.username, t3.title FROM '.$lstable.' AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON (t1.operatorid = t2.id) LEFT JOIN '.DB_PREFIX.'departments AS t3 ON (t1.department = t3.id) WHERE t1.id != "'.smartsql($_GET['id']).'" AND (t1.ip = "'.$row["ip"].'" OR t1.email = "'.$row["email"].'")'.$sqlw.' ORDER BY initiated DESC LIMIT 10');
	
	while ($rowr = $resultr->fetch_assoc()) {
		$alluLeads .= '<tr><td>'.$rowr["id"].'</td><td><a href="index.php?p=leads&amp;sp=readleads&amp;ssp='.$rowr["id"].'">'.$rowr["name"].'</a></td><td>'.LS_base::lsTimesince($rowr['initiated'], LS_DATEFORMAT, LS_TIMEFORMAT).'</td>';
	}
	
	}
	
	if ($alluLeads) {

		echo '<div class="padded-box">';
	    echo '<h3>'.$tl["menu"]["m1"].' - '.$row["name"].'</h3>';
	    echo '<table class="table table-striped">';
	    echo $alluLeads;
	    echo '</table>';
	    echo '</div>';
	        
	} else {
		
		echo '<div class="padded-box">';
		echo '<div class="alert alert-info"><h3>'.$tl["menu"]["m1"].' - '.$row["name"].'</h3>'.$tl["errorpage"]["data"].'</div>';
		echo '</div>';
	
	}
}
?>