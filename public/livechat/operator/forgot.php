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

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['newP'])) {
	$defaults = $_POST;
	
    $femail = filter_var($_POST['f_email'], FILTER_SANITIZE_EMAIL);
    $pass = $_POST['f_pass'];
    $newpass = $_POST['f_newpass'];
        
    if ($pass != $newpass) {
    	$errors['e1'] = $tl['error']['e10'];
    } elseif (strlen($pass) <= '5') {
    	$errors['e1'] = $tl['error']['e11'];
    }
    
    if ($defaults['f_email'] == '' || !filter_var($defaults['f_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['e'] = $tl['error']['e3'];
    }
    
    $user_check = $lsuserlogin->lsForgotcheckuser($femail, $page1);
    if ($user_check == true && count($errors) == 0) {
    
    // The new password encrypt with hash_hmac
    $passcrypt = hash_hmac('sha256', $pass, DB_PASS_HASH);
    	
    $result2 = $lsdb->query('UPDATE '.DB_PREFIX.'user SET password = "'.$passcrypt.'", forgot = 0 WHERE email = "'.smartsql($femail).'" AND forgot = "'.smartsql($page1).'"');
	
	$result = $lsdb->query('SELECT username FROM '.DB_PREFIX.'user WHERE email = "'.smartsql($femail).'" LIMIT 1');
	$row = $result->fetch_assoc();
	
	if (!$result) {
    	ls_redirect(JAK_PARSE_ERROR);
	} else {
		$lsuserlogin->lsLogin($row['username'], $pass, 0);
        ls_redirect(BASE_URL);
    }
    
    } else {
    	$errorsf = $errors;
    }
}

// Call the template
$template = 'forgot.php';

?>