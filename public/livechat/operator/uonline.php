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

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('LS_ADMIN_PREVENT_ACCESS')) die('You cannot access this file directly.');

// Check if the user has access to this file
if (!LS_USERID_RHINO || !LS_ADMINACCESS) ls_redirect(BASE_URL);

$lstable = DB_PREFIX.'buttonstats';
$lstable1 = DB_PREFIX.'user_stats';
$lstable2 = DB_PREFIX.'user';
$lstable3 = DB_PREFIX.'sessions';

switch ($page1) {
	case 'opstat':
	
		if (is_numeric($page2) && $lsuser->getVar("operatorlist")) {
			
			$result = $lsdb->query('SELECT id, username, name FROM '.$lstable2.' WHERE id = "'.smartsql($page2).'"');
			$row = $result->fetch_assoc();
			
			$result1 = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable3.' WHERE operatorid = "'.smartsql($page2).'" AND status = 1');
			$row1 = $result1->fetch_assoc();
			
			$result2 = $lsdb->query('SELECT COUNT(*) as totalAll, SUM(vote) AS total_vote, SUM(support_time) AS total_support FROM '.$lstable1.' WHERE userid = "'.smartsql($page2).'"');
			$row2 = $result2->fetch_assoc();
			
		}
		
		// Call the template
		$template = 'opstat.php';
	break;
	case 'delete':
	
		if (!LS_SUPERADMINACCESS) ls_redirect(BASE_URL);
   		
       	$result = $lsdb->query('DELETE FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"');
		
		if (!$result) {
   			ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		} else {
       		ls_redirect(BASE_URL.'index.php?p=success');
   		} 
  	break;
  	case 'truncate':
  	
  		if (!LS_SUPERADMINACCESS) ls_redirect(BASE_URL);
  		
  	    $result = $lsdb->query('TRUNCATE '.$lstable);
  		
  		if (!$result) {
  			ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
  		} else {
  		    ls_redirect(BASE_URL.'index.php?p=success');
  		}
  		 
  	break;
  	default:
		
		// Now only get the department for the user
		if ($_SESSION['usr_department'] && is_numeric($_SESSION['usr_department'])) {
			$sqluo = ' WHERE depid = '.smartsql($_SESSION['usr_department']);
		}
		if ($_SESSION['usr_department']) {
			$sqluo = ' WHERE depid IN('.smartsql($_SESSION['usr_department']).')';
		}
		if ($_SESSION['usr_department'] == 0) {
			$sqluo = ' WHERE depid >= 0';
		}
		
		$total = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable.$sqluo);
		$rowt = $total->fetch_assoc();
		 
		//break total records into pages
		$total_pages = ceil($rowt['totalAll']/20);
		
		// Call the template
		$template = 'uonline.php';
}
?>