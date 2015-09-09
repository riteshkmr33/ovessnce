<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php if ($page) { ?><?php echo ucwords($page);?> - <?php } echo LS_TITLE;?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Live Chat Rhino" />
	<meta name="keywords" content="Your premium Live Chat from Rhino" />
	<meta name="author" content="Live Chat Rhino" />
	<?php if ($page == 'success' or $page == 'logout' or $page == '404' or $page == 'error') { ?>
	<meta http-equiv="refresh" content="1;URL=<?php if ($page == '404') { echo BASE_URL_ADMIN; } else { echo $_SERVER['HTTP_REFERER']; } ?>" />
	<?php } ?>
	<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="../css/stylesheet.css?=<?php echo LS_UPDATED;?>" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/screen.css?=<?php echo LS_UPDATED;?>" type="text/css" media="screen" />
	
	<?php if (!LS_LANGDIRECTION) { ?>
	<!-- RTL Support -->
	<style>body { direction: rtl; }</style>
	<!-- End RTL Support -->
	<?php } ?>
	
	<script type="text/javascript" src="../js/jquery.js?=<?php echo LS_UPDATED;?>"></script>
	
	<!--[if lt IE 9]>
		<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	 
</head>
<body>

<div class="navbar navbar-default">
	<div class="container">
    	<div class="navbar-header">
        	<a class="navbar-brand" href="<?php echo BASE_URL;?>"><?php echo LS_TITLE;?></a>
    	</div>
	</div>
</div>

<div class="container">
      <div class="row">
      	 	<div class="col-md-12">
      	 	
      	 	<div id="operator-chat"></div>
      	 	
      	 	<!-- Error MSG -->
      	 	<div id="msgErrorOC"></div>
      	 	
      	 	<form role="form" name="messageInputOC" id="MessageInputOC" action="javascript:sendInputOC();">
      	 	
      	 	<div class="form-group">
      	 	<label class="control-label" for="messageOC"><?php echo $tl["general"]["g135"];?></label>
      	 	<textarea name="messageOC" id="messageOC" class="form-control" rows="5"></textarea>
      	 	</div>
      	 	
      	 	<input type="hidden" name="userIDOC" id="userIDOC" value="<?php echo $lsuser->getVar("id");?>" />
      	 				
      	 	</form>

			</div><!--/span-->
		</div><!--/row-->

<hr>

<footer>Copyright <?=date('Y');?> by <a href="http://www.livesupportrhino.com">Live Chat Rhino</a><?php if (LS_SUPERADMINACCESS) echo ' ('.$tl['general']['g118'].LS_VERSION.')';?></footer>

<span id="audio_alert"></span>

<script type="text/javascript" src="js/public.chat.js"></script>

<script type="text/javascript">

	// set up auto refresh to pull new entries into chat window
	ls.intervalID = setInterval("getInputOC();", 1500);

</script>

</body>
</html>