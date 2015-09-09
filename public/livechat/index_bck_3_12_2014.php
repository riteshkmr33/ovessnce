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

// prevent direct php access
define('LS_PREVENT_ACCESS', 1);

if (!file_exists('config.php')) {
    die('[index.php] config.php not exist');
}
require_once 'config.php';

$page = ($tempp ? filter_var($tempp, FILTER_SANITIZE_STRING) : '');
$page1 = ($tempp1 ? filter_var($tempp1, FILTER_SANITIZE_STRING) : '');
$page2 = ($tempp2 ? filter_var($tempp2, FILTER_SANITIZE_STRING) : '');
$page3 = ($tempp3 ? filter_var($tempp3, FILTER_SANITIZE_STRING) : '');
$page4 = ($tempp4 ? filter_var($tempp4, FILTER_SANITIZE_STRING) : '');

// Import the language file
if ($BT_LANGUAGE && file_exists(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini')) {
    $tl = parse_ini_file(APP_PATH.'lang/'.$BT_LANGUAGE.'.ini', true);
} elseif (!$BT_LANGUAGE && file_exists(APP_PATH.'lang/'.LS_LANG.'.ini')) {
	$tl = parse_ini_file(APP_PATH.'lang/'.LS_LANG.'.ini', true);
} else {
    $tl = parse_ini_file(APP_PATH.'lang/en.ini', true);
}

// If Referer Zero go to the session url
if (!isset($_SERVER['HTTP_REFERER'])) {
	if ($_SESSION['ls_lastURL']) {
    	$_SERVER['HTTP_REFERER'] = $_SESSION['ls_lastURL'];
    } else {
    	$_SERVER['HTTP_REFERER'] = BASE_URL;
    }
}

// Lang and pages file for template
define('LS_SITELANG', LS_LANG);

// Assign Pages to template
define('LS_PAGINATE_ADMIN', 0);

// Define the avatarpath in the settings
define(LS_FILEPATH_BASE, BASE_URL.LS_FILES_DIRECTORY);

// Define the real request
$realrequest = substr($getURL->lsRealrequest(), 1);
define('LS_PARSE_REQUEST', $realrequest);

// Get the users ip address
$ipa = get_ip_address();

// Check if the ip is blocked, if so redirect to contact page with a message
if (defined(LS_IP_BLOCK)) {
	$blockedips = explode(',', LS_IP_BLOCK);
	if (in_array($ipa, $blockedips)) {
		$USR_IP_BLOCKED = $tl['error']['e11'];
	}
}

// Now get the available departments
$lv_departments = online_operators($LV_DEPARTMENTS, $page3, $page4);

// Finally get the captcha if wish so
if (LS_CAPTCHA) {
	
	if (isset($_SESSION['jrc_captcha'])) {
		
		$human_captcha = explode(':#:', $_SESSION['jrc_captcha']);
		
		$random_name = $human_captcha[0];
		$random_value = $human_captcha[1];

	} else {
		
		$random_name = rand();
		$random_value = rand();
		
		$_SESSION['jrc_captcha'] = $random_name.':#:'.$random_value;
		
	}

}

// Set the check page to 0
$LS_CHECK_PAGE = 0;
	
	// let's do the dirty work
	if ($page == 'start') {
	
		if ($lv_departments && !$USR_IP_BLOCKED) {
		
			if (!LS_CHAT_DIRECT) {
			
				require_once 'quickstart.php';
				$LS_CHECK_PAGE = 1;
				$PAGE_SHOWTITLE = 1;
			
			} else {
			
				require_once 'start.php';
				$LS_CHECK_PAGE = 1;
				$PAGE_SHOWTITLE = 1;
			}
			
		} else {
			
			require_once 'contact.php';
			$LS_CHECK_PAGE = 1;
			$PAGE_SHOWTITLE = 1;
		}
	}
	// Start the chat
	if ($page == 'chat') {
		require_once 'chat.php';
		$LS_CHECK_PAGE = 1;
		$PAGE_SHOWTITLE = 1;
	}
	// Stop and Feedback the chat
	if ($page == 'feedback') {
		require_once 'feedback.php';
		$LS_CHECK_PAGE = 1;
		$PAGE_SHOWTITLE = 1;
	}
	// Stop the chat
	if ($page == 'stop') {
		require_once 'stop.php';
		$LS_CHECK_PAGE = 1;
		$PAGE_SHOWTITLE = 1;
	}
	// Stop the chat
	if ($page == 'contact') {
		require_once 'contact.php';
		$LS_CHECK_PAGE = 1;
		$PAGE_SHOWTITLE = 1;
	}
	// Get the button
	if ($page == 'b') {
	    require_once 'button.php';
	    $LS_CHECK_PAGE = 1;
	    $PAGE_SHOWTITLE = 1;
	}
	// Get the safari fix page
	if ($page == 'safari') {
		$PAGE_TITLE = 'Safari ';
		require_once 'safari.php';
		$LS_CHECK_PAGE = 1;
		$PAGE_SHOWTITLE = 1;
	}
    // Get the 404 page
   	if ($page == '404') {
   	    $PAGE_TITLE = '404 ';
   	    require_once '404.php';
   	    $LS_CHECK_PAGE = 1;
   	    $PAGE_SHOWTITLE = 1;
   	}

// if page not found
if ($LS_CHECK_PAGE == 0) {
    ls_redirect(LS_rewrite::lsParseurl('404', '', '', '', ''));
}

// Finally close all db connections
$lsdb->ls_close();
?>
