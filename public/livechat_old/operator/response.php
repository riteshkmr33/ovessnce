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

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'jrc_responses';

// Now start with the plugin use a switch to access all pages
switch ($page1) {

	case 'delete':
		 
		// Check if user exists and can be deleted
		if (is_numeric($page2)) {
		        
			// Now check how many languages are installed and do the dirty work
			$sql = 'DELETE FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"';
			$result = $lsdb->query($sql);
		
		if (!$result) {
		    ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		} else {
		    ls_redirect(BASE_URL.'index.php?p=success');
		}
		    
		} else {
		   	ls_redirect(BASE_URL.'index.php?p=error&sp=not-exist');
		}
		
	break;
	case 'edit':
	
		// Check if the user exists
		if (is_numeric($page2) && ls_row_exist($page2,$lstable)) {
		
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $defaults = $_POST;
		
		    if (empty($defaults['title'])) {
		        $errors['e'] = $tl['error']['e2'];
		    }
		    
		    if (empty($defaults['response'])) {
		        $errors['e1'] = $tl['error']['e1'];
		    }
		    
		    if (count($errors) == 0) {
		
				$result = $lsdb->query('UPDATE '.$lstable.' SET 
				title = "'.smartsql($defaults['title']).'",
				message = "'.smartsql($defaults['response']).'"
				WHERE id = '.$page2);
		
				if (!$result) {
				    ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
				} else {
					
					// Now let us delete the define cache file
					$cachestufffile = '../'.LS_CACHE_DIRECTORY.'/stuff.php';
					if (file_exists($cachestufffile)) {
						unlink($cachestufffile);
					}
					
				    ls_redirect(BASE_URL.'index.php?p=success');
				}
		
		// Output the errors
		} else {
		
		    $errors = $errors;
		}
		
		}
		
			$LS_FORM_DATA = ls_get_data($page2, $lstable);
			$template = 'editresponse.php';
		
		} else {
		   	ls_redirect(BASE_URL.'index.php?p=error&sp=not-exist');
		}
		
	break;
	default:
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_response'])) {
		    $defaults = $_POST;
		    
		    if (empty($defaults['title'])) {
		            $errors['e'] = $tl['error']['e2'];
		        }
		        
		        if (empty($defaults['response'])) {
		            $errors['e1'] = $tl['error']['e1'];
		        }
		        
		        if (count($errors) == 0) {
		    
		    		$result = $lsdb->query('INSERT INTO '.$lstable.' SET 
		    		title = "'.smartsql($defaults['title']).'",
		    		message = "'.smartsql($defaults['response']).'"');
		    
		    		if (!$result) {
		    		    ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		    		} else {
		    			
		    			// Now let us delete the define cache file
		    			$cachestufffile = '../'.LS_CACHE_DIRECTORY.'/stuff.php';
		    			if (file_exists($cachestufffile)) {
		    				unlink($cachestufffile);
		    			}
		    		    ls_redirect(BASE_URL.'index.php?p=success');
		    		}
		    
		    // Output the errors
		    } else {
		    
		        $errors = $errors;
		    }
		    
   
		 }
		 
		 
		 $RESPONSES_ALL = ls_get_page_info($lstable, '', '');
		 
		// Call the template
		$template = 'response.php';
}
?>