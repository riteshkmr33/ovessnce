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

// start buffer
ob_start();

if (empty($_SESSION['jrc_userid']) || empty($_SESSION['convid']) || LS_base::lsCheckSession($_SESSION['jrc_userid'], $_SESSION['convid'])) {
	
	// Destroy Session
	unset($_SESSION['convid']);
	unset($_SESSION['jrc_userid']);
	unset($_SESSION['jrc_email']);
	
	ls_redirect(html_entity_decode(LS_rewrite::lsParseurl('start', $page1, $page2, '', '')));
}

if (LS_FEEDBACK) {

	$parseurl = LS_rewrite::lsParseurl('feedback', $page1, $page2, '', '');

} else {

	$parseurl = LS_rewrite::lsParseurl('stop', $page1, $page2, '', '');

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
	<link rel="stylesheet" href="css/print.css?=<?php echo LS_UPDATED;?>" type="text/css" media="print" />
	<link rel="stylesheet" href="css/dropzone.css?=<?php echo LS_UPDATED;?>" type="text/css" />
	<script type="text/javascript" src="js/jquery.js?=<?php echo LS_UPDATED;?>"></script>
	
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
		.jrc_chat_form, .sidebar {
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

<?php if ($page1 == 1) { include_once('template/slide_up/chat.php'); } else { include_once('template/pop_up/chat.php'); } ?>

<!-- Javascript Stuff necessary for Chat -->
<script type="text/javascript" src="js/functions.js?=<?php echo LS_UPDATED;?>"></script>
<script type="text/javascript" src="js/lsajax.js?=<?php echo LS_UPDATED;?>"></script>
<script type="text/javascript" src="js/dropzone.js?=<?php echo LS_UPDATED;?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		var jrc_lang = "<?php echo $_GET['lang'];?>";
		$("#print_transcript").click(function(e){e.preventDefault();$("#jrc_chat_output").printElement({pageTitle:'<?php echo addslashes($tl["general"]["g"]);?> - <?php echo addslashes(LS_TITLE);?>'})});<?php if ($page1 == 1) echo 'ls.ls_slide='.$page1;?>;
			
		//dropzone config
		Dropzone.options.cUploadDrop = {
		    dictResponseError: "SERVER ERROR",
		    paramName: "uploadpp", // The name that will be used to transfer the file
		    addRemoveLinks: true,
		    maxFilesize: 2,
		    maxFiles: 1,
		    acceptedFiles: "<?php echo LS_ALLOWED_FILES;?>",
		    url: "uploader/uploader.php",
		    init: function () {
		        this.on("complete", function (file) {
		          if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
		          	this.removeAllFiles();
		            loadchat = true;
		            scrollchat = true;
		            getInput();
		          }
		        });
		      }
		};
			
		<?php if (LS_CRATING && !LS_FEEDBACK) { ?>$(function(){$("#starify").children().not(":input").hide();$("#starify").stars({cancelShow:false,callback:function(ui,type,value){$("#rhino_update").html("").hide();$("#rhino_update").html('<div class="alert alert-success"><?php echo addslashes($tl["general"]["g43"]);?>'+value+'/5</div>').fadeIn().delay(3000).fadeOut(1000)}})});<?php } ?>
		
		});
		ls.socket_url = "<?php echo SOCKET_PROTOCOL;?>";
		ls.files_url = "<?php echo BASE_URL.LS_FILES_DIRECTORY?>";
</script>
		
</body>
</html>
<?php ob_flush(); ?>