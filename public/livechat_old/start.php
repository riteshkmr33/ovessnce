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

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('LS_PREVENT_ACCESS')) die('You cannot access this file directly.');

// buffer flush
ob_start();

// Start the session
session_start();

if (isset($_SESSION['jrc_userid']) && isset($_SESSION['jrc_convid'])) {
	ls_redirect(LS_rewrite::lsParseurl('chat', '', '', '', ''));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_chat'])) {
		$defaults = $_POST;
		
		// Errors in Array
		$errors = array();
		
		if (empty($defaults['name']) || strlen(trim($defaults['name'])) <= 2) {
		    $errors['name'] = $tl['error']['e'];
		}
		
		if (!empty($defaults['email']) && !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
		    $errors['email'] = $tl['error']['e1'];
		}
		
		if (LS_CAPTCHACHAT) {
			if ($defaults['human'] == '' || md5($defaults['human']) != $_SESSION['JAK_HUMAN_IMAGE']) {
				$errors['human'] = $tl['error']['e9'];
			}
		}
				
		if (count($errors) > 0) {
			
			/* Outputtng the error messages */
			if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
			
				header('Cache-Control: no-cache');
				echo '{"status":0, "errors":'.json_encode($errors).'}';
				exit;
				
			} else {
			
				$errors = $errors;
			}
			
		} else {
			
			$ipa = get_ip_address();
			$salt = rand(100, 999);
			$userid = $defaults['name'].$ipa.$salt;
			$_SESSION['jrc_name'] = filter_var($defaults['name'], FILTER_SANITIZE_STRING);
			$_SESSION['jrc_userid'] = $userid;
			
			if(!empty($defaults['email'])) {
				$_SESSION['guest_email'] = filter_var($defaults['email'], FILTER_SANITIZE_EMAIL);
			} else {
				$_SESSION['guest_email'] = $tl['general']['g12'];
			}
				if(isset($defaults['contactme'])) {
					$contactme = 1;
				} else {
					$contactme = 0;
				}
			
			// add entry to sql
			$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_sessions SET 
			userid = "'.smartsql($userid).'",
			name = "'.smartsql($_SESSION['jrc_name']).'",
			email = "'.smartsql($_SESSION['guest_email']).'",
			initiated = "'.time().'",
			status = 1,
			contact = '.$contactme); 
						
			if ($result) {
				
				$cid = $lsdb->ls_last_id();
				
				$_SESSION['jrc_convid'] = $cid;
				
				$lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET convid = "'.$cid.'" WHERE userid = "'.smartsql($_SESSION['jrc_userid']).'"');
				
				$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET 
				name = "Admin",
				message = "'.smartsql(LS_WELCOME_MESSAGE).'",
				convid = "'.$cid.'",
				time = NOW(),
				class = "admin"');
				
			}
			
			// Redirect page
			$gochat = LS_rewrite::lsParseurl('chat', '', '', '', '');
			
			/* Outputtng the error messages */
			if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
			
				header('Cache-Control: no-cache');
				echo json_encode(array('login' => 1, 'link' => $gochat));
				exit;
				
			}
			
			ls_redirect($gochat);
			
		}
}	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $tl["general"]["g"];?> - <?php echo LS_TITLE;?></title>
	<meta charset="utf-8">
	<meta name="author" content="Live Support Rhino" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="css/stylesheet.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
	<script type="text/javascript" src="js/contact.js"></script>
	
	<!--[if lt IE 9]>
	<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	 <![endif]-->
	 
</head>
<body>

<div class="container">
	<div class="row">
		<div class="span12">
			<h3><img src="img/logo.png" alt="logo" /> <?php echo $tl["general"]["g"];?> - <?php echo LS_TITLE;?></h3>
			<p><?php echo LS_LOGIN_MESSAGE;?></p>
		</div>
	</div>
	<hr class="soften">
	<div class="jrc_chat_form">
		
		<?php if ($errors) { ?>
		<div class="alert alert-error"><?php echo $errors["name"].$errors["email"];?></div>
		<?php } ?>
		
		<form id="cSubmit" class="form-inline" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" >
		
			<div class="control-group">
			    <label class="control-label" for="name"><?php echo $tl["general"]["g4"];?></label>
			    <div class="controls">
					<input type="text" name="name" id="name" class="input-large" placeholder="<?php echo $tl["general"]["g4"];?>" />
				</div>
			</div>
			
			<div class="control-group">
			    <label class="control-label" for="email"><?php echo $tl["general"]["g5"];?></label>
			    <div class="controls">
					<input type="text" name="email" id="email" class="input-large" placeholder="<?php echo $tl["general"]["g5"];?>" />
				</div>
			</div>
				
			<?php if (LS_CAPTCHACHAT) { ?>
			<div class="control-group">
				<label class="control-label"><?php echo $tl["general"]["g30"];?></label>
				<div class="controls captcha_wrapper">
					<img src="include/recaptcha/jak.human.php" alt="captcha" id="captcha" />
					<img src="img/refresh.png" alt="captcha_refresh" id="captcha_refresh" title="<?php echo $tl['general']['g31'];?>" />
				</div>
			</div>
			
			<div class="control-group">
			    <label class="control-label" for="human"><?php echo $tl["general"]["g32"];?></label>
			    <div class="controls">
					<input type="text" name="human" id="human" class="input-small" />
				</div>
			</div>
			<?php } ?>
			
			<label class="checkbox">
			      <input type="checkbox" name="contactme" id="contactme"> <?php echo $tl["general"]["g9"];?>
			</label>
				
			<div class="form-actions">
				<button type="submit" id="formsubmit" class="btn btn-primary pull-right"><?php echo $tl["general"]["g10"];?></button>
			</div>
			
			<input type="hidden" name="start_chat" value="1" />
			
		</form>
	</div>
	
	<hr class="soften">
	
	<!-- Do not remove or modify the copyright without copyright free license http://www.livesupportrhino.com/shop/i/6/copyright-free -->
	<footer><a href="#;" target="_blank">OVESCENCE</a> <?php echo BASE_URL;?></footer>
	
</div>
		
		
		<script type="text/javascript">
			<?php if (LS_CAPTCHACHAT) { ?>
				$(document).ready(function()
				{
					$("img#captcha_refresh").click(function() {
						$("#captcha").attr('src', 'include/recaptcha/jak.human.php?_rnd=' + Math.random());
						return false;
					});		
				});
			<?php } ?>
			
			$("#name").focus();
			ls.main_url = "<?php echo BASE_URL;?>";
			//ls.main_url = "http://localhost/test/livechat/";
			ls.lsrequest_uri = "<?php echo LS_PARSE_REQUEST;?>";
			ls.ls_submit = "<?php echo $tl['general']['g10'];?>";
			ls.ls_submitwait = "<?php echo $tl['general']['g8'];?>";
		</script>
</body>
</html>
<?php ob_flush(); ?>