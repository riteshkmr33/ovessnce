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

if (!file_exists('../config.php')) die('[uploader.php] config.php not found');
require_once '../config.php';

if(!isset($_SESSION['lc_idhash'])) die("Nothing to see here");

if ($_REQUEST["operatorLanguage"] && file_exists(APP_PATH.'lang/'.$_REQUEST["operatorLanguage"].'.ini')) {
    $tl = parse_ini_file(APP_PATH.'lang/'.$_REQUEST["operatorLanguage"].'.ini', true);
} elseif (!$_REQUEST["operatorLanguage"] && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'lang/'.LS_LANG.'.ini', true);
} else {
    trigger_error('Translation file not found');
}

// The new file upload stuff
if (!empty($_FILES['uploadpp']['name']) && is_numeric($_REQUEST["convID"])) {
	
	$filename = $_FILES['uploadpp']['name']; // original filename
	$ls_xtension = pathinfo($_FILES['uploadpp']['name']);
	
	// Check if the extension is valid
	$allowedf = explode(',', LS_ALLOWEDO_FILES);
	if (in_array(".".$ls_xtension['extension'], $allowedf)) {
	
	if ($_FILES['uploadpp']['size'] <= 2000000) {
	
		// first get the target path
		$targetPathd = '../'.LS_FILES_DIRECTORY.'/operator/';
		$targetPath =  str_replace("//", "/", $targetPathd);
	
	
	    $tempFile = $_FILES['uploadpp']['tmp_name'];
	    $name_space = strtolower($_FILES['uploadpp']['name']);
	    $middle_name = str_replace(" ", "_", $name_space);
	    $middle_name = filter_var($middle_name, FILTER_SANITIZE_STRING);
	    $glnrrand = rand(10, 9999);
	    $ufile = str_replace(".", "_" . $glnrrand . ".", $middle_name);
	    	    
	    $targetFile =  str_replace('//','/',$targetPath).$ufile;
	    $origPath = LS_FILES_DIRECTORY.'/operator/';
	    $filelink = $origPath.$ufile;
	    	
	    // Move file     
	    move_uploaded_file($tempFile, $targetFile);
	    
	    // operatorname
	    $oname = $_REQUEST['operatorNameU'];
	    
	    $lightbox = "";
	    if (getimagesize('../'.$filelink)) $lightbox = ' class="lightbox"';
	    
	    $message = $oname.$tl['general']['g33'].'<a'.$lightbox.' href="'.$_REQUEST['base_url'].$filelink.'" target="_blank">'.$ufile.'</a>';
	    
	    $lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
	    name = "'.smartsql($oname).'",
	    message = "'.smartsql($message).'",
	    user = "'.smartsql($_REQUEST['userIDU']).'",
	    convid = "'.smartsql($_REQUEST["convID"]).'",
	    time = NOW(),
	    class = "download"');
	    
	    $lsdb->query('UPDATE '.DB_PREFIX.'sessions SET
	    answered = "'.time().'",
	    o_typing = 0
	    WHERE id = "'.$_REQUEST["convID"].'"');
	                
	} else {
		$msg = $tl['error']['e9'];
	}
	            
	} else {
	    $msg = $tl['error']['e13'];
	}

switch ($_FILES['uploadpp']['error'])
{
     case 0:
     //$msg = "No Error"; // comment this out if you don't want a message to appear on success.
     break;
     case 1:
     $msg = "The file is bigger than this PHP installation allows";
     break;
     case 2:
     $msg = "The file is bigger than this form allows";
     break;
     case 3:
     $msg = "Only part of the file was uploaded";
     break;
     case 4:
     $msg = "No file was uploaded";
     break;
     case 6:
     $msg = "Missing a temporary folder";
     break;
     case 7:
     $msg = "Failed to write file to disk";
     break;
     case 8:
     $msg = "File upload stopped by extension";
     break;
     default:
     $msg = "unknown error ".$_FILES['uploadpp']['error'];
     break;
}

if ($msg) {
    $stringData = $msg;
} else { 
	$stringData = $tl['general']['s']; // This is required for onComplete to fire on Mac OSX
}
} else {
	$stringData = "error";
}
echo $stringData;
?>