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

if (isset($_SESSION['jrc_userid']) && isset($_SESSION['convid'])) ls_redirect(html_entity_decode(LS_rewrite::lsParseurl('chat', $_GET['slide'], $_GET['lang'], '', '')));

// Get the department
if (is_numeric($_GET["dep"]) && $_GET["dep"] != 0) {
	$dep_direct = 1;
	foreach ($lv_departments as $d) {
	    if (in_array($_GET["dep"], $d)) {
	        $dep_direct = $_GET["dep"];
	    }
	}
} else {
	$dep_direct = 1;
}

// Operator ID if set.
$op_direct = 0;

if (is_numeric($_GET["oid"]) && $_GET["oid"] != 0) $op_direct = $_GET["oid"];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_chat'])) {
		$defaults = $_POST;
		
		// Errors in Array
		$errors = array();
		
		if (empty($defaults['message']) || strlen(trim($defaults['message'])) <= 2) {
		    $errors['message'] = $tl['error']['e2'];
		}
		
		if (LS_CAPTCHA) {
			$human_captcha = explode(':#:', $_SESSION['jrc_captcha']);
			
			if ($defaults[$human_captcha[0]] == '' || $defaults[$human_captcha[0]] != $human_captcha[1]) {
				$errors['human'] = $tl['error']['e12'];
			}
		}
				
		if (count($errors) > 0) {
			
			/* Outputtng the error messages */
			if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
			
				header('Cache-Control: no-cache');
				die('{"status":0, "errors":'.json_encode($errors).'}');
				
			} else {
			
				$errors = $errors;
			}
			
		} else {
			
			// Now let's check if the ip is ipv4
			if ($ipa && !is_bot()) {
				
				// Yes, it is
				$ipisvalid = 1;
				
			} else {
				
				// Nope, it isn't
				$ipisvalid = 0;
			}
			
			// if ip is valid do the whole thing
			if ($ipisvalid) {
			
				if (isset($_COOKIE['WIOgeoData'])) {
					// A "geoData" cookie has been previously set by the script, so we will use it
						
					// Always escape any user input, including cookies:
					list($city, $countryName, $countryAbbrev, $countryLat, $countryLong) = explode('|', strip_tags(base64_decode($_COOKIE['WIOgeoData'])));
						
				} else {
						
					// Making an API call to Hostip:
					$xml = @unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ipa));
						
					if ($xml) {
					
						// Get the city
						$city = $xml['geoplugin_city'];
						
						// Get the countryname		
						$countryName = $xml['geoplugin_countryName'];
						
						// Get the country code	
						$countryAbbrev = $xml['geoplugin_countryCode'];
						
						// Country Latitude
						$countryLong = $xml['geoplugin_longitude'];
						$countryLat = $xml['geoplugin_latitude'];
						
					} else {
						
						// try to get the stuff via xml
						$fxml = @jak_load_xml_from_curl('http://www.geoplugin.net/xml.gp?ip='.$ipa);
						
						// Parse the xml
						$xml = simplexml_load_string($fxml);
						
						// Get the city
						$city = $xml->geoplugin_city;
						
						// Get the countryname
						$countryName = $xml->geoplugin_countryName;
						
						// Get the country code
						$countryAbbrev = $xml->geoplugin_countryCode;
						
						// Country Latitude
						$countryLong = $xml->geoplugin_longitude;
						$countryLat = $xml->geoplugin_latitude;
					}
					
			}
				
				// This is a more accurate version but limited to 20 per hour, so if you exceed
				if (!$countryLat) {
				
					$ugeotags = @get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$ipa);
					
					if ($ugeotags) {
						$countryName = $ugeotags['country'];  // city name
						$city = $ugeotags['city'];
						$countryAbbrev = $ugeotags['iso2'];
						$countryLat = $ugeotags['latitude'];
						$countryLong = $ugeotags['longitude'];
					}
				}
			
				$countryName = str_replace('(Unknown Country?)', 'Unknown', $countryName);
				
				// In case API fails:
				if (!$countryName || $countryName == '(Private Address)' || $countryName == 'Unknown' || $countryName == '(unknown City?)') {					
					$countryName = 'Unknown';
					$countryAbbrev = 'xx';
					$city = 'Unknown';
				}
				
				// Setting a cookie with the data, which is set to expire in a week:
				setcookie('WIOgeoData', base64_encode($city.'|'.$countryName.'|'.$countryAbbrev.'|'.$countryLat.'|'.$countryLong), time()+3600*24*7, LS_COOKIE_PATH);
				
			}
			
			
			// create the guest account
			$salt = rand(100, 999);
			$userid = $tl['general']['g51'].$ipa.$salt;
			$_SESSION['jrc_name'] = filter_var($tl['general']['g51'], FILTER_SANITIZE_STRING);
			$_SESSION['jrc_userid'] = $userid;
			$_SESSION['jrc_email'] = $tl['general']['g12'];
			
			$resultref = $lsdb->query('SELECT referrer FROM '.DB_PREFIX.'buttonstats WHERE session = "'.smartsql($_SESSION['rlbid']).'" LIMIT 1');
			$rowref = $resultref->fetch_assoc();
			
			// add entry to sql
			$result = $lsdb->query('INSERT INTO '.DB_PREFIX.'sessions SET 
			userid = "'.smartsql($userid).'",
			department = "'.$dep_direct.'",
			operatorid = "'.smartsql($op_direct).'",
			name = "'.smartsql($_SESSION['jrc_name']).'",
			email = "'.smartsql($_SESSION['jrc_email']).'",
			phone = "'.smartsql(filter_var($defaults['phone'], FILTER_SANITIZE_NUMBER_INT)).'",
			ip = "'.smartsql($ipa).'",
			city = "'.smartsql($city).'",
			country = "'.smartsql(ucwords(strtolower($countryName))).'",
			countrycode = "'.smartsql(strtolower($countryAbbrev)).'",
			longitude = "'.$countryLong.'",
			latitude = "'.$countryLat.'",
			referrer = "'.smartsql($rowref['referrer']).'",
			initiated = "'.time().'",
			status = 1,
			session = "'.smartsql($_SESSION['rlbid']).'"'); 
						
			if ($result) {
				
				$cid = $lsdb->ls_last_id();
				
				$_SESSION['convid'] = $cid;
				
				$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
				name = "'.$tl["general"]["g56"].'",
				message = "'.$tl['general']['g63'].'",
				convid = "'.$cid.'",
				time = NOW(),
				class = "admin"');
				
				$lsdb->query('INSERT INTO '.DB_PREFIX.'transcript SET 
				name = "'.smartsql($_SESSION['jrc_name']).'",
				message = "'.smartsql($defaults['message']).'",
				convid = "'.$cid.'",
				time = NOW(),
				class = "user"');
				
				$lsdb->query('SELECT id FROM '.DB_PREFIX.'user WHERE access = 1 AND available = 1');
				
				if ($lsdb->affected_rows == 0) {
				
				// Now send email if whish so
				$result = $lsdb->query('SELECT username, email FROM '.DB_PREFIX.'user WHERE access = 1 AND available = 0 AND emailnot = 1 AND (departments = 0 OR FIND_IN_SET('.$dep_direct.', departments)) AND FIND_IN_SET("'.date("D").'", tw_days) AND (TIME(NOW()) >= tw_time_from AND TIME(NOW()) <= tw_time_to)');
				
				if ($lsdb->affected_rows > 0) {
				
					$row = $result->fetch_assoc();
				
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
						$mail->SetFrom($row['email'], $row['username']);
						$mail->AddAddress(LS_EMAIL, LS_TITLE);
						
					} else {
					
						$mail->SetFrom($row['email'], $row['username']);
						$mail->AddAddress(LS_EMAIL, LS_TITLE);
					
					}
					
					$mail->Subject = LS_TITLE;
					$mail->MsgHTML(LS_TW_MSG);
					$mail->Send();
				
				}
				
				}
				
				// Now send the message (sms) to the operator if wish so
				if (LS_TW_SID && LS_TW_TOKEN) {
				
					$lsdb->query('SELECT id FROM '.DB_PREFIX.'user WHERE access = 1 AND available = 1');
				
					if ($lsdb->affected_rows == 0) {
				
						$result = $lsdb->query('SELECT phonenumber FROM '.DB_PREFIX.'user WHERE access = 1 AND available = 0 AND phonenumber != "" AND (departments = 0 OR FIND_IN_SET('.$dep_direct.', departments)) AND FIND_IN_SET("'.date("D").'", tw_days) AND (TIME(NOW()) >= tw_time_from AND TIME(NOW()) <= tw_time_to)');
						
							if ($lsdb->affected_rows > 0) {
							
								if (LS_TWILIO_NEXMO) {
					
									require('include/twilio/Twilio.php');
									
									$client = new Services_Twilio(base64_decode(LS_TW_SID), base64_decode(LS_TW_TOKEN));
								
									while($row = $result->fetch_assoc()) {
									
										$message = $client->account->sms_messages->create(
										  LS_TW_PHONE, // From a valid Twilio number
										  $row['phonenumber'], // Text this number
										  LS_TW_MSG
										);
									
									}
								
								} else {
							
									include ('include/nexmo/NexmoMessage.php');
									
									while($row = $result->fetch_assoc()) {
									
										// Step 1: Declare new NexmoMessage. (Api Key) (Api Secret)
										$nexmo_sms = new NexmoMessage(base64_decode(LS_TW_SID), base64_decode(LS_TW_TOKEN));
										
										// Step 2: Use sendText( $to, $from, $message ) method to send a message. 
										$info = $nexmo_sms->sendText($row['phonenumber'], LS_TITLE, LS_TW_MSG);
									
									}
							
								}
							}
					}
				
				}
				
			}
			
			// Redirect page
			$gochat = LS_rewrite::lsParseurl('chat', $_POST['slide_chat'], $_POST['lang'], '', '');
			
			/* Outputtng the error messages */
			if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
			
				header('Cache-Control: no-cache');
				die(json_encode(array('login' => 1, 'link' => html_entity_decode($gochat))));
				
			}
			ls_redirect(html_entity_decode($gochat));
			
		}
}

