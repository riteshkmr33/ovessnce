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

// Redirect to something...
function ls_redirect($url)
{
    header('Location: '.$url);
    exit;
}

// Get a secure mysql input
function smartsql($value)
{
	global $lsdb;
	if (get_magic_quotes_gpc()) {
	$value = stripslashes($value);
	}
    if (!is_int($value)) {
        $value = $lsdb->real_escape_string($value);
    }
    return $value;
}

// Check if userid can have access to the forum, blog, gallery etc.
function ls_get_access($lsvar,$lsvar1)
{
$usergrouparray = explode(',', $lsvar1);
if (in_array($lsvar, $usergrouparray) || $lsvar == 3) {
return true;
}
}

// Get the setting variable as well the default variable as array
function ls_get_setting($group) 
{
	global $lsdb;
    $sql = 'SELECT varname, value, defaultvalue FROM '.DB_PREFIX.'jrc_setting WHERE groupname = "'.smartsql($group).'"';
    $result = $lsdb->query($sql);
    while ($row = $result->fetch_assoc()) {
        $setting[] = array('varname' => $row['varname'], 'value' => $row['value'], 'defaultvalue' => $row['defaultvalue']);
    }
    return $setting;
}

// Get the data only per ID (e.g. edit single user, edit category)
function ls_get_data($id, $table) 
{
		
	global $lsdb;
    $sql = 'SELECT * FROM '.$table.' WHERE id = "'.smartsql($id).'"';
    $result = $lsdb->query($sql);
    while ($row = $result->fetch_assoc()) {
            // collect each record into $lsdata
            $lsdata = $row;
        }
    return $lsdata;
}

// Check if row exist
function ls_row_exist($id, $table)
{
		global $lsdb;
		$sql = 'SELECT id FROM '.$table.' WHERE id = "'.smartsql($id).'" LIMIT 1';
        $result = $lsdb->query($sql);
        if ($lsdb->affected_rows > 0) {
        	return true;
		}
}

// Verify paramaters
function verifyparam($name, $regexp, $default = null)
{
	if (isset($_GET[$name])) {
		$val = $_GET[$name];
		if (preg_match($regexp, $val))
			return $val;

	} else if (isset($_POST[$name])) {
		$val = $_POST[$name];
		if (preg_match($regexp, $val))
			return $val;

	} else {
		if (isset($default))
			return $default;
	}
	echo "<html><head></head><body>Wrong parameter used or absent: " . $name . "</body></html>";
	exit;
}

// Verfiy if there is a online operator
function online_operators()
{	
	$timeout = time() - 180;
	$timerunout = 1;
	
	global $lsdb;
	
	// Update database first to see who is online!
	$lsdb->query('UPDATE '.DB_PREFIX.'jrc_user SET available = 0 WHERE lastactivity < '.$timeout);
	
	$lsdb->query('SELECT id FROM '.DB_PREFIX.'jrc_user WHERE access = 1 AND available = 1');
		
	if ($lsdb->affected_rows == 0) {
	
		$timerunout = 0;
	}
	
	if ($timerunout) {
		return true;
	} else {
		return false;
	}
}

// Check if the lang folder for buttons exist
function folder_lang_button($lang) 
{
	return file_exists('./img/buttons/'.$lang.'/');
}

// Get the real IP Address
function get_ip_address() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
}

// Replace urls
function replace_urls($string) {
	$string = preg_replace('/(https?|ftp)([\:\/\/])([^\\s]+)/', '<a href="$1$2$3" target="_blank">$1$2$3</a>', $string);
	return $string;
}

// Load the version from jakcms
function jak_load_xml_from_curl($url) {

	if (function_exists('curl_version')) {
    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    	
	    return $output;
    
    } else {
    	return false;
    }
}
?>