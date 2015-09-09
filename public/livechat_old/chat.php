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

ob_start();

// Start the session
session_start();

if (empty($_SESSION['jrc_userid']) || empty($_SESSION['jrc_convid']) || LS_base::lsCheckSession($_SESSION['jrc_userid'], $_SESSION['jrc_convid'])) {
	
	// Destroy Session
	session_destroy();
	
	ls_redirect(LS_rewrite::lsParseurl('start', '', '', '', ''));
}

if (LS_FEEDBACK) {

	$parseurl = LS_rewrite::lsParseurl('feedback', $_SESSION['jrc_convid'], '', '', '');

} else {

	$parseurl = LS_rewrite::lsParseurl('stop', $_SESSION['jrc_convid'], '', '', '');

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
	<script type="text/javascript" src="js/lsajax.js"></script>
	
	<!--[if lt IE 9]>
	<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	 <![endif]-->
	 
	 <script type="text/javascript">
	 $(document).ready(function(){
	 		var intervalID = setInterval("jrc_getInput();", 3000);
	 		jrc_getInput();
	 		setChecker('<?php echo $_SESSION['jrc_userid'];?>');
	 		setInterval("setChecker('<?php echo $_SESSION['jrc_userid'];?>');", 10000);
	 });
	 	
	 	ls.ls_submit = "<?php echo $tl['general']['g22'];?>";
	 </script>
	 
</head>
<body>

<div class="container">
	<div class="row">
		<div class="span12">
			<h3><img src="img/logo.png" alt="logo" /> <?php echo $tl["general"]["g"];?> - <?php echo LS_TITLE;?></h3>
		</div>
	</div>
	<hr class="soften">
	<div class="jrc_chat_form">
				
		<!--- Chat output -->
		<div id="jrc_chat_output"></div>
		
		<!-- Client Input -->
		<form action="javascript:jrc_sendInput();" name="messageInput" id="MessageInput" class="form-inline">
			
			<div id="msgError" class="alert alert-error"></div>
			
			<div id="jrc_typing"><?php echo $tl["general"]["g35"];?></div>
			
			<div class="control">
			<textarea name="message" id="message" class="chatme"></textarea>
			</div>
			
			<input type="hidden" name="userID" id="userID" value="<?php echo $_SESSION['jrc_userid'];?>" />
			<input type="hidden" name="userName" id="userName" value="<?php echo $_SESSION['jrc_name'];?>" />
			<input type="hidden" name="convID" id="convID" value="<?php echo $_SESSION['jrc_convid'];?>" />
			
		</form>
		
		<div class="pull-center">
			<a href="<?php echo $parseurl;?>" class="btn btn-danger"><?php echo $tl["general"]["g15"];?></a>
		</div>
		
	</div>
			
	<hr class="soften">
	<!-- Do not remove or modify the copyright without copyright free license http://www.livesupportrhino.com/shop/i/6/copyright-free -->	
	<footer><a href="http://www.livesupportrhino.com" target="_blank">Rhino Light</a></footer>
			
</div>
</body>
</html>
<?php ob_flush(); ?>