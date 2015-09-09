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
if (!ls_get_access("settings", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) ls_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();

// Important template Stuff
$LS_SETTING = ls_get_setting('setting');

// Let's go on with the script
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $defaults = $_POST;
    
    if (isset($defaults['save'])) {
    
    if ($defaults['ls_email'] == '' || !filter_var($defaults['ls_email'], FILTER_VALIDATE_EMAIL)) { 
    	$errors['e1'] = $tl['error']['e3'];
    }
    
    if ($defaults['ls_lang'] == '') { $errors['e6'] = $tl['error']['e29']; }

    if (empty($defaults['ls_date'])) { $errors['e2'] = $tl['error']['e4']; }

    if (count($errors) == 0) {
    
    // if phone is compulsory we need to show the field
    if ($defaults['ls_cphone'] == 1) $defaults['ls_scphone'] = 1;
    
    // Do the dirty work in mysql
    $result = $lsdb->query('UPDATE '.DB_PREFIX.'setting SET value = CASE varname
    	WHEN "title" THEN "'.smartsql($defaults['ls_title']).'"
        WHEN "email" THEN "'.smartsql($defaults['ls_email']).'"
        WHEN "emailcc" THEN "'.smartsql($defaults['ls_emailcc']).'"
        WHEN "chat_direct" THEN '.$defaults['ls_chat_direct'].'
        WHEN "client_email" THEN '.$defaults['ls_cemail'].'
        WHEN "client_semail" THEN '.$defaults['ls_scemail'].'
        WHEN "client_phone" THEN '.$defaults['ls_cphone'].'
        WHEN "client_sphone" THEN '.$defaults['ls_scphone'].'
        WHEN "client_question" THEN '.$defaults['ls_question'].'
        WHEN "client_squestion" THEN '.$defaults['ls_squestion'].'
        WHEN "crating" THEN '.$defaults['ls_rating'].'
        WHEN "captcha" THEN '.$defaults['ls_captcha'].'
        WHEN "smilies" THEN '.$defaults['ls_smilies'].'
        WHEN "sitehttps" THEN '.$defaults['ls_shttp'].'
        WHEN "feedback" THEN '.$defaults['ls_feedback'].'
        WHEN "lang" THEN "'.smartsql($defaults['ls_lang']).'"
        WHEN "langdirection" THEN "'.smartsql($defaults['ls_langd']).'"
        WHEN "useravatwidth" THEN "'.smartsql($defaults['ls_avatwidth']).'"
        WHEN "useravatheight" THEN "'.smartsql($defaults['ls_avatheight']).'"
        WHEN "allowed_files" THEN "'.smartsql($defaults['allowed_files']).'"
        WHEN "allowedo_files" THEN "'.smartsql($defaults['allowedo_files']).'"
        WHEN "dateformat" THEN "'.smartsql($defaults['ls_date']).'"
        WHEN "timeformat" THEN "'.smartsql($defaults['ls_time']).'"
        WHEN "timezoneserver" THEN "'.$defaults['ls_timezone_server'].'"
        WHEN "pro_alert" THEN "'.smartsql($defaults['showalert']).'"
        WHEN "pro_wayin" THEN "'.smartsql($defaults['alertfadein']).'"
        WHEN "pro_wayout" THEN "'.smartsql($defaults['alertfadeout']).'"
        WHEN "wait_message3" THEN '.$defaults['wait_message3'].'
        WHEN "ip_block" THEN "'.smartsql($defaults['ip_block']).'"
        WHEN "email_block" THEN "'.smartsql($defaults['email_block']).'"
        WHEN "twilio_nexmo" THEN '.$defaults['ls_twilio_nexmo'].'
        WHEN "tw_msg" THEN "'.smartsql($defaults['ls_tw_msg']).'"
        WHEN "tw_phone" THEN "'.smartsql($defaults['ls_tw_phone']).'"
        WHEN "tw_sid" THEN "'.base64_encode($defaults['ls_tw_sid']).'"
        WHEN "tw_token" THEN "'.base64_encode($defaults['ls_tw_token']).'"
        WHEN "smtp_mail" THEN "'.smartsql($defaults['ls_smpt']).'"
        WHEN "smtphost" THEN "'.smartsql($defaults['ls_host']).'"
        WHEN "smtpport" THEN "'.smartsql($defaults['ls_port']).'"
        WHEN "smtp_alive" THEN "'.smartsql($defaults['ls_alive']).'"
        WHEN "smtp_auth" THEN "'.smartsql($defaults['ls_auth']).'"
        WHEN "smtp_prefix" THEN "'.smartsql($defaults['ls_prefix']).'"
        WHEN "smtpusername" THEN "'.base64_encode($defaults['ls_smtpusername']).'"
        WHEN "smtppassword" THEN "'.base64_encode($defaults['ls_smtppassword']).'"
    END
		WHERE varname IN ("title","email","emailcc","chat_direct","client_email","client_semail","client_phone","client_sphone","client_question","client_squestion","crating","captcha","smilies","sitehttps","feedback","lang","langdirection","useravatwidth","useravatheight","allowed_files","allowedo_files","dateformat","timeformat","timezoneserver","pro_alert","pro_wayin","pro_wayout","wait_message3","ip_block","email_block","twilio_nexmo","tw_msg","tw_phone","tw_sid","tw_token","smtp_mail","smtphost","smtpport","smtp_alive","smtp_auth","smtp_prefix","smtpusername","smtppassword")');
		
	// Now let us delete the define cache file
	$cachedefinefile = '../'.LS_CACHE_DIRECTORY.'/define.php';
	if (file_exists($cachedefinefile)) {
		unlink($cachedefinefile);
	}
		
	if (!$result) {
		ls_redirect(BASE_URL.'index.php?p=error&sp=mysql');
	} else {		
        ls_redirect(BASE_URL.'index.php?p=success');
    }
    } else {
    
   	$errors['e'] = $tl['error']['e'];
    $errors = $errors;
    }
    
    } else {
    
    	$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
    
    	// Send email the smpt way or else the mail way
    	if (LS_SMTP_MAIL) {
    		
    		try {
        		$mail->IsSMTP(); // telling the class to use SMTP
        		$mail->Host = LS_SMTPHOST;
        		$mail->SMTPAuth = (LS_SMTP_AUTH ? true : false); // enable SMTP authentication
        		$mail->SMTPSecure = LS_SMTP_PREFIX; // sets the prefix to the server
        		$mail->SMTPKeepAlive = (LS_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
        		$mail->Port = LS_SMTPPORT; // set the SMTP port for the GMAIL server
        		$mail->Username = base64_decode(LS_SMTPUSERNAME); // SMTP account username
        		$mail->Password = base64_decode(LS_SMTPPASSWORD);        // SMTP account password
        		$mail->SetFrom(LS_EMAIL, LS_TITLE);
        		$mail->AddReplyTo(LS_EMAIL, LS_TITLE);
        		$mail->AddAddress(LS_EMAIL, LS_TITLE);
        		$mail->AltBody = $tl["general"]["g215"]; // optional, comment out and test
        		$mail->Subject = $tl["general"]["g216"];
        		$mail->MsgHTML($tl["general"]["g217"].'SMTP.');
        		$mail->Send();
        		$success['e'] = $tlnl["general"]["g217"].'SMTP.';
        	} catch (phpmailerException $e) {
    	    	$errors['e'] = $e->errorMessage(); //Pretty error messages from PHPMailer
        	} catch (Exception $e) {
        		$errors['e'] = $e->getMessage(); //Boring error messages from anything else!
        	}
    		
    	} else {
    	
    		try {
        		$mail->SetFrom(LS_EMAIL, LS_TITLE);
        		$mail->AddReplyTo(LS_EMAIL, LS_TITLE);
        		$mail->AddAddress(LS_EMAIL, LS_TITLE);
        		$mail->AltBody = $tl["general"]["g215"]; // optional, comment out and test
        		$mail->Subject = $tl["general"]["g216"];
        		$mail->MsgHTML($tl["general"]["g217"].'Mail().');
        		$mail->Send();
        		$success['e'] = $tl["general"]["g217"].'Mail().';
    		} catch (phpmailerException $e) {
    			$errors['e'] = $e->errorMessage(); //Pretty error messages from PHPMailer
    		} catch (Exception $e) {
    		  	$errors['e'] = $e->getMessage(); //Boring error messages from anything else!
    		}
    	
    	}
    
    }
    
}

// Call the settings function
$lang_files = ls_get_lang_files(false);
// Call the template
$template = 'setting.php';

?>