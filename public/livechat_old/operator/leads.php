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
if (!LS_USERID_RHINO || !LS_OPERATORACCESS) ls_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'jrc_sessions';
$lstable1 = DB_PREFIX.'jrc_transcript';

// Get the special lang var once for the time
define('LS_DAY', $tl['general']['g74']);
define('LS_HOUR', $tl['general']['g75']);
define('LS_MINUTE', $tl['general']['g76']);
define('LS_MULTITIME', $tl['general']['g77']);
define('LS_AGO', $tl['general']['g78']);

switch ($page1) {
	case 'delete':
		
		$result = $lsdb->query('SELECT convid FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"');
   		$row = $result->fetch_assoc();
   			
   		$lsdb->query('DELETE FROM '.$lstable1.' WHERE convid = "'.$row['convid'].'"');
   		
       	$sql = 'DELETE FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"';
		$result = $lsdb->query($sql);
		
		if (!$result) {
   			ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		} else {
       		ls_redirect(BASE_URL.'index.php?p=success');
   		} 
  	break;
  	case 'readleads':
  	
  		// Let's go on with the script
  		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email_conv'])) {
  		    $defaults = $_POST;
  		    
  		    // Errors in Array
  		    $errors = array();
  		    
  		    if ($defaults['email'] == '' || !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
  		        $errors['email'] = $tl['error']['e3'];
  		    }
  		    
  		    if (count($errors) > 0) {
  		    
  		    /* Outputtng the error messages */
  		    	if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
  		    	
  		    		header('Cache-Control: no-cache');
  		    		echo '{"status":0, "errors":'.json_encode($errors).'}';
  		    		exit;
  		    		
  		    	} else {
  		    	
  		    		$errors = $errors;
  		    	}
  		    	
  		    } else {
  		    
  		    	$result = $lsdb->query('SELECT * FROM '.$lstable1.' WHERE convid = "'.smartsql($page2).'" ORDER BY id ASC');
  		    	
  		    	$subject = $tl["general"]["g57"].' '.$defaults['cagent'].' '.$tl["general"]["g58"].' '.$defaults['cuser'];
  		    	
  		    	$mailchat = '<div style="margin:10px 0px 0px 0px;padding:10px;border:1px solid #A8B9CB;font-family: Verdana, sans-serif;font-size: 13px;
  		    	font-weight: 500;letter-spacing: normal;line-height: 1.5em;"><p>'.$subject.'</p><ul style="list-style:none;">';
  		    	
  		    	while ($row = $result->fetch_assoc()) {
  		    	        // collect each record into $_data
  		    	        if ($row['class'] == "notice") {
  		    	        	$mailchat .= '<li style="background-color:#d0e5f9;padding:5px;"><span style="font-size:10px;">'.$row['name'].' '.$tl['general']['g66'].':</span><br />'.$row['message'].'</li>';
  		    	        } else if ($row['class'] == "admin") {
  		    	            $mailchat .= '<li style="background-color:#effcff;padding:5px;"><span style="font-size:10px;">'.$row['time'].' - '.$row['name']." ".$tl['general']['g66'].':</span><br />'.$row['message'].'</li>';
  		    	        } else {
  		    	        	$mailchat .= '<li style="background-color:#f4fdf1;padding:5px;"><span style="font-size:10px;">'.$row['name'].' '.$tl['general']['g66'].':</span><br />'.$row['message'].'</li>';
  		    	        }
  		    	    }
  		    	    
  		    	$mailchat .= '</ul></div>';
  		    	
  		    	$mail = new PHPMailer(); // defaults to using php "mail()"
  		    	$mail->SetFrom(LS_EMAIL, $tl["general"]["g55"]);
  		    	$mail->AddAddress($defaults['email'], $defaults['email']);
  		    	$mail->Subject = $subject;
  		    	$mail->MsgHTML($mailchat);
  		    	
  		    if ($mail->Send()) {
  		    	
  		    	// Ajax Request
  		    	if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
  		    	
  		    		header('Cache-Control: no-cache');
  		    		echo json_encode(array('status' => 1, 'html' => $tl["general"]["g14"]));
  		    		exit;
  		    		
  		    	} else {
  		            ls_redirect($_SERVER['HTTP_REFERER']);
  		        }
  		    } 
  		    
  		}
  	}
  		
  		$result = $lsdb->query('SELECT * FROM '.$lstable1.' WHERE convid = "'.smartsql($page2).'" ORDER BY id ASC');
  		
  		while ($row = $result->fetch_assoc()) {
  		        // collect each record into $_data
  		        $lsdata[] = $row;
  		        
  		        if ($row['class'] == "user") { $CONV_USER = $row['name']; }
  		        if ($row['class'] == "admin") {
  		        	if ($row['name'] != "Admin") { $CONV_AGENT = $row['name']; }
  		        }
  		    }
  		
  		$CONVERSATION_LS = $lsdata;
  		
  	    // Call the template
  	    $template = 'readleads.php';
  	    
  	break;
	default:
		
		// Let's go on with the script
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $defaults = $_POST;
		    
		    if (isset($defaults['delete'])) {
		    
		    $lockuser = $defaults['ls_delete_leads'];
		
		        for ($i = 0; $i < count($lockuser); $i++) {
		            $locked = $lockuser[$i];
		            
		            $result = $lsdb->query('SELECT convid FROM '.$lstable.' WHERE id = "'.smartsql($locked).'"');
		            $row = $result->fetch_assoc();
		            		
		            $lsdb->query('DELETE FROM '.$lstable1.' WHERE convid = "'.$row['convid'].'"');
		            	
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
		
		$LEADS_ALL = ls_get_page_info($lstable, '');
		// Call the template
		$template = 'leads.php';
}
?>