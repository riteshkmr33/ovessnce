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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['convid']) && is_numeric($_SESSION['convid'])) {

	// convert post into defaults
	$defaults = $_POST;

	// check to see if conversation is to be stored
	$result = $lsdb->query('SELECT id, operatorid, name, email, hide FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($_SESSION['convid']).'"');
	$row = $result->fetch_assoc();
	
	if ($lsdb->affected_rows > 0) {
		
		if (!$row['hide']) {
		
			$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 0, ended = "'.time().'"  WHERE id = "'.$row['id'].'"');
			
			$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
			name = "'.smartsql($_SESSION['jrc_name']).'",
			message = "'.smartsql($tl['general']['g16']).'",
			user = "'.smartsql($_SESSION['jrc_userid']).'",
			convid = "'.$row['id'].'",
			time = NOW(),
			class = "ended"');
		
		}
		
		// Send the transcript
		if ($defaults['send_email'] && !empty($defaults['email']) && filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
		
			$transcript = $lsdb->query('SELECT * FROM '.DB_PREFIX.'transcript WHERE convid = "'.smartsql($row['id']).'"');
			
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
			$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET email = "'.smartsql($defaults['email']).'", name = "'.smartsql($defaults['name']).'" WHERE id = "'.$row['id'].'"');
			
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
				$mail->AddReplyTo(LS_EMAIL, LS_TITLE);
				$mail->AddAddress($defaults['email'], $defaults['name']);
				
			} else {
			
				$mail->SetFrom(LS_EMAIL, LS_TITLE);
				$mail->AddReplyTo(LS_EMAIL, LS_TITLE);
				$mail->AddAddress($defaults['email'], $defaults['name']);
			
			}
			
			$body = str_ireplace("[\]", "", $email_body);
			$mail->Subject = $tl['general']['g44'].' - '.LS_TITLE;
			$mail->AltBody = $tl['general']['g45'];
			$mail->MsgHTML($body);
			$mail->Send();
			
			}
		
		}
		
		// Insert the rating
		if (LS_CRATING) {
		
			// Now get the support time
			$result2 = $lsdb->query('SELECT initiated, ended FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($page1).'"');
			$row2 = $result2->fetch_assoc();
			
			$total_supporttime = $row2['ended'] - $row2['initiated'];
			
			// Write stuff into the user stats
			$lsdb->query('INSERT INTO '.DB_PREFIX.'user_stats SET
			userid = "'.smartsql($row["operatorid"]).'",
			vote = "'.smartsql($defaults["fbvote"]).'",
			name = "'.smartsql($_SESSION['jrc_name']).'",
			email = "'.smartsql($row["email"]).'",
			support_time = "'.$total_supporttime.'",
			time = NOW()');
		}

	unset($_SESSION['convid']);
	unset($_SESSION['jrc_userid']);
	unset($_SESSION['jrc_email']);
	unset($_SESSION['jrc_captcha']);
	
	}
}

if ($page1 == 1 && isset($_SESSION['convid'])) {

	// check to see if conversation is to be stored
	$result = $lsdb->query('SELECT id, operatorid, name, email, hide FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($_SESSION['convid']).'"');
	$row = $result->fetch_assoc();
	
	if ($lsdb->affected_rows > 0) {
		
		if (!$row['hide']) {
		
			$lsdb->query('UPDATE '.DB_PREFIX.'sessions SET status = 0, ended = "'.time().'"  WHERE id = "'.$row['id'].'"');
			
			$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
			name = "'.smartsql($_SESSION['jrc_name']).'",
			message = "'.smartsql($tl['general']['g16']).'",
			user = "'.smartsql($_SESSION['jrc_userid']).'",
			convid = "'.$row['id'].'",
			time = NOW(),
			class = "ended"');
		
		}
		
		// Now get the support time
		$result2 = $lsdb->query('SELECT initiated, ended FROM '.DB_PREFIX.'sessions WHERE id = "'.smartsql($page1).'"');
		$row2 = $result2->fetch_assoc();
		
		$total_supporttime = $row2['ended'] - $row2['initiated'];
		
		// Write stuff into the user stats
		$lsdb->query('INSERT INTO '.DB_PREFIX.'user_stats SET
		userid = "'.smartsql($row["operatorid"]).'",
		vote = 0,
		name = "'.smartsql($_SESSION['jrc_name']).'",
		email = "'.smartsql($row["email"]).'",
		support_time = "'.$total_supporttime.'",
		time = NOW()');
		
	}
	
	unset($_SESSION['convid']);
	unset($_SESSION['jrc_userid']);
	unset($_SESSION['jrc_email']);
	unset($_SESSION['jrc_captcha']);

	ls_redirect(html_entity_decode(LS_rewrite::lsParseurl('start', $page1, $page2, '', '')));

}

ob_flush();

?>

<script type="text/javascript">
	javascript:window.close();
</script>