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
if (!LS_USERID_RHINO || !$lsuser->lsModuleaccess(LS_USERID_RHINO,LS_ACCESSGENERAL)) {
    ls_redirect(BASE_URL);
}

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'jrc_files';

// Now start with the plugin use a switch to access all pages
switch ($page1) {

	case 'delete':
		 
		// Check if the file can be deleted
		if (is_numeric($page2)) {
		
			$result = $lsdb->query('SELECT path FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"');
			$row = $result->fetch_assoc();
		        
			// Now check how many languages are installed and do the dirty work
			$sql = 'DELETE FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"';
			$result = $lsdb->query($sql);
			
			// Now let us delete the file
			$filedel = '../'.$row['path'];
			if (file_exists($filedel)) {
				unlink($filedel);
			}
		
		if (!$result) {
		    ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		} else {
			
			// Now let us delete the stuff cache file
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
		
		    if (empty($defaults['name'])) {
		        $errors['e'] = $tl['error']['e7'];
		    }
		    
		    if (count($errors) == 0) {
		
				$result = $lsdb->query('UPDATE '.$lstable.' SET 
				name = "'.smartsql($defaults['name']).'",
				description = "'.smartsql($defaults['description']).'"
				WHERE id = '.$page2);
		
				if (!$result) {
				    ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
				} else {
					
					// Now let us delete the stuff cache file
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
			$template = 'editfile.php';
		
		} else {
		   	ls_redirect(BASE_URL.'index.php?p=error&sp=not-exist');
		}
		
	break;
	default:
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_response'])) {
		    $defaults = $_POST;
		    
		    if (empty($defaults['name'])) {
		            $errors['e'] = $tl['error']['e7'];
		        }
		        
		        if (empty($_FILES['uploadedfile']['name'])) {
		            $errors['e1'] = $tl['error']['e13'];
		        }
		        
		        if (count($errors) == 0) {
		        
		        	$target_path = '../'.LS_FILES_DIRECTORY.'/'.$_FILES['uploadedfile']['name'];
		        	
		        	$db_path = LS_FILES_DIRECTORY.'/'.$_FILES['uploadedfile']['name'];
		        	
		        	if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		    
			    		$result = $lsdb->query('INSERT INTO '.$lstable.' SET 
			    		path = "'.$db_path.'",
			    		name = "'.smartsql($defaults['name']).'",
			    		description = "'.smartsql($defaults['description']).'"');
		    		
		    		}
		    
		    		if (!$result) {
		    		    ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		    		} else {
		    			
		    			// Now let us delete the stuff cache file
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
		 
		$FILES_ALL = ls_get_page_info($lstable, '', ''); 
		// Call the template
		$template = 'files.php';
}
?>