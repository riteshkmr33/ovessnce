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

// Get the data per array for page,newsletter with limit
function ls_get_page_info($lsvar,$lsvar1) 
{
	global $lsdb;
    $sql = 'SELECT * FROM '.$lsvar.' ORDER BY id DESC '.$lsvar1.'';
    $result = $lsdb->query($sql);
    while ($row = $result->fetch_assoc()) {
            // collect each record into $_data
            $lsdata[] = $row;
        }
        
    return $lsdata;
}

// Search for lang files in the admin folder, only choose .ini files.
function ls_get_lang_files($folder) {

if ($folder) {
	$langdir = './lang/';
} else {
	$langdir = '../lang/';
}

if ($handle = opendir($langdir)) {

    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
    $showini = substr($file, strrpos($file, '.'));
    if ($file != '.' && $file != '..' && $showini == '.ini') {
    
    	$getlang[] = substr($file, 0, -4);
    
    }
    }
	return $getlang;
    closedir($handle);
}
}

// Search for lang files in the admin folder, only choose .ini files.
function ls_get_buttons() {

$buttonsdir = '../'.LS_FILES_DIRECTORY.'/buttons/';

if ($handle = opendir($buttonsdir)) {

    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
    
    if ($file != '.' && $file != '..' && $file != 'index.html' && substr($file, 0, strpos($file, '_on'))) {
    
    	list($width, $height)= getimagesize($buttonsdir.$file);
    	
    	$getbutton[] = array('name' => $file, 'shortname' => strtok($file, '_'), 'width' => $width, 'height' => $height);
    
    }
    }
	return $getbutton;
    closedir($handle);
}
}

// Get all user out the database limited with the paginator
function ls_get_user_all($lsvar, $userid) 
{
	
	if ($userid) {
	
		$sqlwhere = ' WHERE id = "'.$userid.'"';
	}
	
	global $lsdb;
	$sql = 'SELECT * FROM '.$lsvar.$sqlwhere;
    $result = $lsdb->query($sql);
    while ($row = $result->fetch_assoc()) {
        $user[] = array('id' => $row['id'], 'username' => $row['username'], 'email' => $row['email'], 'name' => $row['name'], 'access' => $row['access']);
    }
    
    return $user;
}

// Check if row exist with custom field
function ls_field_not_exist($check, $table, $field)
{
		global $lsdb;
		$sql = 'SELECT id FROM '.$table.' WHERE LOWER('.$field.') = "'.smartsql($check).'" LIMIT 1';
        $result = $lsdb->query($sql);
        if ($lsdb->affected_rows > 0) {
        	return true;
		}
}

// Check if user exist and it is possible to delete ## (config.php)
function ls_user_exist_deletable($lsvar)
{
global $lsdb;
$useridarray = explode(',', LS_SUPERADMIN);
// check if userid is protected in the config.php
if (in_array($lsvar, $useridarray)) {
        return false;
        exit;
} else {
		$sql = 'SELECT id FROM '.DB_PREFIX.'user WHERE id = "'.smartsql($lsvar).'" LIMIT 1';
        $result = $lsdb->query($sql);
        if ($lsdb->affected_rows > 0) {
        return true;
}
}
}

// Check if row exist with id
function ls_field_not_exist_id($lsvar,$lsvar1,$lsvar2,$lsvar3)
{
		global $lsdb;
		$sql = 'SELECT id FROM '.$lsvar2.' WHERE id != "'.smartsql($lsvar1).'" AND '.$lsvar3.' = "'.smartsql($lsvar).'" LIMIT 1';
        $result = $lsdb->query($sql);
        if ($lsdb->affected_rows > 0) {
        return true;
}
}

// Get files
function ls_get_files($directory,$exempt = array('.','..','.ds_store','.svn','js','css','img','_cache','index.html'),&$files = array()) { 

if ($handle = opendir($directory)) {

    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
    $showini = substr($file, strrpos($file, '.'));
    if (!in_array($file, $exempt)) {
    
    	$getlang[] = $file;
    
    }
    }
	return $getlang;
    closedir($handle);
} 
}

// Load the version from Gecko CMS
function jak_load_xml_from_url($jakvar) {

	if ($jakvar) return simplexml_load_string(jak_load_xml_from_curl($jakvar));
}

function secondsToTime($seconds,$time) {
	$singletime = explode(",", $time);
	if (is_numeric($seconds)) {
    	$dtF = new DateTime("@0");
    	$dtT = new DateTime("@$seconds");
    	return $dtF->diff($dtT)->format('%a '.$singletime[0].', %h '.$singletime[1].', %i '.$singletime[2].' '.$singletime[4].' %s '.$singletime[3]);
    } else {
    	return '0 '.$singletime[0].', 0 '.$singletime[1].', 0 '.$singletime[2].' '.$singletime[4].' 0 '.$singletime[3];
    }
}
?>