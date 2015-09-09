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

// Start the session
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_email'])) {
		$defaults = $_POST;
		
		$_SESSION['ls_msg_sent'] = -1;
		
		// Errors in Array
		$errors = array();
		
		if (empty($defaults['name']) || strlen(trim($defaults['name'])) <= 2) {
		    $errors['name'] = $tl['error']['e'];
		}
		
		if ($defaults['email'] == '' || !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
		    $errors['email'] = $tl['error']['e1'];
		}
		
		if (empty($defaults['message']) || strlen(trim($defaults['message'])) <= 2) {
		    $errors['message'] = $tl['error']['e2'];
		}
		
		if (LS_CAPTCHA) {
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
		
			$listform = $tl["general"]["g27"].': '.$defaults['name'].'<br />';
			$listform .= $tl["general"]["g34"].': '.$defaults['email'].'<br />';
			$listform .= $tl["general"]["g28"].': '.$defaults['message'];
		
		
			$mail = new PHPMailer(); // defaults to using php "mail()"
			$mail->AddReplyTo($defaults['email'], $defaults['name']);
			$mail->SetFrom(LS_EMAIL, LS_TITLE);
			$mail->AddAddress(LS_EMAIL, LS_TITLE);
			$mail->Subject = LS_TITLE;
			$mail->MsgHTML($listform);
			
			if ($mail->Send()) {
				
				// Ajax Request
				if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
					
					$_SESSION['ls_msg_sent'] = 1;
					
					$thankyou = '<div class="alert alert-success">'.LS_THANKYOU_MESSAGE.'</div>
					<div class="pull-center">
						<a href="javascript:window.close();" class="btn btn-primary">'.$tl["general"]["g3"].'</a>
					</div>';
				
					header('Cache-Control: no-cache');
					echo json_encode(array('status' => 1, 'html' => $thankyou));
					exit;
					
				} else {
				
			        $_SESSION['ls_msg_sent'] = 1;
			        ls_redirect($_SERVER['HTTP_REFERER']);
			    
			    }
			}
		}
}	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $tl["general"]["g1"];?> - <?php echo LS_TITLE;?></title>
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
			<h3><img src="img/logo.png" alt="logo" /> <?php echo $tl["general"]["g1"];?> - <?php echo LS_TITLE;?></h3>
			<p><?php echo LS_OFFLINE_MESSAGE;?></p>
		</div>
	</div>
	<hr class="soften">
	<div class="jrc_chat_form">

		<?php if ($errors) { ?>
		<div class="alert alert-error"><?php echo $errors["name"].$errors["email"].$errors["message"];?></div>
		<?php } ?>
		
				
		<?php if ($_SESSION['ls_msg_sent'] == 1) { ?>
			<div class="alert alert-success"><?php echo LS_THANKYOU_MESSAGE;?></div>
			<div class="pull-center">
				<a href="javascript:window.close();" class="btn btn-primary"><?php echo $tl["general"]["g3"];?></a>
			</div>
		
		<?php } ?>
		
		<div id="thank-you"></div>
			
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
			
			<div class="control-group">
			    <label class="control-label" for="message"><?php echo $tl["general"]["g6"];?></label>
			    <div class="controls">
			    	<textarea name="message" id="message" rows="5" class="input-large"></textarea>
				</div>
			</div>
				
			<?php if (LS_CAPTCHA) { ?>
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
				
				<div class="form-actions">
					<button type="submit" id="formsubmit" class="btn btn-primary pull-right"><?php echo $tl["general"]["g7"];?></button>
				</div>
				
				<input type="hidden" name="send_email" value="1" />
				
			</form>
		</div>
			
	<hr class="soften">
	<!-- Do not remove or modify the copyright without copyright free license http://www.livesupportrhino.com/shop/i/6/copyright-free -->
	<footer><a href="http://www.livesupportrhino.com" target="_blank">Rhino Light</a></footer>
			
</div>
		
		<script type="text/javascript">
			<?php if (LS_CAPTCHA) { ?>
				$(document).ready(function()
				{
					$("img#captcha_refresh").click(function() {
						$("#captcha").attr('src', 'include/recaptcha/jak.human.php?_rnd=' + Math.random());
						return false;
					});		
				});
			<?php } ?>
			
			ls.main_url = "<?php echo BASE_URL;?>";
			ls.lsrequest_uri = "<?php echo LS_PARSE_REQUEST;?>";
			ls.ls_submit = "<?php echo $tl['general']['g7'];?>";
			ls.ls_submitwait = "<?php echo $tl['general']['g8'];?>";
		</script>
</body>
</html>