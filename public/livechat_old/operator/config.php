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

// Do not go any further if install folder still exists
if (is_dir('../install')) die('Please delete or rename install folder.');

// Start the session
session_start();

// The DB connections data
require_once '../include/db.php';

// Get the real stuff
require_once '../config.php';

define('BASE_URL_ADMIN', BASE_URL);
define('BASE_URL_ORIG', str_replace('/operator/', '/', BASE_URL));
define('BASE_PATH_ORIG', str_replace('/operator', '/', _APP_MAIN_DIR));

// Include some functions for the ADMIN Area
include_once 'include/admin.function.php';

// Overwrite url for admin
// We are not using apache so take the ugly urls
$temppa = $getURL->lsGetsegAdmin(0);
$temppa1 = $getURL->lsGetsegAdmin(1);
$temppa2 = $getURL->lsGetsegAdmin(2);
$temppa3 = $getURL->lsGetsegAdmin(3);
$temppa4 = $getURL->lsGetsegAdmin(4);
$temppa5 = $getURL->lsGetsegAdmin(5);
$temppa6 = $getURL->lsGetsegAdmin(6);

// Set the last activity and session into cookies
setcookie('lastactivity', time(), time() + 60 * 60 * 24 * 10, LS_COOKIE_PATH);
setcookie('usrsession', session_id(), time() + 60 * 60 * 24 * 10, LS_COOKIE_PATH);

// Check if user is logged in
$lsuserlogin = new LS_userlogin();
$lsuserrow = $lsuserlogin->lsCheckLogged();
$lsuser = new LS_user($lsuserrow);
if ($lsuser) {
	define(LS_USERID_RHINO, $lsuser->getVar("id"));
} else {
	define(LS_USERID_RHINO, false);
}

if (LS_USERID_RHINO) {

	// Update last activity from this user
	$lsuserlogin->lsUpdatelastactivity(LS_USERID_RHINO);

}
?>