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
if (!ls_get_access("ochat", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) ls_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'operatorchat';

// Get the special lang var once for the time
define('LS_DAY', $tl['general']['g74']);
define('LS_HOUR', $tl['general']['g75']);
define('LS_MINUTE', $tl['general']['g76']);
define('LS_MULTITIME', $tl['general']['g77']);
define('LS_AGO', $tl['general']['g78']);

switch ($page1) {
	case 'delete':
	
		if (!ls_get_access("ochat_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) ls_redirect(BASE_URL);
   		
       	$result = $lsdb->query('DELETE FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"');
		
		if (!$result) {
   			ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		} else {
       		ls_redirect(BASE_URL.'index.php?p=success');
   		} 
  	break;
  	case 'sort':
  	
  		// Leads
  		$sqlw = '';
  		 
  		if (!ls_get_access("ochat_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) $sqlw = ' WHERE operatorid = "'.LS_USERID_RHINO.'"';
  		 
  		$total = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable.$sqlw);
  		$rowt = $total->fetch_assoc();
  		
  		//break total records into pages
  		$total_pages = ceil($rowt['totalAll']/20);
  	 	
  	 	// Call the template
  	 	$template = 'chats.php';
  	 		
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
		
		// Let's go on with the script
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $defaults = $_POST;
		    
		    if (!ls_get_access("ochat_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) ls_redirect(BASE_URL);
		    
		    if (isset($defaults['delete'])) {
		    
		    $lockuser = $defaults['ls_delete_chats'];
		
		        for ($i = 0; $i < count($lockuser); $i++) {
		            $locked = $lockuser[$i];
		            	
		            $sql = 'DELETE FROM '.$lstable.' WHERE id = "'.smartsql($locked).'"';
		            $result = $lsdb->query($sql);
		        	
		        }
		  
		 	if (!$result) {
				ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
			} else {
		        ls_redirect(BASE_URL.'index.php?p=success');
		    }
		    
		    }
		
		    
		 }
		
		// Chat history
		
		// Leads
		$sqlw = '';
		 
		if (!ls_get_access("ochat_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) $sqlw = ' WHERE fromid = "'.LS_USERID_RHINO.'" OR toid = "'.LS_USERID_RHINO.'"';
		 
		$total = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable.$sqlw);
		$rowt = $total->fetch_assoc();
		
		//break total records into pages
		$total_pages = ceil($rowt['totalAll']/20);
		
		// Call the template
		$template = 'chats.php';
}
?>