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
if (!ls_get_access("departments", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) ls_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'departments';

// Now start with the plugin use a switch to access all pages
switch ($page1) {

	case 'delete':
		 
		// Check if user exists and can be deleted
		if (is_numeric($page2) && $page2 != 1) {
		        
			// Now check how many languages are installed and do the dirty work
			$result = $lsdb->query('DELETE FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"');
		
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
		    
		    if ($defaults['email'] != '' && !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) { 
		    	$errors['e1'] = $tl['error']['e3'];
		    }
		    
		    if (count($errors) == 0) {
		
				$result = $lsdb->query('UPDATE '.$lstable.' SET 
				title = "'.smartsql($defaults['title']).'",
				description = "'.smartsql($defaults['description']).'",
				email = "'.smartsql($defaults['email']).'",
				time = NOW()
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
			$template = 'editdepartment.php';
		
		} else {
		   	ls_redirect(BASE_URL.'index.php?p=error&sp=not-exist');
		}
		
	break;
	case 'lock':
	
		// Check if user exists and can be deleted
		if (is_numeric($page2) && $page2 != 1) {
	
		$result = $lsdb->query('UPDATE '.$lstable.' SET active = IF (active = 1, 0, 1) WHERE id = "'.smartsql($page2).'"');
			
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
		
		} else {
		   	ls_redirect(BASE_URL.'index.php?p=error&sp=not-exist');
		}
	
	break;
	default:
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $defaults = $_POST;
		    
		    if (isset($_POST['insert_department'])) {
		    
		    if (empty($defaults['title'])) {
		    	$errors['e'] = $tl['error']['e2'];
		    }
		    
		    if ($defaults['email'] != '' && !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) { 
		    	$errors['e1'] = $tl['error']['e3'];
		    }
		        
		    if (count($errors) == 0) {
		    
		    	$result = $lsdb->query('INSERT INTO '.$lstable.' SET 
		    	title = "'.smartsql($defaults['title']).'",
		    	description = "'.smartsql($defaults['description']).'",
		    	email = "'.smartsql($defaults['email']).'",
		    	dorder = 2,
		    	time = NOW()');
		    
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
		    
		 if (isset($defaults['corder']) && isset($defaults['real_dep_id'])) {
		     
		 	$dorders = $defaults['corder'];
		    $depid = $defaults['real_dep_id'];
		    $realid = implode(',', $defaults['real_dep_id']);
		    $dep = array_combine($depid, $dorders);
		             
		    foreach ($dep as $key => $department) {
		    	$updatesql .= sprintf("WHEN %d THEN %d ", $key, $department);
		    }
		         	
		    $sql = 'UPDATE '.$lstable.' SET dorder = CASE id
		    '.$updatesql.'
		    END
		 	WHERE id IN ('.$realid.')';
		    $result = $lsdb->query($sql);
		             
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
		 	
		 }
		    
   
		 }
		 
		 $sql = 'SELECT * FROM '.$lstable.' ORDER BY dorder ASC';
		 $result = $lsdb->query($sql);
		 while ($row = $result->fetch_assoc()) {
		         // collect each record into $_data
		         $lsdata[] = $row;
		     }
		 
		 // Load all departments into a array
		 $DEPARTMENTS_ALL = $lsdata;
		 
		// Call the template
		$template = 'departments.php';
}
?>