// Get the message from the proactive
$proactivemsg = '';
$pror = $lsdb->query('SELECT message FROM '.DB_PREFIX.'buttonstats WHERE session = "'.smartsql($_SESSION['rlbid']).'" AND message != ""');

if ($lsdb->affected_rows > 0) {

	$rowpror = $pror->fetch_assoc();
	
	$proactivemsg = $rowpror['message'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $tl["general"]["g"];?> - <?php echo LS_TITLE;?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Live Chat Rhino" />
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
	 
	 <!-- Le fav and touch icons -->
	 <link rel="shortcut icon" href="img/ico/favicon.ico">
	 
</head>
<body<?php if (LS_BGCOLOR_TPL) echo ' style="background-color:'.LS_BGCOLOR_TPL.';"';?>>

<?php if ($_GET['slide'] == 1 || $page1 == 1 || $page2 == 1) { include_once('template/slide_up/quickstart.php'); } else { include_once('template/pop_up/quickstart.php'); } ?>
		
		<script type="text/javascript">
			<?php if (LS_CAPTCHA) { ?>
				$(document).ready(function()
				{
					$(".ls-ajaxform").append('<input type="hidden" name="<?php echo $random_name;?>" value="<?php echo $random_value;?>" />');
				});
			<?php } ?>
			$(document).ready(function(){
				$("#jrc_chat_output").css("background-image", "none");
				$("#message").fadeIn().focus();
				
				$('#message').keypress(function (e) {
				  if (e.which == 13) {
				  	$("#message").addClass("loadingbg");
				    $('form.ls-ajaxform').submit();
				  }
				});
			});
			ls.main_url = "<?php echo BASE_URL;?>";
			ls.socket_url = "<?php echo SOCKET_PROTOCOL;?>";
			ls.lsrequest_uri = "<?php echo LS_PARSE_REQUEST;?>";
			ls.ls_submit = "<?php echo $tl['general']['g10'];?>";
			ls.ls_submitwait = "<?php echo $tl['general']['g8'];?>";
		</script>
</body>
</html>
<?php ob_flush(); ?>