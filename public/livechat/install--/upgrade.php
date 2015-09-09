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

if (!file_exists('../config.php')) die('[upgrade.php] config.php not found');
require_once '../config.php';

/* NO CHANGES FROM HERE */

// Set successfully to zero
$succesfully = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Upgrade from Pro to Rhino Socket</title>
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
          <a class="navbar-brand" href="http://www.livesupportrhino.com">Upgrade from Pro to Rhino Socket</a>
        </div>
      </div>
</div>

<div class="container">
<div class="row-fluid">
<div class="col-md-12">
<div class="jumbotron">
<h2>Upgrade from Pro to Rhino Socket</h2>
</div>

<?php 

$result = $lsdb->query('SELECT value FROM '.DB_PREFIX.'setting WHERE varname = "version"');
$row = $result->fetch_assoc();
if ($row["value"] == "2.0") { $succesfully = 1; ?>

<div class="alert alert-info">Your Rhino Socket is already upgraded!</div>

<!-- Plugin is not installed let's display the installation script -->
<?php } else { if (isset($_POST['upgrade']) && $_GET['step'] == 2) {

// confirm
$email_body = 'URL: '.BASE_URL.'<br />Email: '.LS_EMAIL.'<br />License: '.LS_O_NUMBER;

// Send the email to the customer
$mail = new PHPMailer(); // defaults to using php "mail()"
$body = str_ireplace("[\]", "", $email_body);
$mail->SetFrom(LS_EMAIL);
$mail->AddReplyTo(LS_EMAIL);
$mail->AddAddress('lic@livesupportrhino.com');
$mail->Subject = 'Upgrade - Rhino Socket 2.0';
$mail->AltBody = 'HTML Format';
$mail->MsgHTML($body);
$mail->Send();

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

$lsdb->query('ALTER TABLE '.DB_PREFIX.'sessions ADD `operatorname` varchar(255) NOT NULL AFTER `operatorid`, ADD `denied` smallint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `o_typing`, ADD `deniedoid` int(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `denied`, ADD `fcontact` smallint(1) unsigned NOT NULL DEFAULT 0 AFTER `status`, ADD `knockknock` smallint(1) unsigned NOT NULL DEFAULT 0 AFTER `fcontact`');

$lsdb->query('ALTER TABLE '.DB_PREFIX.'user ADD `busy` smallint(1) unsigned NOT NULL DEFAULT 0 AFTER `available`, ADD `tw_days` varchar(30) DEFAULT NULL AFTER `busy`, ADD `tw_time_from` time NOT NULL DEFAULT "00:00:00" AFTER `tw_days`, ADD `tw_time_to` time NOT NULL DEFAULT "00:00:00" AFTER `tw_time_from`, ADD `phonenumber` varchar(255) DEFAULT NULL AFTER `dnotify`, ADD `operatorchat` smallint(1) NOT NULL DEFAULT 0 AFTER `files`, ADD `ringing` smallint(2) NOT NULL DEFAULT 3 AFTER `sound`, ADD`emailnot` smallint(1) NOT NULL DEFAULT 0 AFTER `ringing`');

$lsdb->query("INSERT INTO ".DB_PREFIX."setting (`varname`, `groupname`, `value`, `defaultvalue`, `optioncode`, `datatype`, `product`) VALUES
('client_semail', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket'),
('client_question', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket'),
('client_squestion', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket'),
('emailcc', 'setting', '', '@rhinocc', 'input', 'free', 'rhinosocket'),
('facebook_big', 'setting', '', '', 'textarea', 'free', 'rhinosocket'),
('twitter_big', 'setting', '', '', 'textarea', 'free', 'rhinosocket'),
('tw_msg', 'setting', 'A customer is requesting attention.', 'A customer is requesting attention.', 'input', 'free', 'rhinosocket'),
('tw_phone', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('tw_sid', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('tw_token', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('smtp_mail', 'setting', 0, 0, 'yesno', 'boolean', 'rhinosocket'),
('smtpport', 'setting', 25, 25, 'input', 'number', 'rhinosocket'),
('smtphost', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('smtp_auth', 'setting', 0, 0, 'yesno', 'boolean', 'rhinosocket'),
('smtp_prefix', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('smtp_alive', 'setting', 0, 0, 'yesno', 'boolean', 'rhinosocket'),
('smtpusername', 'setting', '', '', 'input', 'free', 'rhinosocket'),
('smtppassword', 'setting', '', '', 'input', 'free', 'rhinosocket')");

$lsdb->query('ALTER TABLE '.DB_PREFIX.'sessions ADD `creferrer` VARCHAR(255) NULL AFTER `referrer`');
$lsdb->query('ALTER TABLE '.DB_PREFIX.'user ADD `operatorlist` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `operatorchat`');
$lsdb->query('ALTER TABLE '.DB_PREFIX.'buttonstats ADD `opid` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `depid`');

$lsdb->query('ALTER TABLE '.DB_PREFIX.'user ADD `permissions` VARCHAR(512) NULL AFTER `access`');

$lsdb->query('ALTER TABLE '.DB_PREFIX.'buttonstats ADD `sessionid` VARCHAR(64) NULL AFTER `session`');

$lsdb->query('UPDATE '.DB_PREFIX.'setting SET value = "'.time().'" WHERE varname = "updated"');

$lsdb->query('UPDATE '.DB_PREFIX.'setting SET value = "2.0" WHERE varname = "version"');

$lsdb->query('UPDATE '.DB_PREFIX.'setting SET defaultvalue = "Live Chat Socket upgrade" WHERE varname = "title"');

// Now let us delete the hook cache file
$cachehookfile = '../'.LS_CACHE_DIRECTORY.'/stuff.php';
if (file_exists($cachehookfile)) {
	unlink($cachehookfile);
}

// Now let us delete the define cache file
$cachedeffile = '../'.LS_CACHE_DIRECTORY.'/define.php';
if (file_exists($cachedeffile)) {
	unlink($cachedeffile);
}

// Now let us delete the define cache file
$cachedeffile = '../'.LS_CACHE_DIRECTORY.'/version.php';
if (file_exists($cachedeffile)) {
	unlink($cachedeffile);
}

$succesfully = 1;

?>
<div class="alert alert-success">Database upgrade successfully, please delete the <strong>install</strong> directory and then login into your <a href="../operator/">operator</a> panel.</div>
<?php } ?>

<?php if (!$succesfully) { ?>
<div class="alert alert-info">Please follow this steps carefully before you upgrade the database!</div>
<ul>
	<li>Backup all your files and your database.</li>
	<li>Upload all folders and files from the new version.</li>
	<li>Be sure to have a backup from your database before you upgrade!</li>
	<li>Do you have an up to date backup from your database? OK, hit "Upgrade Database".</li>
</ul>

<form name="company" method="post" action="upgrade.php?step=2" enctype="multipart/form-data">

<div class="form-actions">
<button type="submit" name="upgrade" class="btn btn-primary pull-right">Upgrade Database</button>
</div>

</form>
<?php } } ?>

</div>
</div>

<hr>

<footer>
	<p>Copyright 2014 by <a href="http://www.livesupportrhino.com">Live Chat Rhino - Socket</a></p>
</footer>

</div>

</div><!-- #container -->
</body>
</html>