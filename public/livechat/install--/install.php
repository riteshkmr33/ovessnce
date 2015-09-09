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

if (!file_exists('../include/db.php')) die('[install.php] include/db.php not exist');
require_once '../include/db.php';

/* NO CHANGES FROM HERE */

// Get the ls DB class
if (LS_MYSQL_CONNECTION == 1) {
	require_once '../class/class.db.php';
} else {
	require_once '../class/class.dbn.php';
}

// Absolute Path
define('DIR_APPLICATION', str_replace('\'', '/', realpath(dirname(__FILE__))) . '/');
define('DIR_Rhino', str_replace('\'', '/', realpath(DIR_APPLICATION . '../')) . '/');

function smartsql($value)
{
	global $lsdb;
    if (!is_int($value)) {
        $value = $lsdb->real_escape_string($value);
    }
    return $value;
}

// Errors is array
$errors = array();
// Show form
$show_form = true;
// Check if db has content already
$check_db_content = false;

// MySQL/i connection
if (DB_USER && DB_PASS) {

	@$linkdb = mysql_connect(DB_HOST.':'.DB_PORT, DB_USER, DB_PASS, DB_NAME);
	@mysql_select_db(DB_NAME);

	@$result = mysql_query('SELECT title FROM '.DB_PREFIX.'departments WHERE id = 1 LIMIT 1');
	
	if ($result) {
	    $check_db_content = true;
	}
	
	// Finally close all db connections
	@mysql_close($linkdb);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Installation Rhino Socket</title>
	<meta charset="utf-8">
	<meta name="author" content="Rhino (http://www.livesupportrhino.com)" />
	<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="../css/stylesheet.css" type="text/css" media="screen" />
</head>
<style>
body {
	padding-top: 60px;
}

</style>
<body>

<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="navbar-brand" href="http://www.livesupportrhino.com">Installation Rhino Socket</a>
        </div>
      </div>
    </div>

<div class="container">
<div class="row">
<div class="col-md-12">
<div class="jumbotron">
<h2>Installation - Wizard</h2>
Please read or watch the <a href="/install/Installation-English.html">installation manual/video</a> very carefully!
</div>


<?php if (isset($_POST['install']) && $_GET['step'] == 2) {

// MySQL/i connection
if (DB_USER && DB_PASS) {
$lsdb = new ls_mysql(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$lsdb->set_charset("utf8");
}

$lsdb->query("CREATE TABLE ".DB_PREFIX."autoproactive (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(300) NOT NULL,
  `message` varchar(200) NOT NULL,
  `showalert` smallint(1) unsigned NOT NULL DEFAULT '1',
  `wayin` varchar(100) DEFAULT NULL,
  `wayout` varchar(100) DEFAULT NULL,
  `timeonsite` smallint(3) unsigned NOT NULL DEFAULT '2',
  `visitedsites` smallint(2) unsigned NOT NULL DEFAULT '1',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."buttonstats (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `depid` int(10) unsigned NOT NULL DEFAULT '0',
  `opid` int(10) unsigned NOT NULL DEFAULT '0',
  `referrer` varchar(255) DEFAULT NULL,
  `firstreferrer` varchar(255) DEFAULT NULL,
  `agent` varchar(255) DEFAULT NULL,
  `hits` int(10) NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL DEFAULT '0',
  `proactive` int(11) NOT NULL DEFAULT '0',
  `message` varchar(255) DEFAULT NULL,
  `readtime` smallint(1) NOT NULL DEFAULT '0',
  `session` varchar(64) DEFAULT NULL,
  `sessionid` varchar(64) DEFAULT NULL,
  `lasttime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."clientcontact (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessionid` int(11) unsigned NOT NULL DEFAULT '0',
  `operatorid` int(11) unsigned NOT NULL DEFAULT '0',
  `operatorname` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."departments (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `email` varchar(255) DEFAULT NULL,
  `active` smallint(1) unsigned NOT NULL DEFAULT '1',
  `dorder` smallint(2) unsigned NOT NULL DEFAULT '1',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2");


$lsdb->query("INSERT INTO ".DB_PREFIX."departments (`id`, `title`, `description`, `active`, `dorder`, `time`) VALUES
(1, 'My First Department', 'Edit this department to your needs...', 1, 1, NOW())");

$lsdb->query("CREATE TABLE ".DB_PREFIX."files (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(300) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."loginlog (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `fromwhere` varchar(255) DEFAULT NULL,
  `ip` char(15) NOT NULL,
  `usragent` varchar(255) DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` smallint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."operatorchat (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fromid` int(11) NOT NULL DEFAULT '0',
  `toid` int(11) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `sent` int(10) NOT NULL DEFAULT '0',
  `received` smallint(1) unsigned NOT NULL DEFAULT '0',
  `msgpublic` smallint(1) unsigned NOT NULL DEFAULT '0',
  `system_message` varchar(3) DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."operatortyping (
  `typingfrom` int(11) NOT NULL,
  `typingto` int(11) NOT NULL,
  `typingstatus` int(11) NOT NULL,
  UNIQUE KEY `typingfrom` (`typingfrom`,`typingto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

$lsdb->query("CREATE TABLE ".DB_PREFIX."responses (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `message` varchar(3000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2");

$lsdb->query("INSERT INTO ".DB_PREFIX."responses (`id`, `title`, `message`) VALUES
(1, 'Assist Today', 'How can I assist you today?')");

$lsdb->query("CREATE TABLE ".DB_PREFIX."sessions (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(200) NOT NULL,
  `department` int(11) unsigned NOT NULL DEFAULT '0',
  `operatorid` int(11) unsigned NOT NULL DEFAULT '0',
  `operatorname` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `ip` char(15) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `countrycode` varchar(2) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `creferrer` varchar(255) DEFAULT NULL,
  `notes` text,
  `sendfiles` smallint(1) unsigned DEFAULT '0',
  `initiated` int(11) unsigned NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '0',
  `fcontact` smallint(1) unsigned NOT NULL DEFAULT '0',
  `knockknock` smallint(1) unsigned NOT NULL DEFAULT '0',
  `ended` int(11) unsigned NOT NULL,
  `updated` int(11) unsigned NOT NULL,
  `answered` int(11) unsigned NOT NULL,
  `u_status` int(10) unsigned DEFAULT '0',
  `u_typing` smallint(1) unsigned NOT NULL DEFAULT '0',
  `o_typing` smallint(1) unsigned NOT NULL DEFAULT '0',
  `msg_status` smallint(1) unsigned NOT NULL DEFAULT '0',
  `denied` smallint(1) unsigned NOT NULL DEFAULT '0',
  `deniedoid` int(11) unsigned NOT NULL DEFAULT '0',
  `transferid` int(11) unsigned NOT NULL DEFAULT '0',
  `transfermsg` varchar(255) DEFAULT NULL,
  `hide` smallint(1) unsigned NOT NULL DEFAULT '0',
  `session` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."setting (
  `varname` varchar(100) NOT NULL DEFAULT '',
  `groupname` varchar(50) DEFAULT NULL,
  `value` mediumtext,
  `defaultvalue` mediumtext,
  `optioncode` mediumtext,
  `datatype` enum('free','number','boolean','bitfield','username','integer','posint') NOT NULL DEFAULT 'free',
  `product` varchar(25) DEFAULT '',
  PRIMARY KEY (`varname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

$lsdb->query("INSERT INTO ".DB_PREFIX."setting (`varname`, `groupname`, `value`, `defaultvalue`, `optioncode`, `datatype`, `product`) VALUES
('version', 'version', '2.0', '2.0', NULL, 'free', 'rhinosocket'),
('title', 'setting', 'Rhino Socket', 'Live Chat Socket', 'input', 'free', 'rhinosocket'),
('email', 'setting', '', '@rhinosocket', 'input', 'free', 'rhinosocket'),
('emailcc', 'setting', '', '@rhinocc', 'input', 'free', 'rhinosocket'),
('crating', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket'),
('sitehttps', 'setting', '0', '0', 'yesno', 'boolean', 'rhinosocket'),
('feedback', 'setting', '1', '1', 'yesno', 'boolean', 'rhinosocket'),
('dateformat', 'setting', 'd.m.Y', 'd.m.Y', 'input', 'free', 'rhinosocket'),
('timeformat', 'setting', ' - H:i', 'h:i A', 'input', 'free', 'rhinosocket'),
('langdirection', 'setting', '1', '1', 'yesno', 'boolean', 'rhinosocket'),
('timezoneserver', 'setting', 'Europe/Zurich', 'Europe/Zurich', 'select', 'free', 'rhinosocket'),
('lang', 'setting', 'en', 'en', 'input', 'free', 'rhinosocket'),
('useravatwidth', 'setting', '150', '150', 'input', 'free', 'rhinosocket'),
('useravatheight', 'setting', '113', '113', 'input', 'free', 'rhinosocket'),
('ip_block', 'setting', '', NULL, 'textarea', 'free', 'rhinosocket'),
('email_block', 'setting', '', NULL, 'textarea', 'free', 'rhinosocket'),
('chat_direct', 'setting', '1', '1', 'yesno', 'boolean', 'rhinosocket'),
('client_email', 'setting', '0', '0', 'yesno', 'boolean', 'rhinosocket'),
('client_semail', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket'),
('client_phone', 'setting', '0', '0', 'yesno', 'boolean', 'rhinosocket'),
('client_sphone', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket'),
('client_question', 'setting', '0', '0', 'yesno', 'boolean', 'rhinosocket'),
('client_squestion', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket'),
('allowed_files', 'setting', '.zip,.rar,.jpg,.jpeg,.png,.gif', '.zip,.rar,.jpg,.jpeg,.png,.gif', 'input', 'free', 'rhinosocket'),
('allowedo_files', 'setting', '.zip,.rar,.jpg,.jpeg,.png,.gif', '.zip,.rar,.jpg,.jpeg,.png,.gif', 'input', 'free', 'rhinosocket'),
('captcha', 'setting', '0', '1', 'yesno', 'boolean', 'rhinosocket'),
('smilies', 'setting', '0', '1', 'yesno', 'boolean', 'rhinosocket'),
('updated', 'setting', '".time()."', '".time()."', 'time', 'number', 'rhinosocket'),
('pro_alert', 'setting', '1', '1', 'yesno', 'boolean', 'rhinosocket'),
('pro_wayin', 'setting', 'bounce', 'bounce', 'input', 'free', 'rhinosocket'),
('pro_wayout', 'setting', 'hinge', 'hinge', 'input', 'free', 'rhinosocket'),
('o_number', 'setting', '0', '0', 'input', 'free', 'rhinosocket'),
('wait_message3', 'setting', '1', '1', 'yesno', 'boolean', 'rhinosocket'),
('facebook', 'setting', '', '', 'textarea', 'free', 'rhinosocket'),
('twitter', 'setting', '', '', 'textarea', 'free', 'rhinosocket'),
('facebook_big', 'setting', '', '', 'textarea', 'free', 'rhinosocket'),
('twitter_big', 'setting', '', '', 'textarea', 'free', 'rhinosocket'),
('twilio_nexmo', 'setting', '0', '1', 'yesno', 'boolean', 'rhinosocket'),
('tw_msg', 'setting', 'A customer is requesting attention.', 'A customer is requesting attention.', 'input', 'free', 'rhinosocket'),
('tw_phone', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('tw_sid', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('tw_token', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('font_tpl', 'setting', 'Arial, Helvetica, sans-serif', 'Arial, Helvetica, sans-serif', 'input', 'free', 'rhinosocket'),
('fontg_tpl', 'setting', 'Lobster', 'Lobster', 'input', 'free', 'rhinosocket'),
('fcolor_tpl', 'setting', '#494949', '#494949', 'input', 'free', 'rhinosocket'),
('fhccolor_tpl', 'setting', '#494949', '#494949', 'input', 'free', 'rhinosocket'),
('fhcolor_tpl', 'setting', '#494949', '#494949', 'input', 'free', 'rhinosocket'),
('facolor_tpl', 'setting', '#2f942b', '#2f942b', 'input', 'free', 'rhinosocket'),
('bgcolor_tpl', 'setting', '#ffffff', '#ffffff', 'input', 'free', 'rhinosocket'),
('iccolor_tpl', 'setting', '#f9f9f9', '#f9f9f9', 'input', 'free', 'rhinosocket'),
('smtp_mail', 'setting', 0, 0, 'yesno', 'boolean', 'rhinosocket'),
('smtpport', 'setting', 25, 25, 'input', 'number', 'rhinosocket'),
('smtphost', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('smtp_auth', 'setting', 0, 0, 'yesno', 'boolean', 'rhinosocket'),
('smtp_prefix', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('smtp_alive', 'setting', 0, 0, 'yesno', 'boolean', 'rhinosocket'),
('smtpusername', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('smtppassword', 'setting', '', '', 'input', 'free', 'rhinosocket')");

$lsdb->query("CREATE TABLE ".DB_PREFIX."transcript (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `user` varchar(100) NOT NULL,
  `convid` int(11) unsigned NOT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `class` varchar(20) NOT NULL,
  `plevel` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."user (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departments` varchar(100) DEFAULT '0',
  `available` smallint(1) unsigned NOT NULL DEFAULT '0',
  `busy` smallint(1) unsigned NOT NULL DEFAULT '0',
  `tw_days` varchar(30) DEFAULT NULL,
  `tw_time_from` time NOT NULL DEFAULT '00:00:00',
  `tw_time_to` time NOT NULL DEFAULT '00:00:00',
  `dnotify` smallint(1) unsigned NOT NULL DEFAULT '0',
  `phonenumber` varchar(255) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` char(64) NOT NULL,
  `idhash` varchar(32) DEFAULT NULL,
  `session` varchar(64) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `picture` varchar(255) NOT NULL DEFAULT '/standard.png',
  `language` varchar(10) DEFAULT NULL,
  `invitationmsg` varchar(255) DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `logins` int(11) unsigned NOT NULL DEFAULT '0',
  `responses` smallint(1) unsigned NOT NULL DEFAULT '1',
  `files` smallint(1) unsigned NOT NULL DEFAULT '1',
  `operatorchat` smallint(1) NOT NULL DEFAULT '0',
  `operatorlist` tinyint(1) NOT NULL DEFAULT '0',
  `sound` smallint(1) NOT NULL DEFAULT '1',
  `ringing` smallint(2) NOT NULL DEFAULT '3',
  `emailnot` smallint(1) NOT NULL DEFAULT '0',
  `access` smallint(1) unsigned NOT NULL DEFAULT '0',
  `permissions` varchar(512) DEFAULT NULL,
  `forgot` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."user_stats (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) unsigned NOT NULL,
  `vote` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comment` text,
  `support_time` int(10) unsigned NOT NULL DEFAULT '0',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

// Finally close all db connections
$lsdb->ls_close();

?>
<div class="alert alert-info">Database installed successfully.</div>
<form id="company" method="post" action="install.php?step=3" enctype="multipart/form-data">

<div class="form-actions">
<button type="submit" name="user" class="btn btn-primary pull-right">Setup Super Operator</button>
</div>

</form>
<?php } elseif (isset($_POST['user']) && $_GET['step'] == 3) { ?>
Last Step - Create Admin<br /><br />
<?php

if (isset($_POST['user']) && isset($_POST['pass'])) {

if ($_POST['email'] == '' || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors = 'Please insert a valid email address.<br />';
}

if (!preg_match('/^([a-zA-Z0-9\-_])+$/', $_POST['username'])) {
	$errors .= 'Please insert a valid username (A-Z,a-z,0-9,-_).<br />';
}

if ($_POST['onumber'] == '') $errors .= 'Please insert your order number.<br />';

if (count($errors) == 0) {

// MySQL/i connection
if (DB_USER && DB_PASS) {
$lsdb = new ls_mysql(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$lsdb->set_charset("utf8");
}

// The new password encrypt with hash_hmac
$passcrypt = hash_hmac('sha256', $_POST['pass'], DB_PASS_HASH);
 
$lsdb->query('INSERT INTO '.DB_PREFIX.'user SET
	username = "'.smartsql($_POST['username']).'",
	password = "'.$passcrypt.'",
	email = "'.smartsql($_POST['email']).'",
	name = "'.smartsql($_POST['name']).'",
	operatorchat = 1,
	time = NOW(),
	access = 1');
	
$lsdb->query('UPDATE '.DB_PREFIX.'setting SET value = "'.smartsql($_POST['email']).'" WHERE varname = "email"');

$lsdb->query('UPDATE '.DB_PREFIX.'setting SET value = "'.smartsql($_POST['onumber']).'" WHERE varname = "o_number"');

@$lsdb->query('ALTER DATABASE '.DB_NAME.' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
    
// Finally close all db connections
$lsdb->ls_close();

// confirm
include_once '../class/PHPMailerAutoload.php';

$email_body = 'URL: '.FULL_SITE_DOMAIN.'<br />Email: '.$_POST['email'].'<br />License: '.$_POST['onumber'];

// Send the email to the customer
$mail = new PHPMailer(); // defaults to using php "mail()"
$body = str_ireplace("[\]", "", $email_body);
$mail->SetFrom($_POST['email']);
$mail->AddReplyTo($_POST['email']);
$mail->AddAddress('lic@livesupportrhino.com');
$mail->Subject = 'Install - Rhino Socket 2.0';
$mail->AltBody = 'HTML Format';
$mail->MsgHTML($body);
$mail->Send();

echo '<div class="alert alert-success">Installation successful, please delete or rename the <strong>install</strong> directory. You can now log in, in your <a href="../operator/">operator</a> panel.</div>';

$show_form = false;

} else {
   $errors = $errors;
   
} } 

if ($errors) echo '<div class="alert alert-danger">'.$errors.'</div>';

if ($show_form) { ?>

<form role="form" name="user" method="post" action="install.php?step=3" enctype="multipart/form-data">
<table class="table table-striped">
<tr>
	<td>Order Number <span class="complete">*</span></td>
	<td><input type="text" value="" class="form-control" name="onumber" placeholder="Order Number" /></td>
</tr>
<tr>
	<td>Name <span class="complete">*</span></td>
	<td><input type="text" value="" class="form-control" name="name" placeholder="Name" /></td>
</tr>
<tr>
	<td>Username <span class="complete">*</span></td>
	<td><input type="text" value="" class="form-control" name="username" placeholder="Username" /></td>
</tr>
<tr>
	<td>Password <span class="complete">*</span></td>
	<td><input type="text" value="" class="form-control" name="pass" placeholder="Password" /></td>
</tr>
<tr>
	<td>Email <span class="complete">*</span></td>
	<td><input type="text" value="" class="form-control" name="email" placeholder="Email" /></td>
</tr>
</table>

<div class="form-actions">
<button type="submit" name="user" class="btn btn-primary pull-right">Finish</button>
</div>

</form>
<?php } } if (!isset($_GET['step'])) { ?>

<div class="alert alert-info">
	Please read or watch the <a href="/install/Installation-English.html">installation manual/video</a> very carefully!
</div>

<?php

// Test for the config.php File

if (@file_exists('../config.php')) {
	
	$data_file = '<strong style="color:green">config.php available</strong>';
} else {
	
	$data_file = '<strong style="color:red">config.php not available!</strong>';
}

// Connect to the database

@$linkdb = mysql_connect(DB_HOST.':'.DB_PORT, DB_USER, DB_PASS, DB_NAME);

$result_mysqli = '';
$result_mysqlv = '';

if ($linkdb && DB_USER && DB_PASS) {

	if (function_exists('mysqli_connect') && LS_MYSQL_CONNECTION) {
	  $result_mysqli = '<strong style="color:green">MySQLi extension available, perfect!</strong>';
	} else {
		$result_mysqli = '<strong style="color:green">No support for MySQLi, please change in db.php to MySQL support only (Line 33).</strong>';
	}
	
	$mysqlv = mysql_get_server_info();
	
	if (version_compare($mysqlv, '5.0.7') < 0) {
		$result_mysqlv = '<strong style="color:red">You need a higher version of MySQL (min. MySQL 5.0.7)!</strong>';
	} else {
		$result_mysqlv = '<strong style="color:green">MySQL Version: '.$mysqlv.'</strong>';
	}
 
    $conn_data = '<strong style="color:green">Database connection available</strong>';
} else {
 
	$conn_data = '<strong style="color:red">Could not connect to the database!</strong>';
@mysql_close($linkdb);
}

// Database exist

@$dlink = mysql_select_db(DB_NAME);

if ($dlink) {
 
    $data_exist = '<strong style="color:green">Database available</strong>';
} else {
 
	$data_exist = '<strong style="color:red">Could not find the database!</strong>';
@mysql_close($dlink);
}

// Test the minimum PHP version
$php_version = PHP_VERSION;
$php_big = '';
if (version_compare($php_version, '5.2.0') < 0) {
	$result_php = '<strong style="color:red">You need a higher version of PHP (min. PHP 5.2)!</strong>';
} else {
	
	if (version_compare($php_version, '5.5.3') > 0) $php_big = '<br /><strong style="color:red">The software has not been tested on your php version yet.</strong>';

	// We also give feedback on whether we're running in safe mode
	$result_safe = '<strong style="color:green">PHP Version: '.$php_version.'</strong>';
	if (@ini_get('safe_mode') || strtolower(@ini_get('safe_mode')) == 'on') {
		$result_safe .= ', <strong style="color:red">Safe Mode activated</strong>.';
	} else {
		$result_safe .= '<strong style="color:green">, Safe Mode deactivated.</strong>';
	}
	
	$result_safe .= $php_big;
}

$dircc = DIR_Rhino."/".LS_CACHE_DIRECTORY;
$writecc = false;
// Now really check
			if (file_exists($dircc) && is_dir($dircc))
			{
				if (@is_writable($dircc))
				{
					$writecc = true;
				}
				$existscc = true;
			}

			@$passedcc['files'] = ($existscc && $passedcc['files']) ? true : false;

			@$existscc = ($existscc) ? '<strong style="color:green">Found folder ('.LS_CACHE_DIRECTORY.')</strong>' : '<strong style="color:red">Folder not found! ('.LS_CACHE_DIRECTORY.'), </strong>';
			@$writecc = ($writecc) ? '<strong style="color:green">permission set</strong> ('.LS_CACHE_DIRECTORY.'), ' : (($existscc) ? '<strong style="color:red">permission not set (check guide)!</strong> ('.LS_CACHE_DIRECTORY.'), ' : '');	

// Check if the files directory is writeable			
$dirc = DIR_Rhino."/".LS_FILES_DIRECTORY;
$writec = false;
// Now really check
			if (file_exists($dirc) && is_dir($dirc))
			{
				if (@is_writable($dirc))
				{
					$writec = true;
				}
				$existsc = true;
			}

			@$passedc['files'] = ($existsc && $passedc['files']) ? true : false;

			@$existsc = ($existsc) ? '<strong style="color:green">Found folder</strong> ('.LS_FILES_DIRECTORY.')' : '<strong style="color:red">Folder not found!</strong> ('.LS_FILES_DIRECTORY.')';
			@$writec = ($writec) ? '<strong style="color:green">permission set</strong> ('.LS_FILES_DIRECTORY.')' : (($existsc) ? '<strong style="color:red">permission not set!</strong> ('.LS_FILES_DIRECTORY.')' : '');
			
// GD Graphics Support

if (!extension_loaded("gd")) {

	$gd_data = '<strong style="color:orange">GD-Libary not available</strong>';
} else {
	$gd_data = '<strong style="color:green">GD-Libary available</strong>';
}


?>
<div class="well well-small">
Before we start with the installation, the script will check your server settings, everything <strong style="color:green">green</strong> means ready to go!
</div>

<table class="table table-striped">
<tr>
	<td><strong>What we check</strong></td>
	<td><strong>Result</strong></td>
</tr>
<tr>
	<td>config.php:</td>
	<td><?php echo $data_file;?></td>
</tr>
<tr>
	<td>Database connection</td>
	<td><?php echo $conn_data;?></td>
</tr>
<tr>
	<td>Database Version and MySQLi Support</td>
	<td><?php echo $result_mysqlv;?> / <?php echo $result_mysqli;?></td>
</tr>
<tr>
	<td>Database</td>
	<td><?php echo $data_exist?></td>
</tr>
<tr>
	<td>PHP Version and Safe Mode:</td>
	<td><?php echo @$result_php?> <?php echo $result_safe;?></td>
</tr>
<tr>
	<td valign="top">Folders:</td>
	<td><?php echo $writecc.$writec;?></td>
</tr>
<tr>
	<td>GD Library Support:</td>
	<td><?php echo $gd_data;?></td>
</tr>
</table>
<?php if (file_exists('../config.php') AND ($linkdb) AND ($dlink) && !$check_db_content) { ?>
<form name="company" method="post" action="install.php?step=2" enctype="multipart/form-data">
<div class="form-actions">
<button type="submit" name="install" class="btn btn-primary pull-right">Install Database</button>
</div>
</form>
<?php } elseif ((file_exists('../config.php') AND ($linkdb) AND ($dlink) && $check_db_content)) { ?>
<form name="company" method="post" action="install.php?step=3" enctype="multipart/form-data">
<div class="form-actions">
<button type="submit" name="user" class="btn btn-primary pull-right">(Database exist already) Create User</button>
</div>
</form>
<?php } else { ?>
<input type="button" class="btn" value="Refresh page" onclick="history.go(0)" />
<?php } } ?>

</div>
</div>

<hr>

<footer>
	<p>Copyright 2014 by <a href="http://www.livesupportrhino.com">Live Chat Rhino - Socket</a></p>
</footer>

</div>
</body>
</html>