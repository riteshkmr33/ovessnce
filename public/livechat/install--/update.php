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

if (!file_exists('../config.php')) die('[update.php] config.php not found');
require_once '../config.php';

/* NO CHANGES FROM HERE */

// Set successfully to zero
$succesfully = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Update Rhino Socket</title>
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
          <a class="navbar-brand" href="http://www.livesupportrhino.com">Update Rhino Socket</a>
        </div>
      </div>
</div>

<div class="container">
<div class="row-fluid">
<div class="col-md-12">
<div class="jumbotron">
<h2>Update Rhino Socket</h2>
</div>

<?php 

$result = $lsdb->query('SELECT value FROM '.DB_PREFIX.'setting WHERE varname = "version"');
$row = $result->fetch_assoc();
if ($row["value"] == "2.0") { $succesfully = 1; ?>

<div class="alert alert-info">Your Rhino Socket is already up to date!</div>

<!-- Plugin is not installed let's display the installation script -->
<?php } else { if (isset($_POST['update']) && $_GET['step'] == 2) {

if ($row["value"] == "1.0" || $row["value"] == "1.1") {

$lsdb->query('ALTER TABLE '.DB_PREFIX.'sessions ADD `fcontact` smallint(1) unsigned NOT NULL DEFAULT 0 AFTER `status`, ADD `knockknock` smallint(1) unsigned NOT NULL DEFAULT 0 AFTER `fcontact`');

$lsdb->query('ALTER TABLE '.DB_PREFIX.'user ADD `logins` int(11) unsigned NOT NULL DEFAULT 0 AFTER `hits`');

}

if ($row["value"] == "1.0" || $row["value"] == "1.1" || $row["value"] == "1.2") {

$lsdb->query('ALTER TABLE '.DB_PREFIX.'sessions ADD `creferrer` VARCHAR(255) NULL AFTER `referrer`');
$lsdb->query('ALTER TABLE '.DB_PREFIX.'user ADD `operatorlist` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `operatorchat`');
$lsdb->query('ALTER TABLE '.DB_PREFIX.'buttonstats ADD `opid` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `depid`');

}

$lsdb->query("INSERT INTO ".DB_PREFIX."setting (`varname`, `groupname`, `value`, `defaultvalue`, `optioncode`, `datatype`, `product`) VALUES
('client_semail', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket'),
('client_question', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket'),
('client_squestion', 'setting', '1', '0', 'yesno', 'boolean', 'rhinosocket')");

$lsdb->query('ALTER TABLE '.DB_PREFIX.'buttonstats ADD `sessionid` VARCHAR(64) NULL AFTER `session`');

$lsdb->query('ALTER TABLE '.DB_PREFIX.'user ADD `permissions` VARCHAR(512) NULL AFTER `access`');

// confirm
$email_body = 'URL: '.BASE_URL.'<br />Email: '.LS_EMAIL.'<br />License: '.LS_O_NUMBER;

// Send the email to the customer
$mail = new PHPMailer(); // defaults to using php "mail()"
$body = str_ireplace("[\]", "", $email_body);
$mail->SetFrom(LS_EMAIL);
$mail->AddReplyTo(LS_EMAIL);
$mail->AddAddress('lic@livesupportrhino.com');
$mail->Subject = 'Update - Rhino Socket 2.0';
$mail->AltBody = 'HTML Format';
$mail->MsgHTML($body);
$mail->Send();

$lsdb->query('UPDATE '.DB_PREFIX.'setting SET value = "'.time().'" WHERE varname = "updated"');

$lsdb->query('UPDATE '.DB_PREFIX.'setting SET value = "2.0" WHERE varname = "version"');

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
<div class="alert alert-success">Database update successfully, please delete the <strong>install</strong> directory and then login into your <a href="../operator/">operator</a> panel.</div>
<?php } ?>

<?php if (!$succesfully) { ?>
<div class="alert alert-info">Please follow this steps carefully before you update the database!</div>
<ul>
	<li>Backup all your files and your database.</li>
	<li>Upload all folders and files from the new version.</li>
	<li>Be sure to have a backup from your database before you update!</li>
	<li>Do you have an up to date backup from your database? OK, hit "Update Database".</li>
</ul>

<form name="company" method="post" action="update.php?step=2" enctype="multipart/form-data">

<div class="form-actions">
<button type="submit" name="update" class="btn btn-primary pull-right">Update Database</button>
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