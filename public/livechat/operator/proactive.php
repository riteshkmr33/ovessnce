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
if (!ls_get_access("proactive", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) ls_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'autoproactive';

// enter number
$lic_nr = false;

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
		
		    if (!filter_var($defaults['path'], FILTER_VALIDATE_URL)) {
		        $errors['e'] = $tl['error']['e21'];
		    }
		    
		    if (empty($defaults['message'])) {
		        $errors['e1'] = $tl['error']['e1'];
		    }
		    
		    if (count($errors) == 0) {
		
				$result = $lsdb->query('UPDATE '.$lstable.' SET 
				path = "'.smartsql($defaults['path']).'",
				showalert = "'.smartsql($defaults['showalert']).'",
				wayin = "'.smartsql($defaults['alertfadein']).'",
				wayout = "'.smartsql($defaults['alertfadeout']).'",
				timeonsite = "'.smartsql($defaults['onsite']).'",
				visitedsites = "'.smartsql($defaults['visited']).'",
				message = "'.smartsql($defaults['message']).'"
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
			$template = 'editproactive.php';
		
		} else {
		    
		   	ls_redirect(BASE_URL.'index.php?p=error&sp=not-exist');
		}
		
	break;
	default:
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_proactive'])) {
		    $defaults = $_POST;
		    
		    if (!filter_var($defaults['path'], FILTER_VALIDATE_URL)) {
		            $errors['e'] = $tl['error']['e21'];
		        }
		        
		        if (empty($defaults['message'])) {
		            $errors['e1'] = $tl['error']['e1'];
		        }
		        
		        if (count($errors) == 0) {
		    
		    		$result = $lsdb->query('INSERT INTO '.$lstable.' SET 
		    		path = "'.smartsql($defaults['path']).'",
		    		showalert = "'.smartsql($defaults['showalert']).'",
		    		wayin = "'.smartsql($defaults['alertfadein']).'",
		    		wayout = "'.smartsql($defaults['alertfadeout']).'",
		    		timeonsite = "'.smartsql($defaults['onsite']).'",
		    		visitedsites = "'.smartsql($defaults['visited']).'",
		    		message = "'.smartsql($defaults['message']).'",
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
		 
		 
		 $RESPONSES_ALL = ls_get_page_info($lstable, '', '');
		 
		 if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_lic'])) {
		 
		 	$result = $lsdb->query('UPDATE '.DB_PREFIX.'setting SET value = CASE varname
		 		WHEN "o_number" THEN "'.smartsql($_POST['license']).'"
		 	END
		 		WHERE varname IN ("o_number")');
		 	
		 	// Now let us delete the define cache file
		 	$cachedefinefile = '../'.LS_CACHE_DIRECTORY.'/define.php';
		 	if (file_exists($cachedefinefile)) {
		 		unlink($cachedefinefile);
		 	}
		 	ls_redirect(BASE_URL.'index.php?p=success');
		 }
		 
		 $pos = strpos(LS_O_NUMBER, 'O-');
		 
		 if ($pos === false) {
		 	$email_body = 'URL: '.BASE_URL.'<br />Email: '.LS_EMAIL.'<br />License: '.LS_O_NUMBER;
		 	
		     // Send the email to the customer
		     $mail = new PHPMailer(); // defaults to using php "mail()"
		     $body = str_ireplace("[\]", "", $email_body);
		     $mail->SetFrom(LS_EMAIL);
		     $mail->AddReplyTo(LS_EMAIL);
		     $mail->AddAddress('lic@livesupportrhino.com');
		     $mail->Subject = 'License - '.LS_TITLE;
		     $mail->AltBody = 'HTML Format';
		     $mail->MsgHTML($body);
		     $mail->Send();
		     
		     $lic_nr = true;
		 }
		 
		// Call the template
		$template = 'proactive.php';
}
?>