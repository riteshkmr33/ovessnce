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

if (empty($_SESSION['jrc_userid'])) {
	ls_redirect(LS_rewrite::lsParseurl('start', '', '', '', ''));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_feedback'])) {
		$defaults = $_POST;
		
		// session
		$_SESSION['ls_fb_sent'] = -1;
		
		// Errors in Array
		$errors = array();
		
		if ($defaults['email'] != '' && !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
		    $errors['email'] = $tl['error']['e1'];
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
		
			if (is_numeric($defaults['convid'])) {
			
				// check to see if conversation is to be stored
				$result = $lsdb->query('SELECT convid, name, email, contact FROM '.DB_PREFIX.'jrc_sessions WHERE convid = "'.smartsql($defaults['convid']).'"');
				
				if ($lsdb->affected_rows > 0) {
				
					$row = $result->fetch_assoc();
			
					$lsdb->query('UPDATE '.DB_PREFIX.'jrc_sessions SET status = 0, ended = "'.time().'"  WHERE convid = "'.$row['convid'].'"');
					
					$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_transcript SET 
					name = "'.smartsql($_SESSION['jrc_name']).'",
					message = "'.smartsql($tl['general']['g16']).'",
					user = "'.smartsql($_SESSION['jrc_userid']).'",
					convid = "'.$row['convid'].'",
					time = NOW(),
					class = "notice"');
				
				}
			}
		
			$listform = $tl["general"]["g27"].': '.$defaults['name'].'<br />';
			if ($defaults['message']) {
				$listform .= $tl["general"]["g24"].': '.$defaults['message'].'<br />';
			} else {
				$listform .= $tl["general"]["g24"].': '.$tl["general"]["g12"].'<br />';
			}
			$listform .= $tl["general"]["g29"].': '.$defaults['fbvote'].'/5';
			
			$result1 = $lsdb->query('SELECT user FROM '.DB_PREFIX.'jrc_transcript WHERE convid = "'.smartsql($defaults['convid']).'" AND class = "admin" AND user != "" LIMIT 1');
			if ($lsdb->affected_rows > 0) {
				$row1 = $result1->fetch_assoc();
				$operator = explode("::", $row1['user']);
			} else {
				$operator = 0;
			}
			
			$name = filter_var($defaults['name'], FILTER_SANITIZE_STRING);
			$email = filter_var($defaults['email'], FILTER_SANITIZE_EMAIL);
			$message = filter_var($defaults['message'], FILTER_SANITIZE_STRING);
			
			// Now get the support time
			$result2 = $lsdb->query('SELECT initiated, ended FROM '.DB_PREFIX.'jrc_sessions WHERE convid = "'.smartsql($defaults['convid']).'"');
			$row2 = $result2->fetch_assoc();
			
			$total_supporttime = $row2['ended'] - $row2['initiated'];
			
			// Write stuff into the user stats
			$lsdb->query('INSERT INTO '.DB_PREFIX.'jrc_user_stats SET
			userid = "'.smartsql($operator[0]).'",
			vote = "'.smartsql($defaults['fbvote']).'",
			name = "'.smartsql($name).'",
			email = "'.smartsql($email).'",
			comment = "'.smartsql($message).'",
			support_time = "'.$total_supporttime.'",
			time = NOW()');
		
		
			$mail = new PHPMailer(); // defaults to using php "mail()"
			if ($email) {
				$mail->SetFrom($email, $name);
			} else { 
				$mail->SetFrom(LS_EMAIL, "no-reply");
			}
			$mail->AddAddress(LS_EMAIL, LS_TITLE);
			$mail->Subject = $tl["general"]["g24"];
			$mail->MsgHTML($listform);
			
			if ($mail->Send()) {
				
				// Ajax Request
				if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
					
					$thankyou = '<div class="alert alert-success">'.LS_THANKYOU_FEEDBACK.'</div>
					<div class="pull-center">
						<a href="javascript:window.close();" class="btn btn-primary">'.$tl["general"]["g3"].'</a>
					</div>';
				
					header('Cache-Control: no-cache');
					echo json_encode(array('status' => 1, 'html' => $thankyou));
					session_destroy();
					exit;
					
				} else {
				
					session_destroy();
				
			        $_SESSION['ls_fb_sent'] = 1;
			        ls_redirect($_SERVER['HTTP_REFERER']);
			    
			    }
			}
		}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $tl["general"]["g24"];?> - <?php echo LS_TITLE;?></title>
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
			<h3><img src="img/logo.png" alt="logo" /> <?php echo $tl["general"]["g24"];?> - <?php echo LS_TITLE;?></h3>
			<p><?php echo LS_FEEDBACK_MESSAGE;?></p>
		</div>
	</div>
	<hr class="soften">
	<div class="jrc_chat_form">
			
		<?php if ($errors) { ?>
			<div class="alert alert-error"><?php echo $errors["name"].$errors["email"];?></div>
			<?php } ?>
		
				
		<?php if ($_SESSION['ls_fb_sent'] == 1) { ?>
			
			<div class="alert alert-success"><?php echo LS_THANKYOU_FEEDBACK;?></div>
			<div class="pull-center">
				<a href="javascript:window.close();" class="btn btn-primary"><?php echo $tl["general"]["g3"];?></a>
			</div>
		
		<?php } else { ?>
		
		<div id="thank-you"></div>
				
		<!--- Chat Rating -->
		<form id="cSubmit" class="form-inline" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
		
					<div class="control-group">
					    <label class="control-label" for="vote5"><?php echo $tl["general"]["g23"];?></label>
					    <div class="controls">
							<div class="rate-result"><?php echo $tl["general"]["g33"];?> <span id="stars-cap"></span></div>
							<div id="starify">
								<label for="vote1"><input type="radio" name="fbvote" id="vote1" value="1" title="Poor" /> Poor</label>
								<label for="vote2"><input type="radio" name="fbvote" id="vote2" value="2" title="Fair" /> Fair</label>
								<label for="vote3"><input type="radio" name="fbvote" id="vote3" value="3" title="Average" checked="checked" /> Average</label>
								<label for="vote4"><input type="radio" name="fbvote" id="vote4" value="4" title="Good" /> Good</label>
								<label for="vote5"><input type="radio" name="fbvote" id="vote5" value="5" title="Excellent" /> Excellent</label>
							</div>
						</div>
					</div>
						
					<div class="control-group">
					    <label class="control-label" for="name"><?php echo $tl["general"]["g4"].$tl["general"]["g26"];?></label>
					    <div class="controls">
							<input type="text" name="name" id="name" class="input-large" placeholder="<?php echo $tl["general"]["g4"];?>" />
						</div>
					</div>
					
					<div class="control-group">
					    <label class="control-label" for="email"><?php echo $tl["general"]["g5"].$tl["general"]["g26"];?></label>
					    <div class="controls">
							<input type="text" name="email" id="email" class="input-large" placeholder="<?php echo $tl["general"]["g5"];?>" />
						</div>
					</div>
					
					<div class="control-group">
					    <label class="control-label" for="message"><?php echo $tl["general"]["g24"].$tl["general"]["g26"];?></label>
					    <div class="controls">
					    	<textarea name="message" id="message" rows="5" class="input-large"></textarea>
						</div>
					</div>
					
					<input type="hidden" name="send_feedback" value="1" />
					<input type="hidden" name="convid" value="<?php echo $page1;?>" />
					
					<div class="form-actions">
						<a href="<?php echo LS_rewrite::lsParseurl('stop', $_SESSION['jrc_convid'], '', '', '');?>" class="btn btn-danger"><?php echo $tl["general"]["g3"];?></a> <button type="submit" id="formsubmit" class="btn btn-primary pull-right"><?php echo $tl["general"]["g25"];?></button>
					</div>
				
			</form>
			
			</div>
			<?php } ?>
			
	<hr class="soften">
	<!-- Do not remove or modify the copyright without copyright free license http://www.livesupportrhino.com/shop/i/6/copyright-free -->	
	<footer><a href="http://www.livesupportrhino.com" target="_blank">Rhino Light</a></footer>
			
</div>
		
		<script type="text/javascript">
				$(function(){
					$("#starify").children().not(":input").hide();
					
					// Create stars from :radio boxes
					$("#starify").stars({
						cancelShow: false,
						captionEl: $("#stars-cap")
					});
				});
				
			$("#name").focus();
			ls.main_url = "<?php echo BASE_URL;?>";
			ls.lsrequest_uri = "<?php echo LS_PARSE_REQUEST;?>";
			ls.ls_submit = "<?php echo $tl['general']['g25'];?>";
			ls.ls_submitwait = "<?php echo $tl['general']['g8'];?>";
		</script>
</body>
</html>

<?php ob_flush(); ?>