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

if (!file_exists('../config.php')) die('[update.php] config.php not found');
require_once '../config.php';

/* NO CHANGES FROM HERE */

// Set successfully to zero
$succesfully = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Update Rhino Light</title>
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
        <div class="container-fluid">
          <a class="brand" href="http://www.livesupportrhino.com">Update Rhino Light</a>
        </div>
      </div>
</div>

<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<div class="hero-unit">
<h2>Update Rhino Light</h2>
</div>

<?php 

$result = $lsdb->query('SELECT value FROM '.DB_PREFIX.'jrc_setting WHERE varname = "version"');
$row = $result->fetch_assoc();
if ($row["value"] == "2.5") { $succesfully = 1; ?>

<div class="alert alert-info">Your Rhino Light is already up to date!</div>

<!-- Plugin is not installed let's display the installation script -->
<?php } else { if (isset($_POST['update']) && $_GET['step'] == 2) {

$lsdb->query('UPDATE '.DB_PREFIX.'jrc_setting SET value = "2.5" WHERE varname = "version"');

// confirm
$email_body = 'URL: '.BASE_URL.'<br />Email: '.LS_EMAIL;

// Send the email to the customer
$mail = new PHPMailer(); // defaults to using php "mail()"
$body = str_ireplace("[\]", "", $email_body);
$mail->SetFrom(LS_EMAIL);
$mail->AddReplyTo(LS_EMAIL);
$mail->AddAddress('lic@livesupportrhino.com');
$mail->Subject = 'Update - Rhino Light 2.5';
$mail->AltBody = 'HTML Format';
$mail->MsgHTML($body);
$mail->Send();

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
	<p>Copyright 2014 by <a href="http://www.livesupportrhino.com">Live Support Light - Rhino</a></p>
</footer>

</div>

</div><!-- #container -->
</body>
</html>