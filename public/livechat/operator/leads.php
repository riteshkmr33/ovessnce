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
if (!ls_get_access("leads", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) ls_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'sessions';
$lstable1 = DB_PREFIX.'transcript';
$lstable2 = DB_PREFIX.'clientcontact';

// Get the special lang var once for the time
define('LS_DAY', $tl['general']['g74']);
define('LS_HOUR', $tl['general']['g75']);
define('LS_MINUTE', $tl['general']['g76']);
define('LS_MULTITIME', $tl['general']['g77']);
define('LS_AGO', $tl['general']['g78']);

switch ($page1) {
	case 'delete':
	
		if (!LS_USERID_RHINO || !LS_SUPERADMINACCESS) ls_redirect(BASE_URL);
		
		$result = $lsdb->query('SELECT id FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"');
   		$row = $result->fetch_assoc();
   			
   		$lsdb->query('DELETE FROM '.$lstable1.' WHERE convid = "'.$row['id'].'"');
   		
       	$sql = 'DELETE FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"';
		$result = $lsdb->query($sql);
		
		if (!$result) {
   			ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		} else {
       		ls_redirect(BASE_URL.'index.php?p=success');
   		} 
  	break;
  	case 'readleads':
  	
  		$resulti = $lsdb->query('SELECT name, email, phone, ip FROM '.$lstable.' WHERE id = "'.smartsql($page2).'" LIMIT 1');
  		$rowi = $resulti->fetch_assoc();
  	
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
  		    		die('{"status":0, "errors":'.json_encode($errors).'}');
  		    		
  		    	} else {
  		    		$errors = $errors;
  		    	}
  		    	
  		    } else {
  		    
  		    	$result = $lsdb->query('SELECT * FROM '.$lstable1.' WHERE convid = "'.smartsql($page2).'" ORDER BY id ASC');
  		    	
  		    	$subject = $tl["general"]["g57"].' '.$defaults['cagent'].' '.$tl["general"]["g58"].' '.$defaults['cuser'];
  		    	
  		    	$mailchat = '<div style="margin:10px 0px 0px 0px;padding:10px;border:1px solid #A8B9CB;font-family: Verdana, sans-serif;font-size: 13px;
  		    	font-weight: 500;letter-spacing: normal;line-height: 1.5em;"><p>'.$subject.'</p><p>'.$tl["user"]["u"].': '.$rowi['name'].' / '.$tl["user"]["u1"].': '.$rowi['email'].' / '.$tl["user"]["u14"].': '.$rowi['phone'].' / '.$tl["general"]["g11"].': '.$rowi['ip'].'</p><ul style="list-style:none;">';
  		    	
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
  		    	
  		    	if (LS_SMTP_MAIL) {
  		    	
  		    		$mail->IsSMTP(); // telling the class to use SMTP
  		    		$mail->Host = LS_SMTPHOST;
  		    		$mail->SMTPAuth = (LS_SMTP_AUTH ? true : false); // enable SMTP authentication
  		    		$mail->SMTPSecure = LS_SMTP_PREFIX; // sets the prefix to the server
  		    		$mail->SMTPKeepAlive = (LS_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
  		    		$mail->Port = LS_SMTPPORT; // set the SMTP port for the GMAIL server
  		    		$mail->Username = base64_decode(LS_SMTPUSERNAME); // SMTP account username
  		    		$mail->Password = base64_decode(LS_SMTPPASSWORD); // SMTP account password
  		    		$mail->SetFrom(LS_EMAIL, $tl["general"]["g55"]);
  		    		$mail->AddAddress($defaults['email'], $defaults['email']);
  		    		
  		    	} else {
  		    	
  		    		$mail->SetFrom(LS_EMAIL, $tl["general"]["g55"]);
  		    		$mail->AddAddress($defaults['email'], $defaults['email']);
  		    	
  		    	}
  		    	
  		    	$mail->Subject = $subject;
  		    	$mail->MsgHTML($mailchat);
  		    	
  		    if ($mail->Send()) {
  		    	
  		    	// Ajax Request
  		    	if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
  		    	
  		    		header('Cache-Control: no-cache');
  		    		die(json_encode(array('status' => 1, 'html' => $tl["general"]["g14"])));
  		    		
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
  		        
  		        if ($row['class'] == "admin") {
  		        	if ($row['name'] != "Admin") { $CONV_AGENT = $row['name']; }
  		        }
  		    }
  		
  		$CONVERSATION_LS = $lsdata;
  		
  	    // Call the template
  	    $template = 'readleads.php';
  	    
  	break;
  	case 'location':
  	
  		if (is_numeric($page2)) {
  	
  			$result = $lsdb->query('SELECT * FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"');
  			$row = $result->fetch_assoc();
  		
  		}
  		
  		// Call the template
  		$template = 'location.php';
  	
  	break;
  	case 'clientcontact':
  	
  		// Let's go on with the script
  		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_email'])) {
  		    $defaults = $_POST;
  		    
  		    // Errors in Array
  		    $errors = array();
  		    
  		    if (empty($defaults['subject']) || strlen(trim($defaults['subject'])) <= 2) {
  		        $errors['subject'] = $tl['error']['e17'];
  		    }
  		    
  		    if (empty($defaults['message']) || strlen(trim($defaults['message'])) <= 2) {
  		        $errors['message'] = $tl['error']['e1'];
  		    }
  		    
  		    if (count($errors) > 0) {
  		    
  		    /* Outputtng the error messages */
  		    	if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
  		    	
  		    		header('Cache-Control: no-cache');
  		    		die('{"status":0, "errors":'.json_encode($errors).'}');
  		    		
  		    	} else {
  		    		$errors = $errors;
  		    	}
  		    	
  		    } else {
  		    	
  		    	$mail = new PHPMailer(); // defaults to using php "mail()"
  		    	
  		    	if (LS_SMTP_MAIL) {
  		    	
  		    		$mail->IsSMTP(); // telling the class to use SMTP
  		    		$mail->Host = LS_SMTPHOST;
  		    		$mail->SMTPAuth = (LS_SMTP_AUTH ? true : false); // enable SMTP authentication
  		    		$mail->SMTPSecure = LS_SMTP_PREFIX; // sets the prefix to the server
  		    		$mail->SMTPKeepAlive = (LS_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
  		    		$mail->Port = LS_SMTPPORT; // set the SMTP port for the GMAIL server
  		    		$mail->Username = base64_decode(LS_SMTPUSERNAME); // SMTP account username
  		    		$mail->Password = base64_decode(LS_SMTPPASSWORD); // SMTP account password
  		    		$mail->SetFrom(LS_EMAIL, LS_TITLE);
  		    		$mail->AddAddress($defaults['email'], $defaults['name']);
  		    		
  		    	} else {
  		    	
  		    		$mail->SetFrom(LS_EMAIL, LS_TITLE);
  		    		$mail->AddAddress($defaults['email'], $defaults['name']);
  		    	
  		    	}
  		    	
  		    	$mail->Subject = trim($defaults['subject']);
  		    	$mail->MsgHTML(trim(nl2br($defaults['message'])));
  		    	
  		    if ($mail->Send()) {
  		    
  		    	// Insert the stuff into the database
  		    	$lsdb->query('INSERT INTO '.$lstable2.' SET 
  		    	sessionid = "'.smartsql($page2).'",
  		    	operatorid = '.LS_USERID_RHINO.',
  		    	operatorname = "'.$lsuser->getVar("username").'",
  		    	subject = "'.smartsql(trim($defaults['subject'])).'",
  		    	message = "'.smartsql(trim($defaults['message'])).'",
  		    	sent = NOW()');
  		    	
  		    	// Ajax Request
  		    	if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
  		    	
  		    		header('Cache-Control: no-cache');
  		    		die(json_encode(array('status' => 1, 'html' => $tl["general"]["g14"])));
  		    		
  		    	} else {
  		    	
  		            ls_redirect($_SERVER['HTTP_REFERER']);
  		        
  		        }
  		    } 
  		    
  		}
  		}
  		
  		$result = $lsdb->query('SELECT id, operatorname, subject, message, sent FROM '.$lstable2.' WHERE sessionid = "'.smartsql($page2).'"');
  		while ($row = $result->fetch_assoc()) {
  			
  			$allmessages[] = array('id' => $row['id'], 'operator' => $row['operatorname'], 'subject' => $row['subject'], 'message' => $row['message'], 'sent' => $row['sent']);
  		}
  		
  		// Ouput all leads, well with paginate of course	
  		$MESSAGES_ALL = $allmessages;
  		    
  		$resulti = $lsdb->query('SELECT name, email FROM '.$lstable.' WHERE id = "'.smartsql($page2).'" LIMIT 1');
  		$rowi = $resulti->fetch_assoc();
  		
  	    // Call the template
  	    $template = 'clientcontact.php';
  	    
  	break;
  	case 'operator':
  	
  		if (!ls_get_access("leads_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS) && LS_USERID_RHINO != $page2) ls_redirect(BASE_URL);
  	
  	 	$total = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable.' WHERE operatorid = '.$page2);
  	 	$rowt = $total->fetch_assoc();
  	 	 
  	 	if ($rowt['totalAll'] != 0) {
  	 	
  	 		// Paginator
  	 		$leads = new Paginator;
  	 		$leads->items_total = $rowt['totalAll'] ;
  	 		$leads->mid_range = 10;
  	 		$leads->items_per_page = 20;
  	 		$leads->jak_get_page = $page4;
  	 		$leads->jak_where = 'index.php?p=leads&sp=operator&ssp='.$page2.'&sssp='.$page3;
  	 		$leads->paginate();
  	 		$JAK_PAGINATE = $leads->display_pages();
  	 	}
  	 	
  	 	$result = $lsdb->query('SELECT t1.id, t1.name, t1.email, t1.department, t1.operatorid, t1.initiated, t1.ip, t1.notes, t1.countrycode, t1.country, t1.city, t2.username, t3.title FROM '.$lstable.' AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON (t1.operatorid = t2.id) LEFT JOIN '.DB_PREFIX.'departments AS t3 ON (t1.department = t3.id) WHERE operatorid = '.$page2.' '.$leads->limit);
  	 	
  	 	while ($row = $result->fetch_assoc()) {
  	 		$allLeads[] = $row;
  	 	}
  	 	
  	 	// Ouput all leads, well with paginate of course	
  	 	$LEADS_ALL = $allLeads;
  	 	
  	 	// Call the template
  	 	$template = 'leads.php';
  	 		
  	break;
  	case 'departement':
  	
  		if (!ls_get_access("leads_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) $sqlw = ' AND operatorid = "'.LS_USERID_RHINO.'"';
  	
  	 	$total = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable.' WHERE department = '.$page2.$sqlw);
  	 	$rowt = $total->fetch_assoc();
  	 	 
  	 	if ($rowt['totalAll'] != 0) {
  	 	
  	 		// Paginator
  	 		$leads = new Paginator;
  	 		$leads->items_total = $rowt['totalAll'] ;
  	 		$leads->mid_range = 10;
  	 		$leads->items_per_page = 20;
  	 		$leads->jak_get_page = $page4;
  	 		$leads->jak_where = 'index.php?p=leads&sp=sort&ssp='.$page2.'&sssp='.$page3;
  	 		$leads->paginate();
  	 		$JAK_PAGINATE = $leads->display_pages();
  	 	}
  	 	
  	 	$result = $lsdb->query('SELECT t1.id, t1.name, t1.email, t1.department, t1.operatorid, t1.initiated, t1.ip, t1.notes, t1.countrycode, t1.country, t1.city, t2.username, t3.title FROM '.$lstable.' AS t1 LEFT JOIN '.DB_PREFIX.'user AS t2 ON (t1.operatorid = t2.id) LEFT JOIN '.DB_PREFIX.'departments AS t3 ON (t1.department = t3.id) WHERE department = '.$page2.$sqlw.' '.$leads->limit);
  	 	
  	 	while ($row = $result->fetch_assoc()) {
  	 		$allLeads[] = $row;
  	 	}
  	 	
  	 	// Ouput all leads, well with paginate of course	
  	 	$LEADS_ALL = $allLeads;
  	 	
  	 	// Call the template
  	 	$template = 'leads.php';
  	 		
  	break;
  	case 'sort':
  	
  		// Leads
  		$sqlw = '';
  		$rowts['total_support'] = 0;
  		$rowtc['totalAll'] = 0;
  		$bounce_percentage = 0;
  		 
  		if (!ls_get_access("leads_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) $sqlw = ' WHERE operatorid = "'.LS_USERID_RHINO.'"';
  		 
  		$total = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable.$sqlw);
  		$rowt = $total->fetch_assoc();
  		
  		//break total records into pages
  		$total_pages = ceil($rowt['totalAll']/20);
  		
  		$sqlw1 = $sqlw ? $sqlw.' AND fcontact = 1' : ' WHERE fcontact = 1';
  		
  		$totalc = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable.$sqlw1.'');
  		$rowtc = $totalc->fetch_assoc();
  		
  		// Get percentage 
  		if ($rowtc['totalAll']) $bounce_percentage = round($rowtc['totalAll'] / $rowt['totalAll'] * 100, 2, PHP_ROUND_HALF_UP);
  		
  		$sqlw2 = $sqlw ? $sqlw.' AND ended != 0 AND initiated != 0' : ' WHERE ended != 0 AND initiated != 0';
  		
  		$totals = $lsdb->query('SELECT SUM(ended - initiated) AS total_support FROM '.$lstable.$sqlw2.'');
  		$rowts = $totals->fetch_assoc();
  	 	
  	 	// Call the template
  	 	$template = 'leads.php';
  	 		
  	break;
  	case 'truncate':
  	
  		if (!LS_USERID_RHINO || !LS_SUPERADMINACCESS) ls_redirect(BASE_URL);
  		
  		$lsdb->query('TRUNCATE '.$lstable1);
  	    $result = $lsdb->query('TRUNCATE '.$lstable);
  	    
  		
	  	if (!$result) {
	  		ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
	  	} else {
	  	    ls_redirect(BASE_URL.'index.php?p=success');
	  	}
	  	 
  	break;
  	case 'history':
  	
  	if (is_numeric($page2)) {
  	
  		$result = $lsdb->query('SELECT name, email, ip FROM '.$lstable.' WHERE id = "'.smartsql($page2).'"');
  		
  		if ($lsdb->affected_rows > 0) {
  		
  			$row = $result->fetch_assoc();
  	
  		if (!ls_get_access("leads_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) $sqlw = ' AND operatorid = "'.JAK_USERID_CD.'"';
  		
  		$result = $lsdb->query('SELECT id, name, initiated FROM '.$lstable.' WHERE id != "'.smartsql($page2).'" AND ((ip = "'.$row["ip"].'" AND name = "'.$row["name"].'") OR (ip = "'.$row["ip"].'" AND email = "'.$row["email"].'"))'.$sqlw.' ORDER BY initiated DESC LIMIT 5');
  		
  		while ($row = $result->fetch_assoc()) {
  		
  			$lsdata = '';
  		
  			$result1 = $lsdb->query('SELECT * FROM '.$lstable1.' WHERE convid = "'.smartsql($row['id']).'" ORDER BY id ASC');
  			
  			while ($row1 = $result1->fetch_assoc()) {
  			        // collect each record into $_data
  			        $lsdata[] = $row1;
  			    }
  			
  			$allLeads[] = array('id' => $row['id'], 'name' => $row['name'], 'initiated' => $row['initiated'], 'chat' => $lsdata);
  		}
  		
  		// Ouput all leads, well with paginate of course	
  		$LEADS_ALL = $allLeads;
  		
  		}
  		
  	}
  	
  		
  		// Call the template
  		$template = 'historyleads.php';
  	
  	break;
	default:
	
		$searchstatus = false;
		
		// Let's go on with the script
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $defaults = $_POST;
		    
		    if (isset($defaults['delete'])) {
		    
		    if (!LS_USERID_RHINO || !LS_SUPERADMINACCESS) ls_redirect(BASE_URL);
		    
		    $lockuser = $defaults['ls_delete_leads'];
		
		        for ($i = 0; $i < count($lockuser); $i++) {
		            $locked = $lockuser[$i];
		            
		            $result = $lsdb->query('SELECT id FROM '.$lstable.' WHERE id = "'.smartsql($locked).'"');
		            $row = $result->fetch_assoc();
		            		
		            $lsdb->query('DELETE FROM '.$lstable1.' WHERE convid = "'.$row['id'].'"');
		            	
		            $sql = 'DELETE FROM '.$lstable.' WHERE id = "'.smartsql($locked).'"';
		            $result = $lsdb->query($sql);
		        	
		        }
		  
		 	if (!$result) {
				ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
			} else {
		        ls_redirect(BASE_URL.'index.php?p=success');
		    }
		    
		    }
		    
		 	if (isset($defaults['search'])) {
		 
		     if (strlen($defaults['jakSH']) < 3) {
		         $errors['e'] = $tl['search']['s'];
		     }
		 
		     if (count($errors) > 0) {
		         $errors = $errors;
		     } else {
		         $searchword = smartsql(strip_tags($defaults['jakSH']));
		         
		         if (!ls_get_access("leads_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) $sqlw = 't1.operatorid = "'.LS_USERID_RHINO.'" AND ';
		         
		         // The Query
		         $result = $lsdb->query('SELECT t1.id, t1.operatorname as username, t1.name, t1.email, t1.department, t1.operatorid, t1.ip, t1.initiated, t1.fcontact, t1.notes, t1.countrycode, t1.country, t1.city, t3.title FROM '.$lstable.' AS t1 LEFT JOIN '.$lstable1.' AS t2 ON (t1.id = t2.convid) LEFT JOIN '.DB_PREFIX.'departments AS t3 ON (t1.department = t3.id) WHERE '.$sqlw.'(t1.name like "%'.$searchword.'%" OR t1.email like "%'.$searchword.'%" OR t2.message like "%'.$searchword.'%") GROUP BY t1.id ORDER BY t1.initiated DESC LIMIT 10');
		         
		         while ($row = $result->fetch_assoc()) {
		         	$allLeads[] = $row;
		         }
		         
		         // Ouput all leads, well with paginate of course
		         if (is_array($allLeads)) {
		         	$LEADS_ALL = $allLeads;
		         	$searchstatus = true;
		         } else {
		         	$errors['e1'] = $tl['search']['s2'];
		         	$errors = $errors;
		         }
		     }
		     
		     }
		
		    
		 }
		 
		if (!$searchstatus) {
		 
			// Leads
			$sqlw = '';
			$rowts['total_support'] = 0;
			$rowtc['totalAll'] = 0;
			$bounce_percentage = 0;
			 
			if (!ls_get_access("leads_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) $sqlw = ' WHERE operatorid = "'.LS_USERID_RHINO.'"';
			 
			$total = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable.$sqlw);
			$rowt = $total->fetch_assoc();
			
			//break total records into pages
			$total_pages = ceil($rowt['totalAll']/20);
			
			$sqlw1 = $sqlw ? $sqlw.' AND fcontact = 1' : ' WHERE fcontact = 1';
			
			$totalc = $lsdb->query('SELECT COUNT(*) as totalAll FROM '.$lstable.$sqlw1.'');
			$rowtc = $totalc->fetch_assoc();
			
			// Get percentage 
			if ($rowtc['totalAll']) $bounce_percentage = round($rowtc['totalAll'] / $rowt['totalAll'] * 100, 2, PHP_ROUND_HALF_UP);
			
			$sqlw2 = $sqlw ? $sqlw.' AND ended != 0 AND initiated != 0' : ' WHERE ended != 0 AND initiated != 0';
			
			$totals = $lsdb->query('SELECT SUM(ended - initiated) AS total_support FROM '.$lstable.$sqlw2.'');
			$rowts = $totals->fetch_assoc();
		
		}
		
		// Call the template
		$template = 'leads.php';
}
?>