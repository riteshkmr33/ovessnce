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

if (!ls_get_access("leads_all", $lsuser->getVar("permissions"), $_SESSION['ls_superoperator'])) $sqlw = ' WHERE operatorid = "'.smartsql($_SESSION['ls_opid']).'"';

if ($_POST["page2"] && ($_POST["page3"] == "ASC" || $_POST["page3"] == "DESC")) {
	$sqlorderby = $_POST["page2"].' '.$_POST["page3"];
} else {
	$sqlorderby = 'initiated DESC';
}

$result = $lsdb->query('SELECT t1.id, t1.name, t1.email, t1.department, t1.operatorid, t1.ip, t1.initiated, t1.fcontact, t1.notes, t1.countrycode, t1.country, t1.city, t2.username, t3.title FROM '.DB_PREFIX.'sessions AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON (t1.operatorid = t2.id) LEFT JOIN '.DB_PREFIX.'departments AS t3 ON (t1.department = t3.id)'.$sqlw.' ORDER BY '.$sqlorderby.' LIMIT '.$position.', 10');
	
	while ($row = $result->fetch_assoc()) {
		
		// refresh vars
		$class = '';
		$country = '';
		$sendmail = '';
		$classb = ' btn-default';
		$superop = '';
		
		if ($row["fcontact"] == 1) $class = ' class="danger"';
		if ($row["countrycode"] != xx) $country = ' <img src="img/country/'.$row['countrycode'].'.gif" alt="nocountry" title="'.$row['country'].'/'.$row['city'].'" />';
		if (filter_var($row['email'], FILTER_VALIDATE_EMAIL)) $sendmail = ' <a class="btn btn-default btn-xs" data-toggle="modal" href="index.php?p=leads&amp;sp=clientcontact&amp;ssp='.$row["id"].'&amp;sssp=1" data-target="#generalModal"><span class="glyphicon glyphicon-envelope"></span></a>';
		if ($row["notes"] == 1) $class = ' btn-success';
		if (LS_SUPERADMINACCESS) $superop = '<a class="btn btn-default btn-xs" href="index.php?p=leads&amp;sp=delete&amp;ssp='.$row["id"].'" onclick="if(!confirm("'.$tl["error"]["e33"].'"))return false;"><span class="glyphicon glyphicon-trash"></span></a>';
	
		$loadcontent .= '<tr'.$class.'>
		<td>'.$row["id"].'</td>
		<td><input type="checkbox" name="ls_delete_leads[]" class="highlight" value="'.$row["id"].'"></td>
		<td>'.$row["name"].$country.'</td>
		<td>'.$row["email"].$sendmail.'</td>
		<td><a href="index.php?p=leads&amp;sp=operator&amp;ssp='.$row["operatorid"].'">'.$row["username"].'</a></td>
		<td><a href="index.php?p=leads&amp;sp=departement&amp;ssp='.$row["department"].'">'.$row["title"].'</a></td>
		<td><a class="btn btn-default btn-xs" data-toggle="modal" href="index.php?p=leads&amp;sp=readleads&amp;ssp='.$row["id"].'&amp;sssp=1" data-target="#generalModal"><span class="glyphicon glyphicon-eye-open"></span></a></td>
		<td><a class="btn btn-default btn-xs" data-toggle="modal" href="index.php?p=leads&amp;sp=location&amp;ssp='.$row['id'].'" data-target="#generalModal"><span class="glyphicon glyphicon-globe"></span></a></td>
		<td><a class="btn'.$classb.' btn-xs" data-toggle="modal" href="index.php?p=notes&amp;sp='.$row["id"].'" data-target="#generalModal"><span class="glyphicon glyphicon-comment"></span></a></td>
		<td>'.LS_base::lsTimesince($row['initiated'], LS_DATEFORMAT, LS_TIMEFORMAT).'</td>
		<td>'.$row["ip"].'</td>
		<td>'.$superop.'</td>
		<td></td>
		</tr>';
	
	}
	
echo $loadcontent;	
?>