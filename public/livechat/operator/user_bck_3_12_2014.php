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

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'user';
$lstable1 = DB_PREFIX.'user_stats';
$lstable2 = DB_PREFIX.'departments';
$lsfield = 'username';

// Now start with the plugin use a switch to access all pages
switch ($page1) {

	// Create new user
	case 'new':
		
		// No special access, so what you doing here?
		if (!$LS_SPECIALACCESS) ls_redirect(BASE_URL);
		
		$result = $lsdb->query('SELECT id, title FROM '.$lstable2.' ORDER BY dorder ASC');
		while ($row = $result->fetch_assoc()) {
			// collect each record into $_data
		    $LS_DEPARTMENTS[] = $row;
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $defaults = $_POST;
		
		    if (empty($defaults['ls_name'])) {
		        $errors['e1'] = $tl['error']['e7'];
		    }
		    
		    if ($defaults['ls_email'] == '' || !filter_var($defaults['ls_email'], FILTER_VALIDATE_EMAIL)) {
		        $errors['e2'] = $tl['error']['e3'];
		    }
		    
		    if (!preg_match('/^([a-zA-Z0-9\-_])+$/', $defaults['ls_username'])) {
		    	$errors['e3'] = $tl['error']['e8'];
		    }
		    
		    if (ls_field_not_exist(strtolower($defaults['ls_username']),$lstable,$lsfield)) {
		        $errors['e4'] = $tl['error']['e9'];
		    }
		     
		    if ($defaults['ls_password'] != $defaults['ls_confirm_password']) {
		    	$errors['e5'] = $tl['error']['e10'];
		    } elseif (strlen($defaults['ls_password']) <= '5') {
		    	$errors['e6'] = $tl['error']['e11'];
		    } else {
		    	$updatepass = '1';
		    }
		    
		    if (count($errors) == 0) {
		    
		    if (!isset($defaults['jak_depid'])) {
		    	$depa = 0;
		    } else {
		    	$depa = join(',', $defaults['jak_depid']);
		    }
		    
		    if (!isset($defaults['ls_days'])) {
		    	$tw_days = '';
		    } else {
		    	$tw_days = join(',', $defaults['ls_days']);
		    }
		    
		    if ($LS_SPECIALACCESS) {
		    
			    if (!isset($defaults['ls_roles'])) {
			    	$tw_roles = '';
			    } else {
			    	$tw_roles = join(',', $defaults['ls_roles']);
			    }
			    
			    $insert .= 'permissions = "'.$tw_roles.'",';
			    
			}
		    
		    if ($updatepass) { $insert .= 'password = "'.hash_hmac('sha256', $defaults['ls_password'], DB_PASS_HASH).'",'; }
		
			$result = $lsdb->query('INSERT INTO '.$lstable.' SET 
			departments = "'.$depa.'",
			username = "'.smartsql($defaults['ls_username']).'",
			name = "'.smartsql($defaults['ls_name']).'",
			email = "'.smartsql($defaults['ls_email']).'",
			responses = '.$defaults['ls_responses'].',
			tw_days = "'.$tw_days.'",
			tw_time_from = "'.$defaults['jak_timefrom'].':'.$defaults['jak_timefromm'].':00",
			tw_time_to = "'.$defaults['jak_timeto'].':'.$defaults['jak_timetom'].':00",
			phonenumber = "'.$defaults['ls_phone'].'",
			dnotify = "'.$defaults['ls_dnotify'].'",
			files = '.$defaults['ls_files'].',
			operatorchat = '.$defaults['ls_chat'].',
			operatorlist = '.$defaults['ls_chatlist'].',
			sound = '.$defaults['ls_sound'].',
			ringing = '.$defaults['ls_ringing'].',
			emailnot = '.$defaults['ls_emailnot'].',
			language = "'.$defaults['ls_lang'].'",
			invitationmsg = "'.$defaults['ls_inv'].'",
			access = '.$defaults['ls_access'].',
			'.$insert.'
			time = NOW()');
			
			$row1['id'] = $lsdb->ls_last_id();
		
		if (!$result) {
		    	ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
			} else {
				
				$newuserpath = '../'.LS_FILES_DIRECTORY.'/'.$row1['id'];
				
				if (!is_dir($newuserpath)) {
				                mkdir($newuserpath, 0777);
				                copy('../'.LS_FILES_DIRECTORY."/index.html", $newuserpath."/index.html");
				            }
		        ls_redirect(BASE_URL.'index.php?p=success');
		    }
		 } else {
		    
		   	$errors['e'] = $tl['error']['e'];
		    $errors = $errors;
		 }
		}
		
		// Call the settings function
		$lang_files = ls_get_lang_files(false);
		
		// Call the template
		$template = 'newuser.php';
		
	break;
	case 'stats':
	
		// Let's go on with the script
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email_feedback'])) {
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
			    
			    	$result = $lsdb->query('SELECT * FROM '.$lstable1.' WHERE userid = "'.smartsql($page2).'" ORDER BY id ASC');
			    	
			    	$result1 = $lsdb->query('SELECT SUM(vote) AS total_vote, SUM(support_time) AS total_support FROM '.$lstable1.' WHERE userid = "'.$page2.'"');
			    	$row1 = $result1->fetch_assoc();
			    	
			    	$subject = $tl["general"]["g81"].' '.$page3;
			    	
			    	$mailchat = '<div style="margin:10px 0px 0px 0px;padding:10px;border:1px solid #A8B9CB;font-family: Verdana, sans-serif;font-size: 13px;
			    	font-weight: 500;letter-spacing: normal;line-height: 1.5em;"><h2>'.$subject.'</h2><ul style="list-style:none;">';
			    	
			    	while ($row = $result->fetch_assoc()) {
			    	        // collect each record into $_data
			    	        	$mailchat .= '<li style="border-bottom:1px solid #333"><span style="font-size:11px">'.$row['time'].' - '.$tl['general']['g86'].':</span><br /><span style="color:#c92e2e">'.$tl['general']['g85'].': </span>'.$row['vote'].'/5<br />'.$tl['general']['g54'].': '.$row['name'].'<br />'.$tl['stat']['s12'].': '.$row['comment'].'<br />'.$tl['login']['l5'].': '.$row['email'].'<br />'.$tl['general']['g87'].': '.gmdate('H:i:s', $row['support_time']).'</li>';
			    	        	
			    	        	$count++;
			    	    }
			    	    
			    	$mailchat .= '</ul>';
			    	
			    	$mailchat .= '<h2>'.$tl["general"]["g89"].'</h2>
			    	<p><strong>'.$tl["general"]["g90"].':</strong> '.gmdate('H:i:s', $row1['total_support']).'<br /><strong>'.$tl["general"]["g91"].':</strong> '.round(($row1['total_vote'] / $count), 2).'/5</p></div>';
			    	
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
			    		$mail->SetFrom(LS_EMAIL, $tl["general"]["g92"]);
			    		$mail->AddAddress($defaults['email'], $defaults['email']);
			    		
			    	} else {
			    	
			    		$mail->SetFrom(LS_EMAIL, $tl["general"]["g92"]);
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
	
		// Check if the user exists
		if (is_numeric($page2) && ls_row_exist($page2, $lstable)) {
	
			$sql = 'SELECT * FROM '.$lstable1.' WHERE userid = "'.$page2.'" ORDER BY id DESC';
			$result = $lsdb->query($sql);
			if ($lsdb->affected_rows > 0) {
			while ($row = $result->fetch_assoc()) {
			        // collect each record into $_data
			        $lsdata[] = $row;
			    }
			    
			    $result1 = $lsdb->query('SELECT SUM(vote) AS total_vote, SUM(support_time) AS total_support FROM '.$lstable1.' WHERE userid = "'.$page2.'"');
			    $row1 = $result1->fetch_assoc();
			}
			
			$USER_FEEDBACK = $lsdata;
			$USER_VOTES = $row1['total_vote'];
			$USER_SUPPORTT = $row1['total_support'];
		
		}
	
		// Call the template
		$template = 'userstats.php';
		 		
	break;
	case 'lock':
	
		if (ls_user_exist_deletable($page2)) {
	
			$sql = 'UPDATE '.$lstable.' SET access = IF (access = 1, 0, 1) WHERE id = '.smartsql($page2).'';
			$result = $lsdb->query($sql);
		    	
		if (!$result) {
			ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		} else {
		    ls_redirect(BASE_URL.'index.php?p=success');
		}
		
		} else {
		   	ls_redirect(BASE_URL.'index.php?p=error&sp=user-no-delete');
		}
		 		
	break;
	case 'delete':
		 
		// Check if user exists and can be deleted
		if (ls_user_exist_deletable($page2)) {
		        
			// Now check how many languages are installed and do the dirty work
			$sql = 'DELETE FROM '.$lstable.' WHERE id = '.smartsql($page2).'';
			$result = $lsdb->query($sql);
		
			if (!$result) {
		    	ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
			} else {
				
				// Delete Avatar and folder
				$targetPath = '../'.LS_FILES_DIRECTORY.'/'.$page2.'/';
				$removedouble =  str_replace("//","/",$targetPath);
				foreach(glob($removedouble.'*.*') as $jak_unlink) {
				
					@unlink($jak_unlink);
				
					@unlink($targetPath);
				
				}
				
		        ls_redirect(BASE_URL.'index.php?p=success');
		    }
		    
		} else {
		   	ls_redirect(BASE_URL.'index.php?p=error&sp=user-no-delete');
		}
		
	break;
	case 'edit':
		
		// No special access and not your userid, what you up to?
		if (!$LS_SPECIALACCESS && $page2 != LS_USERID_RHINO) ls_redirect(BASE_URL);
	
		// Check if the user exists
		if (is_numeric($page2) && ls_row_exist($page2,$lstable)) {
		
			$result = $lsdb->query('SELECT id, title FROM '.$lstable2.' ORDER BY dorder ASC');
			while ($row = $result->fetch_assoc()) {
				// collect each record into $_data
			    $LS_DEPARTMENTS[] = $row;
			}
		
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $defaults = $_POST;
		
		    if (empty($defaults['ls_name'])) {
		        $errors['e1'] = $tl['error']['e7'];
		    }
		    
		    if ($defaults['ls_email'] == '' || !filter_var($defaults['ls_email'], FILTER_VALIDATE_EMAIL)) {
		        $errors['e2'] = $tl['error']['e3'];
		    }
		    
		    if (!preg_match('/^([a-zA-Z0-9\-_])+$/', $defaults['ls_username'])) {
		    	$errors['e3'] = $tl['error']['e8'];
		    }
		    
		    if (ls_field_not_exist_id($defaults['ls_username'],$page2,$lstable,$lsfield)) {
		        $errors['e4'] = $tl['error']['e9'];
		    }
		    
		    if (!empty($defaults['ls_password']) || !empty($defaults['ls_confirm_password'])) {    
		    if ($defaults['ls_password'] != $defaults['ls_confirm_password']) {
		    	$errors['e5'] = $tl['error']['e10'];
		    } elseif (strlen($defaults['ls_password']) <= '5') {
		    	$errors['e6'] = $tl['error']['e11'];
		    } else {
		    	$updatepass = '1';
		    }
		    }
		    
		    // Delete Avatar if yes
		    if (!empty($defaults['ls_delete_avatar'])) {
		    $avatarpi = '../'.LS_FILES_DIRECTORY.'/index.html';
		    $avatarpid =  str_replace("//","/",$avatarpi);
		    $targetPath = '../'.LS_FILES_DIRECTORY.'/'.$page2.'/';
		    $removedouble =  str_replace("//","/",$targetPath);
		    foreach(glob($removedouble.'*.*') as $ls_unlink){
		        unlink($ls_unlink);
		        copy($avatarpid, $targetPath . "/index.html");
		    }
		    
		    $lsdb->query('UPDATE '.$lstable.' SET picture = "/standard.png" WHERE id = '.smartsql($page2).'');
		    
		    }
		    
		    if (!empty($_FILES['uploadpp']['name'])) {
		    
		    	if ($_FILES['uploadpp']['name'] != '') {
		    	
		    	$filename = $_FILES['uploadpp']['name']; // original filename
		    	// Fix explode when upload in 1.2
		    	$tmpf = explode(".", $filename);
		    	$ls_xtension = end($tmpf);
		    	
		    	if ($ls_xtension == "jpg" || $ls_xtension == "jpeg" || $ls_xtension == "png" || $ls_xtension == "gif") {
		    	
		    	if ($_FILES['uploadpp']['size'] <= 500000) {
		    	
		    	list($width, $height, $type, $attr) = getimagesize($_FILES['uploadpp']['tmp_name']);
		    	$mime = image_type_to_mime_type($type);
		    	
		    	if (($mime == "image/jpeg") || ($mime == "image/pjpeg") || ($mime == "image/png") || ($mime == "image/gif")) {
		    	
		    	// first get the target path
		    	$targetPathd = '../'.LS_FILES_DIRECTORY.'/'.$page2.'/';
		    	$targetPath =  str_replace("//","/",$targetPathd);
		    	// Create the target path
		    	if (!is_dir($targetPath)) {
		    	
		    	                mkdir($targetPath, 0777);
		    	                copy('../'.LS_FILES_DIRECTORY."/index.html", $targetPath . "/index.html");
		    	
		    	            }
		    	// if old avatars exist delete it
		    	foreach(glob($targetPath.'*.*') as $ls_unlink){
		    	    unlink($ls_unlink);
		    	    copy('../'.LS_FILES_DIRECTORY."/index.html", $targetPath . "/index.html");
		    	}
		    	
		    	            	$tempFile = $_FILES['uploadpp']['tmp_name'];
		    	            	$origName = substr($_FILES['uploadpp']['name'], 0, -4);
		    	            	$name_space = strtolower($_FILES['uploadpp']['name']);
		    	                $middle_name = str_replace(" ", "_", $name_space);
		    	                $middle_name = str_replace(".jpeg", ".jpg", $name_space);
		    	                $glnrrand = rand(10, 99);
		    	                $bigPhoto = str_replace(".", "_" . $glnrrand . ".", $middle_name);
		    	                $smallPhoto = str_replace(".", "_t.", $bigPhoto);
		    	            	    
		    	            	$targetFile =  str_replace('//','/',$targetPath) . $bigPhoto;
		    	            	$origPath = '/'.$page2.'/';
		    	            	$dbSmall = $origPath.$smallPhoto;
		    	            	$dbBig = $origPath.$bigPhoto;
		    	            
		    	            require_once '../include/functions_thumb.php';
		    	            // Move file and create thumb     
		    	            move_uploaded_file($tempFile,$targetFile);
		    	                 
		    	            create_thumbnail($targetPath, $targetFile, $smallPhoto, LS_USERAVATWIDTH, LS_USERAVATHEIGHT, 80);
		    	                 	
		    	            // SQL insert
		    	            $lsdb->query('UPDATE '.$lstable.' SET picture = "'.$dbSmall.'" WHERE id = "'.$page2.'" LIMIT 1');
		    	                 		
		    	                } else {
		    	                	$errors['e'] = $tl['error']['e24'].'<br />';
		    	                	$errors = $errors;
		    	                }
		    	                
		    	                } else {
		    	                	$errors['e'] = $tl['error']['e24'].'<br />';
		    	                	$errors = $errors;
		    	                }
		    	                
		    	                } else {
		    	                	$errors['e'] = $tl['error']['e24'].'<br />';
		    	                	$errors = $errors;
		    	                }
		    	                
		    	                } else {
		    	                	$errors['e'] = $tl['error']['e24'].'<br />';
		    	                	$errors = $errors;
		    	                }
		    	
		    
		    }
		    
		    if (count($errors) == 0) {
		    
		    if (!isset($defaults['ls_access'])) $defaults['ls_access'] = '1';
		    
		    if ($updatepass) $insert .= 'password = "'.hash_hmac('sha256', $defaults['ls_password'], DB_PASS_HASH).'",';
		    
		    // We cant deny access for superadmin
		    $useridarray = explode(',', LS_SUPERADMIN);
		    
		    if (!in_array($page2, $useridarray)) {
		    	
		    	$insert .= 'access = "'.$defaults['ls_access'].'",';
		    }
		    
		    if (!isset($defaults['jak_depid'])) {
		    	$depa = 0;
		    } else {
		    	$depa = join(',', $defaults['jak_depid']);
		    }
		    
		    if (!isset($defaults['ls_days'])) {
		    	$tw_days = '';
		    } else {
		    	$tw_days = join(',', $defaults['ls_days']);
		    }
		    
		    if ($LS_SPECIALACCESS) {
		    
		        if (!isset($defaults['ls_roles'])) {
		        	$tw_roles = '';
		        } else {
		        	$tw_roles = join(',', $defaults['ls_roles']);
		        }
		        
		        $insert .= 'permissions = "'.$tw_roles.'",';
		        
		    }
		    
		    if ($LS_SPECIALACCESS) {
		
				$result = $lsdb->query('UPDATE '.$lstable.' SET 
				departments = "'.$depa.'",
				username = "'.smartsql(trim($defaults['ls_username'])).'",
				name = "'.smartsql(trim($defaults['ls_name'])).'",
				tw_days = "'.$tw_days.'",
				tw_time_from = "'.$defaults['jak_timefrom'].':'.$defaults['jak_timefromm'].':00",
				tw_time_to = "'.$defaults['jak_timeto'].':'.$defaults['jak_timetom'].':00",
				phonenumber = "'.$defaults['ls_phone'].'",
				dnotify = "'.$defaults['ls_dnotify'].'",
				responses = '.$defaults['ls_responses'].',
				files = '.$defaults['ls_files'].',
				operatorchat = '.$defaults['ls_chat'].',
				operatorlist = '.$defaults['ls_chatlist'].',
				sound = '.$defaults['ls_sound'].',
				ringing = '.$defaults['ls_ringing'].',
				emailnot = '.$defaults['ls_emailnot'].',
				language = "'.$defaults['ls_lang'].'",
				invitationmsg = "'.$defaults['ls_inv'].'",
				'.$insert.'
				email = "'.filter_var($defaults['ls_email'], FILTER_SANITIZE_EMAIL).'"
				WHERE id = '.$page2);
			
			} else {
			
				$result = $lsdb->query('UPDATE '.$lstable.' SET 
				username = "'.smartsql(trim($defaults['ls_username'])).'",
				name = "'.smartsql(trim($defaults['ls_name'])).'",
				language = "'.$defaults['ls_lang'].'",
				invitationmsg = "'.$defaults['ls_inv'].'",
				tw_days = "'.$tw_days.'",
				tw_time_from = "'.$defaults['jak_timefrom'].':'.$defaults['jak_timefromm'].':00",
				tw_time_to = "'.$defaults['jak_timeto'].':'.$defaults['jak_timetom'].':00",
				phonenumber = "'.$defaults['ls_phone'].'",
				dnotify = "'.$defaults['ls_dnotify'].'",
				sound = '.$defaults['ls_sound'].',
				ringing = '.$defaults['ls_ringing'].',
				'.$insert.'
				email = "'.filter_var($defaults['ls_email'], FILTER_SANITIZE_EMAIL).'"
				WHERE id = '.$page2);
			
			}
		
		if (!$result) {
		    ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
		} else {
		    ls_redirect(BASE_URL.'index.php?p=success');
		}
		
		// Output the errors
		} else {
		    
		   	$errors['e'] = $tl['error']['e'];
		    $errors = $errors;
		}
		}
		
			// Call the settings function
			$lang_files = ls_get_lang_files(false);
		
			$LS_FORM_DATA = ls_get_data($page2,$lstable);
			$template = 'edituser.php';
		
		} else {
		   	ls_redirect(BASE_URL.'index.php?p=error&sp=not-exist');
		}
		
		break;
		default:
		
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ls_delete_user'])) {
			    $defaults = $_POST;
			    
			    if (isset($defaults['lock'])) {
			    
			    $lockuser = $defaults['ls_delete_user'];
			    $useridarray = explode(',', LS_SUPERADMIN);
			
			        for ($i = 0; $i < count($lockuser); $i++) {
			            $locked = $lockuser[$i];
			            
			            if (!in_array($locked, $useridarray)) {
				        	$sql = 'UPDATE '.$lstable.' SET access = IF (access = 1, 0, 1) WHERE id = '.$locked.'';
				        	$result = $lsdb->query($sql);
						}
			        }
			  
			 	if (!$result) {
					ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
				} else {
			        ls_redirect(BASE_URL.'index.php?p=success');
			    }
			    
			    }
			    
			    if (isset($defaults['delete'])) {
			    
			    $lockuser = $defaults['ls_delete_user'];
			    $useridarray = explode(',', LS_SUPERADMIN);
			
			        for ($i = 0; $i < count($lockuser); $i++) {
			            $locked = $lockuser[$i];
			            
			            if (!in_array($locked, $useridarray)) {
			            	
			            	// Delete user
			            	$lsdb->query('DELETE FROM '.$lstable.' WHERE id = '.$locked.'');
			        	
						}
						
						$result = 1;
			        }
			  
			 	if (!$result) {
					ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
				} else {
			        ls_redirect(BASE_URL.'index.php?p=success');
			    }
			    
			    }
			
			    
			 }
			 
			if ($LS_SPECIALACCESS) {
				$LS_USER_ALL = ls_get_user_all($lstable, '');		
			} else {
				$LS_USER_ALL = ls_get_user_all($lstable, LS_USERID_RHINO);
			}
			
			// Call the template
			$template = 'user.php';
}
?>