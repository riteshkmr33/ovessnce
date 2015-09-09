<?php 

setcookie("activation", "visited", time() + LS_COOKIE_TIME, LS_COOKIE_PATH);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo LS_TITLE;?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Live Support Rhino" />
	<link rel="stylesheet" href="css/stylesheet.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
	
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
<body>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="jrc_chat_form_slide" style="text-align: center;">
				<p><?php echo $tl["general"]["g58"].LS_TITLE;?></p>
				<p><button class="btn btn-primary" type="button" onclick="window.close()"><?php echo $tl["general"]["g3"];?></button></p>
			</div>
		</div>
	</div>
</div>

<script>setTimeout(function(){window.close()},2000);</script>

</body>
</html>