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

if (!file_exists('../include/db.php')) {
    die('[install.php] include/db.php not exist');
}
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

	@$result = mysql_query('SELECT title FROM '.DB_PREFIX.'jrc_responses WHERE id = 1 LIMIT 1');
	
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
	<title>Installation Rhino Light</title>
	<meta charset="utf-8">
	<meta name="author" content="Rhino (http://www.livesupportrhino.com)" />
	<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="../css/stylesheet.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="../css/stylesheet-resp.css" type="text/css" media="screen" />
</head>
<style>
body {
	padding-top: 60px;
}

</style>
<body>

<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="brand" href="http://www.livesupportrhino.com">Installation Rhino Light</a>
        </div>
      </div>
    </div>

<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<div class="hero-unit">
<h2>Installation - Wizard</h2>
Please read or watch the <a href="/install/Installation-English.html">installation manual/video</a> very carefully
</div>

<?php if (isset($_POST['install']) && $_GET['step'] == 2) {

// MySQL/i connection
if (DB_USER && DB_PASS) {
$lsdb = new ls_mysql(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$lsdb->set_charset("utf8");
}

$lsdb->query("CREATE TABLE ".DB_PREFIX."jrc_files (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(300) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."jrc_loginlog (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `fromwhere` varchar(255) DEFAULT NULL,
  `ip` char(15) NOT NULL,
  `usragent` varchar(255) DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` smallint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."jrc_responses (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `message` varchar(3000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2");

$lsdb->query("INSERT INTO ".DB_PREFIX."jrc_responses VALUES(1, 'Assist Today', 'How can I assist you today?')");

$lsdb->query("CREATE TABLE ".DB_PREFIX."jrc_sessions (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(255) NOT NULL,
  `convid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `initiated` int(11) NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT 0,
  `ended` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `answered` int(11) NOT NULL,
  `u_typing` smallint(1) unsigned NOT NULL DEFAULT 0,
  `o_typing` smallint(1) unsigned NOT NULL DEFAULT 0,
  `contact` smallint(1) unsigned NOT NULL DEFAULT 0,
  `hide` smallint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$lsdb->query("CREATE TABLE ".DB_PREFIX."jrc_setting (
  `varname` varchar(100) NOT NULL DEFAULT '',
  `groupname` varchar(50) DEFAULT NULL,
  `value` mediumtext,
  `defaultvalue` mediumtext,
  `optioncode` mediumtext,
  `datatype` enum('free','number','boolean','bitfield','username','integer','posint') NOT NULL DEFAULT 'free',
  `product` varchar(25) DEFAULT '',
  PRIMARY KEY (`varname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

$lsdb->query("INSERT INTO ".DB_PREFIX."jrc_setting VALUES('version', 'version', '2.5', '2.5', NULL, 'free', 'rhino'), ('thankyou_message', 'setting', 'Thank you for your message. We will be in touch as soon as possible!', 'Thank you for your message.  We will be in touch as soon as possible!', 'textarea', 'free', 'rhino'), ('title', 'setting', 'Live Support - Rhino', 'Live Support - Rhino', 'input', 'free', 'rhino'), ('email', 'setting', '', 'ls_rhino', 'input', 'free', 'rhino'), ('sitehttps', 'setting', '0', '0', 'yesno', 'boolean', 'rhino'), ('dateformat', 'setting', 'd.m.Y', 'd.m.Y', 'input', 'free', 'rhino'), ('timeformat', 'setting', ' - H:i', 'h:i A', 'input', 'free', 'rhino'), ('leave_message', 'setting', 'None of our representatives are currently available. Please use the form below to send us an email.', 'None of our representatives are currently available.  Please use the form below to send us an email.', 'textarea', 'free', 'rhino'), ('welcome_message', 'setting', 'Welcome, a representative will be with you shortly', 'Welcome, a representative will be with you shortly', 'textarea', 'free', 'rhino'), ('feedback_message', 'setting', 'Please rate the conversation and let us know what we can improve.', 'Please rate the conversation and let us know what we can improve.', 'textarea', 'free', 'rhino'), ('thankyou_feedback', 'setting', 'Thank you for taking the time to give us your feedback.', 'Thank you for taking the time to give us your feedback.', 'textarea', 'free', 'rhino'), ('timezoneserver', 'setting', 'Europe/Zurich', 'Europe/Zurich', 'select', 'free', 'rhino'), ('lang', 'setting', 'en', 'en', 'input', 'free', 'rhino'), ('useravatwidth', 'setting', '150', '150', 'input', 'free', 'rhino'), ('useravatheight', 'setting', '113', '113', 'input', 'free', 'rhino'), ('login_message', 'setting', 'Please type your name to begin. Entering your email address is optional, although if you would like to be contacted in the future, please add your email address and tick the checkbox before starting your session.', 'Please type your name to begin. Entering your email address is optional, although if you would like to be contacted in the future, please add your email address and tick the checkbox before starting your session.', 'textarea', 'free', 'rhino'), ('offline_message', 'setting', 'None of our representatives are available right now, although you are welcome to leave a message!', 'None of our representatives are available right now, although you are welcome to leave a message!', 'textarea', 'free', 'rhino'), ('feedback', 'setting', '1', '1', 'yesno', 'boolean', 'rhino'), ('captcha', 'setting', '1', '1', 'yesno', 'boolean', 'rhino'), ('captchachat', 'setting', '1', '1', 'yesno', 'boolean', 'rhinopro'), ('smilies', 'setting', '1', '1', 'yesno', 'boolean', 'rhino'), ('updated', 'setting', '".time()."', '".time()."', 'time', 'number', 'rhino')");

$lsdb->query('CREATE TABLE '.DB_PREFIX.'jrc_transcript (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `message` text,
  `user` varchar(100) NOT NULL,
  `convid` int(11) unsigned NOT NULL,
  `time` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `class` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

$lsdb->query('CREATE TABLE '.DB_PREFIX.'jrc_user (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `available` smallint(1) unsigned NOT NULL DEFAULT 0,
  `username` varchar(100) DEFAULT NULL,
  `password` char(64) NOT NULL,
  `idhash` varchar(32) DEFAULT NULL,
  `session` varchar(32) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `picture` varchar(255) NOT NULL DEFAULT \'/standard.png\',
  `time` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT 0,
  `hits` int(11) unsigned NOT NULL DEFAULT 0,
  `access` smallint(1) unsigned NOT NULL DEFAULT 0,
  `forgot` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

$lsdb->query('CREATE TABLE '.DB_PREFIX.'jrc_user_stats (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) unsigned NOT NULL,
  `vote` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comment` text,
  `support_time` int(10) unsigned NOT NULL DEFAULT 0,
  `time` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

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
	$errors .= 'Please insert a valid username (A-Z,a-z,0-9,-_).';
}

if (count($errors) == 0) {

// MySQL/i connection
if (DB_USER && DB_PASS) {
$lsdb = new ls_mysql(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$lsdb->set_charset("utf8");
}

// The new password encrypt with hash_hmac
$passcrypt = hash_hmac('sha256', $_POST['pass'], DB_PASS_HASH);
 
$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_user SET
	username = "'.smartsql($_POST['username']).'",
	password = "'.$passcrypt.'",
	email = "'.smartsql($_POST['email']).'",
	name = "'.smartsql($_POST['name']).'",
	time = NOW(),
	access = 1');

$lsdb->query('UPDATE '.DB_PREFIX.'jrc_setting SET value = "'.smartsql($_POST['email']).'" WHERE varname = "email"');

@$lsdb->query('ALTER DATABASE '.DB_NAME.' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
    
// Finally close all db connections
$lsdb->ls_close();

// confirm
include_once '../class/class.postmail.php';

$email_body = 'URL: '.FULL_SITE_DOMAIN.'<br />Email: '.$_POST['email'];

// Send the email to the customer
$mail = new PHPMailer(); // defaults to using php "mail()"
$body = str_ireplace("[\]", "", $email_body);
$mail->SetFrom($_POST['email']);
$mail->AddReplyTo($_POST['email']);
$mail->AddAddress('lic@livesupportrhino.com');
$mail->Subject = 'Install - Rhino Light 2.4';
$mail->AltBody = 'HTML Format';
$mail->MsgHTML($body);
$mail->Send();

echo '<div class="alert alert-info">Installation successful, please delete the <strong>install</strong> directory. You can now log in, in your <a href="../operator/">operator</a> panel.</div>';

$show_form = false;

} else {
   $errors = $errors;
} } 

if ($errors) echo '<div class="alert alert-error">'.$errors.'</div>';

if ($show_form) { ?>

<form name="user" method="post" action="install.php?step=3" enctype="multipart/form-data">
<table class="table table-striped">
<tr>
	<td>Name <span class="complete">*</span></td>
	<td><input type="text" value="" class="input-xlarge" name="name" placeholder="Name" /></td>
</tr>
<tr>
	<td>Username <span class="complete">*</span></td>
	<td><input type="text" value="" class="input-xlarge" name="username" placeholder="Username" /></td>
</tr>
<tr>
	<td>Password <span class="complete">*</span></td>
	<td><input type="text" value="" class="input-xlarge" name="pass" placeholder="Password" /></td>
</tr>
<tr>
	<td>Email <span class="complete">*</span></td>
	<td><input type="text" value="" class="input-xlarge" name="email" placeholder="Email" /></td>
</tr>
</table>

<div class="form-actions">
<button type="submit" name="user" class="btn btn-primary pull-right">Finish</button>
</div>

</form>
<?php } } if (!isset($_GET['step'])) { ?>
<div class="alert alert-info">
	Please read or watch the <a href="/install/Installation-English.html">installation manual/video</a> very carefully
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

<table class="table">
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
	<p>Copyright 2014 by <a href="http://www.livesupportrhino.com">Live Support Light - Rhino</a></p>
</footer>

</div>
</body>
</html>