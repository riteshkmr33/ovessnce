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

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('LS_OPERATOR_PREVENT_ACCESS')) die('You cannot access this file directly.');

// Check if the user has access to this file
if (!LS_USERID_RHINO || !LS_SUPEROPERATORACCESS) ls_redirect(BASE_URL);
if (!LS_OPERATORACCESS) ls_redirect(BASE_URL_ORIG);

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'jrc_loginlog';


$LS_LOGINLOG_ALL = ls_get_page_info($lstable, '', '');

// Let's go on with the script
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $defaults = $_POST;
    
    if (isset($defaults['delete'])) {
    
    $lockuser = $defaults['ls_delete_log'];

        for ($i = 0; $i < count($lockuser); $i++) {
            $locked = $lockuser[$i];
            
        	$sql = 'DELETE FROM '.$lstable.' WHERE id = '.$locked.'';
        	$result = $lsdb->query($sql);
        	
        }
  
 	if (!$result) {
		ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
	} else {
        ls_redirect(BASE_URL.'index.php?p=success');
    }
    
    }

    
 }
 
 switch ($page1) {
    case 'delete':
        $sql = 'DELETE FROM '.$lstable.' WHERE id = '.smartsql($page2).'';
		$result = $lsdb->query($sql);
		
	if (!$result) {
    	ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
	} else {
        ls_redirect(BASE_URL.'index.php?p=success');
    } 
   	break;
   	case 'truncate':
   	    $sql = 'TRUNCATE '.$lstable;
   		$result = $lsdb->query($sql);
   		
   	if (!$result) {
   		ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
   	} else {
   	    ls_redirect(BASE_URL.'index.php?p=success');
   	} 
   	break;
	default:
		// Call the template
		$template = 'logs.php';
	}
?>