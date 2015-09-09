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
if (!defined('LS_PREVENT_ACCESS')) die('You cannot access this file directly.');

// buffer flush
ob_start();

if (empty($_SESSION['jrc_userid'])) ls_redirect(LS_rewrite::lsParseurl('start', $page1, $page2, '', ''));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_feedback']) && is_numeric($_POST['convid'])) {
		$defaults = $_POST;
		
		// Errors in Array
		$errors = array();
		
		if ($defaults['send_email'] && !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
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
			
			// check to see if conversation is to be stored
			$result = $lsdb->query('SELECT id, department, operatorid, name, email, hide FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($defaults['convid']).'"');
			$row = $result->fetch_assoc();
			
			if ($lsdb->affected_rows > 0) {
				
				if ($row['hide'] == 0) {
				
					$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 0, ended = "'.time().'"  WHERE id = "'.$row['id'].'"');
					
					$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
					name = "'.smartsql($_SESSION['jrc_name']).'",
					message = "'.$tl['general']['g16'].'",
					user = "'.smartsql($_SESSION['jrc_userid']).'",
					convid = "'.$row['id'].'",
					time = NOW(),
					class = "ended"');
				
				}
				
				// Send the transcript
				if ($defaults['send_email'] && !empty($defaults['email']) && filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
				
					$transcript = $lsdb->query('SELECT * FROM '.DB_PREFIX.'transcript WHERE convid = "'.$row['id'].'" AND plevel = 1');
					
					if ($lsdb->affected_rows > 0) {
					
					$email_body = '<body style="margin:10px;">
					<div style="width:550px; font-family: \'Droid Serif\', Helvetica, Arial, sans-serif;">
					<table style="width:100%;margin:0;padding:0;font-size: 13px;" cellspacing="10" border="0">
					<tr>
					<td>
					<h1>'.LS_TITLE.'</h1>
					<p>'.$tl['general']['g66'].'</p>
					<div style="margin: 10px 0 10px 10px;
					border:1px solid #A8B9CB;
					height: 500px;
					overflow:auto;
					letter-spacing: normal;
					line-height: 1.5em;
					-moz-border-radius: 9px;
					-webkit-border-radius: 9px;
					border-radius: 9px;"><ul style="list-style: none;margin:0;padding:0;">';
					
						while ($rowt = $transcript->fetch_assoc()) {
						
							if ($rowt['class'] == "admin") {
								$css_chat = 'background-color:#effcff;
								padding:5px 5px 10px 5px;
								border-bottom:1px solid #c4dde1;';
							} elseif ($rowt['class'] == "download") {
								$css_chat = 'padding:10px 5px 10px 5px;
								background-color:#d0e5f9;
								background-image:url('.BASE_URL.'img/download.png);
								background-position:98% 50%;
								background-repeat:no-repeat;
								border-bottom:1px solid #c4dde1;';
							} elseif ($rowt['class'] == "notice") {
								$css_chat = 'padding:10px 5px 10px 5px;
								background-color:#d0e5f9;
								background-image:url('.BASE_URL.'img/notice.png);
								background-position:98% 50%;
								background-repeat:no-repeat;
								border-bottom:1px solid #c4dde1;';
							} else {
								$css_chat = 'background-color:#f4fdf1;
								padding:5px 5px 10px 5px;
								border-bottom:1px solid #c4dde1;';
							}
					
							$email_body .= '<li style="'.$css_chat.'"><span style="font-size:10px;color:#555;">'.date(LS_DATEFORMAT.LS_TIMEFORMAT, strtotime($rowt['time'])).' '.$rowt['name'].' '.$tl['general']['g14'].' :</span><br />'.stripcslashes($rowt['message']).'</li>';	
						}
						
					$email_body .= '</ul></div></td>
					</tr>
					</table>
					</div>
					</body>';
					
					// update session table to new email address:
					$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET email = "'.smartsql($defaults['email']).'" WHERE id = "'.$row['id'].'"');
					
					$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
					name = "'.smartsql($defaults['name']).'",
					message = "'.smartsql($tl['general']['g54']).'",
					user = "'.smartsql($_SESSION['jrc_userid']).'",
					convid = "'.$row['id'].'",
					time = NOW(),
					class = "notice"');
					
					$mail = new PHPMailer(); // defaults to using php "mail()" or optional SMTP
					
					if (LS_SMTP_MAIL) {
					
						$mail->IsSMTP(); // telling the class to use SMTP
						$mail->Host = LS_SMTPHOST;
						$mail->SMTPAuth = (LS_SMTP_AUTH ? true : false); // enable SMTP authentication
						$mail->SMTPSecure = LS_SMTP_PREFIX; // sets the prefix to the server
						$mail->SMTPKeepAlive = (LS_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
						$mail->Port = LS_SMTPPORT; // set the SMTP port for the GMAIL server
						$mail->Username = base64_decode(LS_SMTPUSERNAME); // SMTP account username
						$mail->Password = base64_decode(LS_SMTPPASSWORD); // SMTP account password
						$mail->SetFrom(LS_EMAIL, LS_TITLE);
						$mail->AddAddress($defaults['email'], $defaults['name']);
						
					} else {
					
						$mail->SetFrom(LS_EMAIL, LS_TITLE);
						$mail->AddAddress($defaults['email'], $defaults['name']);
					
					}
					
					$body = str_ireplace("[\]", "", $email_body);
					$mail->Subject = $tl['general']['g44'].' - '.LS_TITLE;
					$mail->AltBody = $tl['general']['g45'];
					$mail->MsgHTML($body);
					$mail->Send();
					
					}
				
				}
				
				// update session table to new email address:
				$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET name = "'.smartsql($defaults['name']).'" WHERE id = "'.$row['id'].'"');
				
				$email = filter_var($defaults['email'], FILTER_SANITIZE_EMAIL);
				$message = filter_var($defaults['message'], FILTER_SANITIZE_STRING);
				
				
				// Now get the support time
				$result2 = $lsdb->query('SELECT initiated, ended FROM '.DB_PREFIX.'sessions WHERE id = "'.$row['id'].'"');
				$row2 = $result2->fetch_assoc();
				
				$total_supporttime = $row2['ended'] - $row2['initiated'];
				
				// Write stuff into the user stats
				$lsdb->query('INSERT INTO '.DB_PREFIX.'user_stats SET
				userid = "'.smartsql($row["operatorid"]).'",
				vote = "'.smartsql($defaults["fbvote"]).'",
				name = "'.smartsql($_SESSION['jrc_name']).'",
				email = "'.smartsql($email).'",
				comment = "'.smartsql($message).'",
				support_time = "'.$total_supporttime.'",
				time = NOW()');
		
				$listform = $tl["general"]["g27"].': '.$defaults['name'].'<br />';
				if ($defaults['message']) {
					$listform .= $tl["general"]["g24"].': '.$message.'<br />';
				} else {
					$listform .= $tl["general"]["g24"].': '.$tl["general"]["g12"].'<br />';
				}
				$listform .= $tl["general"]["g29"].': '.$defaults['fbvote'].'/5';
				
				// Get the department for the contact form if set
				if (is_numeric($row["department"]) && $row["department"] != 0) {
				
					$op_email = LS_EMAIL;
					
					foreach ($LV_DEPARTMENTS as $d) {
					    if (in_array($row["department"], $d)) {
					        if ($d['email']) $op_email = $d['email'];
					    }
					}
					
				} else {
					$op_email = LS_EMAIL;
				}
			
				$mail = new PHPMailer(); // defaults to using php "mail()"
				if ($email) {
					$mail->AddReplyTo($email);
				}
				$mail->AddAddress($op_email);
				$mail->SetFrom(LS_EMAIL);
				$mail->Subject = $tl["general"]["g24"];
				$mail->MsgHTML($listform);
				
				if ($mail->Send()) {
				
					unset($_SESSION['convid']);
					unset($_SESSION['jrc_userid']);
					unset($_SESSION['jrc_email']);
					unset($_SESSION['jrc_captcha']);
					
					// Ajax Request
					if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
						
						header('Cache-Control: no-cache');
						die(json_encode(array('status' => 1, 'html' => $tl["general"]["g68"])));
						
					} else {
				        ls_redirect($_SERVER['HTTP_REFERER']);
				    }
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Live Chat Rhino" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="css/stylesheet.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
	<script type="text/javascript" src="js/contact.js"></script>
	
	<?php if (LS_FONTG_TPL != "NonGoogle") { ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo LS_FONTG_TPL;?>:regular,italic,bold,bolditalic" type="text/css" />
	<?php } ?>
	
	<style type="text/css">
		.navbar-brand { font-family:<?php if (LS_FONTG_TPL != "NonGoogle") echo '"'.str_replace("+", " ", LS_FONTG_TPL).'", '; echo LS_FONT_TPL;?>; }
	</style>
	<style id="cFontStyles" type="text/css">
		body, code, input[type="text"], textarea { font-family:<?php echo LS_FONT_TPL;?>; }
	</style>
	<?php if (LS_FHCOLOR_TPL != '#494949') { ?>
	<style type="text/css">
		.navbar-brand { color: <?php echo LS_FHCOLOR_TPL;?>; }
	</style>
	<?php } if (LS_FCOLOR_TPL != '#494949') { ?>
	<style type="text/css">
		body { color: <?php echo LS_FCOLOR_TPL;?>; }
	</style>
	<?php } if (LS_FACOLOR_TPL != '#2f942b') { ?>
	<style type="text/css">
		a { color: <?php echo LS_FACOLOR_TPL;?>; }
	</style>
	<?php } if (LS_ICCOLOR_TPL != '#f9f9f9') { ?>
	<style type="text/css">
		.jrc_chat_form {
			background-color: <?php echo LS_ICCOLOR_TPL;?> !important;
		}
	</style>
	<?php } ?>
	
	<?php if (!LS_LANGDIRECTION) { ?>
	<!-- RTL Support -->
	<style>body { direction: rtl; }</style>
	<!-- End RTL Support -->
	<?php } ?>
		
	<!--[if lt IE 9]>
	<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<script src="js/respond_ie.js"></script>
	 <![endif]-->
	 
</head>
<body>

<?php if ($page1 == 1) { include_once('template/slide_up/feedback.php'); } else { include_once('template/pop_up/feedback.php'); } ?>
		
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
			ls.socket_url = "<?php echo SOCKET_PROTOCOL;?>";
			ls.lsrequest_uri = "<?php echo LS_PARSE_REQUEST;?>";
			ls.ls_submit = "<?php echo $tl['general']['g25'];?>";
			ls.ls_submitwait = "<?php echo $tl['general']['g8'];?>";
		</script>
</body>
</html>

<?php ob_flush(); ?>