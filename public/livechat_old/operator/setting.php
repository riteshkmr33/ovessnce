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

// Important template Stuff
$LS_SETTING = ls_get_setting('setting');

// Let's go on with the script
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $defaults = $_POST;
    
    if (isset($defaults['button_c'])) {
    
    if ($defaults['hostname']) {
    	
    	$_SESSION['show_host'] = $defaults['hostname'];
    
    } else {
    
    	unset($_SESSION['show_host']);
    }
    
    	ls_redirect(BASE_URL.'index.php?p=success');
    }
    
    if ($defaults['ls_email'] == '' || !filter_var($defaults['ls_email'], FILTER_VALIDATE_EMAIL)) { 
    	$errors['e1'] = $tl['error']['e3'];
    }
    
    if ($defaults['ls_lang'] == '') { $errors['e6'] = $tl['error']['e29']; }

    if (empty($defaults['ls_date'])) { $errors['e2'] = $tl['error']['e4']; }

    if (count($errors) == 0) {
    
    // Do the dirty work in mysql
    
    $sql = 'UPDATE '.DB_PREFIX.'jrc_setting SET value = CASE varname
    	WHEN "title" THEN "'.smartsql($defaults['ls_title']).'"
        WHEN "email" THEN "'.smartsql($defaults['ls_email']).'"
        WHEN "feedback" THEN '.$defaults['ls_feedback'].'
        WHEN "captcha" THEN '.$defaults['ls_captcha'].'
        WHEN "captchachat" THEN '.$defaults['ls_captchac'].'
        WHEN "smilies" THEN '.$defaults['ls_smilies'].'
        WHEN "sitehttps" THEN '.$defaults['ls_shttp'].'
        WHEN "lang" THEN "'.smartsql($defaults['ls_lang']).'"
        WHEN "useravatwidth" THEN "'.smartsql($defaults['ls_avatwidth']).'"
        WHEN "useravatheight" THEN "'.smartsql($defaults['ls_avatheight']).'"
        WHEN "dateformat" THEN "'.smartsql($defaults['ls_date']).'"
        WHEN "timeformat" THEN "'.smartsql($defaults['ls_time']).'"
        WHEN "timezoneserver" THEN "'.$defaults['ls_timezone_server'].'"
        WHEN "offline_message" THEN "'.smartsql($defaults['offline_message']).'"
        WHEN "login_message" THEN "'.smartsql($defaults['login_message']).'"
        WHEN "welcome_message" THEN "'.smartsql($defaults['welcome_message']).'"
        WHEN "leave_message" THEN "'.smartsql($defaults['leave_message']).'"
        WHEN "thankyou_message" THEN "'.smartsql($defaults['thankyou_message']).'"
        WHEN "feedback_message" THEN "'.smartsql($defaults['feedback_message']).'"
        WHEN "thankyou_feedback" THEN "'.smartsql($defaults['thankyou_feedback']).'"
    END
		WHERE varname IN ("title","email","feedback","captcha","captchachat","smilies","sitehttps","lang","useravatwidth","useravatheight","dateformat","timeformat","timezoneserver","offline_message","login_message","welcome_message","leave_message","thankyou_message","feedback_message","thankyou_feedback")';
		$result = $lsdb->query($sql);
		
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
}

// Call the settings function
$lang_files = ls_get_lang_files(false);
$get_buttons = ls_get_buttons();
// Call the template
$template = 'setting.php';

?>