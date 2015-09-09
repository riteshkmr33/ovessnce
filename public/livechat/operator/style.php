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
if (!LS_USERID_RHINO || !LS_SUPERADMINACCESS) ls_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();

// Important template Stuff
$LS_SETTING = ls_get_setting('setting');
$lstable = DB_PREFIX.'departments';
$lstable1 = DB_PREFIX.'user';

// Upload a button
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $defaults = $_POST;
    
    if (isset($defaults['upload'])) {

	if ($_FILES['uploadpp']['name'] != '' || $_FILES['uploadpp1']['name'] != '') {
	
	$filename = $_FILES['uploadpp']['name']; // original filename
	$filename1 = $_FILES['uploadpp1']['name']; // original filename
	
	// Fix explode when upload in 1.2
	$tmpf = explode(".", $filename);
	$ls_xtension = end($tmpf);
	
	$tmpf1 = explode(".", $filename1);
	$ls_xtension1 = end($tmpf1);
	
	if (($ls_xtension == "jpg" && $ls_xtension1 == "jpg") || ($ls_xtension == "jpeg" && $ls_xtension1 == "jpeg") || ($ls_xtension == "png" && $ls_xtension1 == "png") || ($ls_xtension == "gif" && $ls_xtension1 == "gif")) {
	
	if ($_FILES['uploadpp']['size'] <= 500000 && $_FILES['uploadpp1']['size'] <= 500000) {
	
	list($width, $height, $type, $attr) = getimagesize($_FILES['uploadpp']['tmp_name']);
	$mime = image_type_to_mime_type($type);
	
	list($width1, $height1, $type1, $attr1) = getimagesize($_FILES['uploadpp1']['tmp_name']);
	$mime1 = image_type_to_mime_type($type1);
	
	if (($mime == "image/jpeg" && $mime1 == "image/jpeg") || ($mime == "image/pjpeg" && $mime1 == "image/pjpeg") || ($mime == "image/png" && $mime1 == "image/png") || ($mime == "image/gif" && $mime1 == "image/gif")) {
	
	// first get the target path
	$targetPath = '../'.LS_FILES_DIRECTORY.'/buttons/';
	
	$tempFile = $_FILES['uploadpp']['tmp_name'];
	$tempFile1 = $_FILES['uploadpp1']['tmp_name'];
	$name_space = strtolower($_FILES['uploadpp']['name']);
	$middle_name = str_replace(" ", "_", $name_space);
	$button = str_replace(".jpeg", ".jpg", $name_space);
	$name_space1 = strtolower($_FILES['uploadpp1']['name']);
	$middle_name1 = str_replace(" ", "_", $name_space1);
	$button1 = str_replace(".jpeg", ".jpg", $name_space1);
	    
	$targetFile =  str_replace('//','/',$targetPath) . $button;
	$targetFile1 =  str_replace('//','/',$targetPath) . $button1;
	
	// Move file
	if (!file_exists($targetFile) && !file_exists($targetFile1)) {
		move_uploaded_file($tempFile,$targetFile);
		move_uploaded_file($tempFile1,$targetFile1);
		
		ls_redirect(BASE_URL.'index.php?p=success');
	} else {
		$errors['e'] = $tl['error']['e24'].'<br />';
	}
	 		
	} else {
		$errors['e'] = $tl['error']['e24'].'<br />';
	}
	
	} else {
		$errors['e'] = $tl['error']['e24'].'<br />';
	}
	
	} else {
		$errors['e'] = $tl['error']['e24'].'<br />';
	}
	
	} else {
		$errors['e'] = $tl['error']['e24'].'<br />';
	}
	
	}
    
    if (isset($defaults['slide_up'])) {
    	
    	$_SESSION['slide_up'] = "on";
    	unset($_SESSION['pop_up']);
    	unset($_SESSION['slide_pop_up']);
    }
    if (isset($defaults['slide_pop_up'])) {
    	
    	$_SESSION['slide_pop_up'] = "on";
    	unset($_SESSION['slide_up']);
    	unset($_SESSION['pop_up']);
    }
    if (isset($defaults['pop_up'])) {
    	
    	$_SESSION['pop_up'] = "on";
    	unset($_SESSION['slide_up']);
    	unset($_SESSION['slide_pop_up']);
    }
    
    if ($defaults['slidechatc'] && isset($_SESSION['show_host'])) {
    	$defaults['hostname'] = false;
    }
    
    if ($defaults['hostname']) {
    	
    	$_SESSION['show_host'] = $defaults['hostname'];
    	unset($_SESSION['slide_chatc']);
    	$defaults['slidechatc'] = false;
    
    } else {
    
    	unset($_SESSION['show_host']);
    }
    
    
    if ($defaults['slidechatc']) {
    	
    	$_SESSION['slide_chatc'] = $defaults['slidechatc'];
    	unset($_SESSION['show_host']);
    
    } else {
    
    	unset($_SESSION['slide_chatc']);
    }
    
    if ($defaults['showimage']) {
    	
    	$_SESSION['showimage'] = $defaults['showimage'];
    
    } else {
    
    	unset($_SESSION['showimage']);
    }
    
    if ($defaults['cproactive']) {
    	
    	$_SESSION['chat_proactive'] = $defaults['cproactive'];
    
    } else {
    
    	unset($_SESSION['chat_proactive']);
    }
    
    if ($defaults['slidechato']) {
    	
    	$_SESSION['slide_chato'] = $defaults['slidechato'];
    
    } else {
    
    	unset($_SESSION['slide_chato']);
    }
    
    if ($defaults['jquerybutton']) {
    	
    	$_SESSION['show_jquery'] = $defaults['jquerybutton'];
    
    } else {
    
    	unset($_SESSION['show_jquery']);
    }
    
    if ($defaults['slidebutton'] && isset($_SESSION['show_float'])) {
    	$defaults['floatbutton'] = false;
    }
    
    if ($defaults['floatbutton']) {
    	
    	$_SESSION['show_float'] = $defaults['floatbutton'];
    	unset($_SESSION['show_slide']);
    	$defaults['slidebutton'] = false;
    
    } else {
    
    	unset($_SESSION['show_float']);
    }
    
    if ($defaults['slidebutton']) {
    	
    	$_SESSION['show_slide'] = $defaults['slidebutton'];
    	unset($_SESSION['show_float']);
    
    } else {
    
    	unset($_SESSION['show_slide']);
    }
    
    if ($defaults['jak_depid']) {
    	
    	$_SESSION['departments'] = $defaults['jak_depid'];
        
    } else {
    
    	unset($_SESSION['departments']);
    }
    
    if ($defaults['jak_depid'] && isset($_SESSION['operator'])) {
    	$defaults['jak_opid'] = false;
    	unset($_SESSION['operator']);
    }
    
    if ($defaults['jak_opid']) {
    	
    	$_SESSION['operator'] = $defaults['jak_opid'];
    	unset($_SESSION['departments']);
   
    } else {
    
    	unset($_SESSION['operator']);
    }
    
    if ($defaults['ls_lang']) {
    	
    	$_SESSION['lang_button'] = $defaults['ls_lang'];
    
    } else {
    
    	unset($_SESSION['lang_button']);
    }
    
    if (count($errors) == 0) {
    
    // Do the dirty work in mysql
	$result = $lsdb->query('UPDATE '.DB_PREFIX.'setting SET value = CASE varname
        WHEN "font_tpl" THEN "'.smartsql($defaults['cFont']).'"
        WHEN "fontg_tpl" THEN "'.smartsql($defaults['gFont']).'"
        WHEN "fcolor_tpl" THEN "'.smartsql($defaults['pfont']).'"
        WHEN "facolor_tpl" THEN "'.smartsql($defaults['pafont']).'"
        WHEN "fhcolor_tpl" THEN "'.smartsql($defaults['pfhead']).'"
        WHEN "fhccolor_tpl" THEN "'.smartsql($defaults['pfheadc']).'"
        WHEN "bgcolor_tpl" THEN "'.smartsql($defaults['pcolor']).'"
        WHEN "iccolor_tpl" THEN "'.smartsql($defaults['icont']).'"
    END
    	WHERE varname IN ("font_tpl", "fontg_tpl", "fcolor_tpl", "facolor_tpl", "fhcolor_tpl", "fhccolor_tpl", "bgcolor_tpl", "iccolor_tpl")');
		
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
        
        $errors = $errors;
    }
}

// Get departments
$result = $lsdb->query('SELECT id, title FROM '.$lstable.' ORDER BY dorder ASC');
while ($row = $result->fetch_assoc()) {
        // collect each record into $_data
        $DEPARTMENTS_ALL[] = $row;
    }

// Get operators
$result1 = $lsdb->query('SELECT id, username FROM '.$lstable1.' WHERE access = 1 ORDER BY username ASC');
while ($row1 = $result1->fetch_assoc()) {
        // collect each record into $_data
        $OPERATORS_ALL[] = $row1;
    }

// Call the settings function
$get_buttons = ls_get_buttons();
// Call the settings function
$lang_files = ls_get_lang_files(false);
// Call the template
$template = 'style.php';

?>