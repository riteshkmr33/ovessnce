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

if (!isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if ($_SESSION['lc_ulang'] && file_exists(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini')) {
    $tl = parse_ini_file(APP_PATH.'operator/lang/'.$_SESSION['lc_ulang'].'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'operator/lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'operator/lang/en.ini', true);
}

if (is_numeric($_GET['id']) && is_numeric($_GET['userid'])) {

$timeout = 180;
$udepl = '';

$result = $lsdb->query('SELECT id, departments, username, name, available, lastactivity FROM '.DB_PREFIX.'user WHERE access = 1 AND available = 1 AND id != '.smartsql($_GET['userid']));

$resultd = $lsdb->query('SELECT id, title FROM '.DB_PREFIX.'departments ORDER BY dorder ASC');
while ($rowd = $resultd->fetch_assoc()) {
        // collect each record into $_data
        $lsdata[] = $rowd;
}
	
if ($lsdb->affected_rows > 0) {
	while($row = $result->fetch_assoc()) {
	
		if (time() > ($row['lastactivity'] + $timeout)) {
			$lsdb->query('UPDATE '.DB_PREFIX.'user SET available = 0 WHERE id = "'.$row['id'].'"');
		}
		
		if ($row["departments"] == 0) {
			$udep = $tl['general']['g105'];
		} else {
		
			if (isset($lsdata) && is_array($lsdata)) foreach($lsdata as $z) {
			
				if (in_array($z["id"], explode(',', $row["departments"]))) {
				
					$udepl[] = $z["title"];
				
				}
			
			}
		
		}
		
		if (!empty($udepl) && is_array($udepl)) $departmentlist = join(", ", $udepl);
		
		if ($departmentlist) $udep = $tl['menu']['m9'].': '.$departmentlist;
		
		$operator .= '<option value="'.$row['id'].'">'.$row['name'].' - '.$row['username'].' ('.$udep.')</option>';
	
	}
}

if ($operator) {
	$oselect = $operator;
	$showbutton = '<hr><button class="btn btn-primary btn-block" type="submit" name="transfer_customer">'.$tl['general']['g4'].'</button>';
	
} else {
	$oselect = '<option value="0">'.$tl['general']['g114'].'</option>';
	$showbutton = '';
}

	echo '<div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	      <h4 class="modal-title">'.LS_TITLE.'</h4>
	    </div>
	    <div class="modal-body"><div class="padded-box"><form method="post" role="form" action="index.php">';
	        echo '<p>'.$tl['general']['g112'].'</p>';
	        echo '
	        <label for="transfermsg">'.$tl['general']['g113'].'</label>
	        <input type="text" name="transfermsg" id="transfermsg" value="" class="form-control" />
	        <label for="operator">'.$tl['general']['g106'].'</label>
	        <select name="operator" id="operator" class="form-control">
	        '.$oselect.'
	        </select>
	        <input type="hidden" name="cid" id="cid" value="'.$_GET['id'].'" />
	        <input type="hidden" name="userid" id="userid" value="'.$_GET['userid'].'" />
	        
	        '.$showbutton.'
	        
	        </form></div></div>
	        	<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal">'.$tl["general"]["g180"].'</button>
	        	</div>';
	
}
?>