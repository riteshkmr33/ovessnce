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

if ($_SESSION['lc_ulang'] && file_exists(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'operator/lang/en.ini', true);
}

if (!is_numeric($_POST['id'])) die(json_encode(array('status' => 1, "html" => $tl['general']['g79'])));

$result = $lsdb->query('SELECT t1.id, t1.operatorid, t1.name, t1.email, t1.phone, t1.ip, t1.country, t1.city, t1.countrycode, t1.referrer, t1.sendfiles, t1.initiated, t2.title AS dep_title FROM '.DB_PREFIX.'sessions AS t1 LEFT JOIN '.DB_PREFIX.'departments AS t2 ON (t1.department = t2.id) WHERE t1.id = "'.smartsql($_POST['id']).'"');

if ($lsdb->affected_rows > 0) {
	
	$row = $result->fetch_assoc();
	
	$showphone = '';
	$showemail = '';
	
	// Write the session id for uploading files
	$_SESSION["oconvId"] = $row["id"];
	
	$usrc = '<img src="img/country/xx.gif" width="16" height="11" alt="nocountry" /> '.$tl['general']['g11'].': '.$row['ip'];
	
	$usrf = '<a href="javascript:void(0)" onclick="usrFiles('.$row['id'].');" id="user_files"><span class="glyphicon glyphicon-lock"></span> '.$tl['user']['u9'].'</a>';
	
	if ($row['countrycode'] != 'xx') {
		$usrc = '<img src="img/country/'.$row['countrycode'].'.gif" alt="nocountry" title="'.$row['country'].'/'.$row['city'].'" /> '.$tl['general']['g11'].': '.$row['ip'];
	}
	
	if ($row['sendfiles']) {
		$usrf = '<a href="javascript:void(0)" onclick="usrFiles('.$row['id'].');" id="user_files"><span class="glyphicon glyphicon-ok"></span> '.$tl['user']['u9'].'</a>';
	}
	
	if ($row['phone']) $showphone = '<br /><span class="glyphicon glyphicon-headphones"></span> '.$row['phone'];
	if (filter_var($row['email'], FILTER_VALIDATE_EMAIL)) $showemail = ' <br /><span class="glyphicon glyphicon-envelope"></span> '.$row['email'];
	
	$userinfo = '<table class="table table-bordered">
<tr>
<td rowspan="2" style="text-align: center;">'.get_gravatar($row['email']).'<br />'.$row['name'].'</td>
<td>'.$usrc.'</td>

<td>'.$usrf.'</td>
<td><a data-toggle="modal" href="ajax/transfer.php?id='.$row['id'].'&amp;userid='.$row['operatorid'].'" data-target="#generalModal"><span class="glyphicon glyphicon-share-alt"></span> '.$tl['general']['g110'].'</a></td>
<td><a data-toggle="modal" href="ajax/delconv.php?id='.$row['id'].'" data-target="#generalModal"><span class="glyphicon glyphicon-off"></span> '.$tl['general']['g62'].'</a></td>
</tr>
<tr>
<td colspan="2">'.$tl["general"]["g156"].$row['referrer'].'<br />'.$tl["general"]["g120"].$row["dep_title"].$showphone.$showemail.'</td>
<td colspan="2"><a data-toggle="modal" href="ajax/knockknock.php?id='.$row['id'].'" data-target="#generalModal"><span class="glyphicon glyphicon-bell"></span> '.$tl['general']['g223'].'</a><br /><a data-toggle="modal" href="index.php?p=leads&amp;sp=location&amp;ssp='.$row['id'].'" data-target="#generalModal"><span class="glyphicon glyphicon-globe"></span> '.$tl['general']['g224'].'</a><br /><a data-toggle="modal" href="index.php?p=leads&amp;sp=history&amp;ssp='.$row['id'].'" data-target="#generalModal"><span class="glyphicon glyphicon-file"></span> '.$tl['general']['g55'].'</a><br /><a data-toggle="modal" href="index.php?p=notes&amp;sp='.$row['id'].'" data-target="#generalModal"><span class="glyphicon glyphicon-comment"></span> '.$tl['general']['g181'].'</a></td>
</tr>
</table>';

echo json_encode(array('status' => 1, "html" => $userinfo, "timeonchat" => date("Y-m-d H:i:s", $row['initiated']), "seconds" => $tl['general']['g196'], "minutes" => $tl['general']['g76'].$tl['general']['g77'], "hours" => $tl['general']['g75'].$tl['general']['g77']));

}
?>