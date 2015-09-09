<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $tl["general"]["g2"];?> - <?php echo LS_TITLE;?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Live Chat Rhino" />
	<link rel="stylesheet" href="css/stylesheet.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
	
	<?php if (LS_FONTG_TPL != "NonGoogle") { ?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo LS_FONTG_TPL;?>:regular,italic,bold,bolditalic" type="text/css" />
	<?php } ?>
	
	<style type="text/css">
		h1, h2, h3, h4, h5, h6 { font-family:<?php if (LS_FONTG_TPL != "NonGoogle") echo '"'.str_replace("+", " ", LS_FONTG_TPL).'", '; echo LS_FONT_TPL;?>; }
	</style>
	<style id="cFontStyles" type="text/css">
		body, code, input[type="text"], textarea { font-family:<?php echo LS_FONT_TPL;?>; }
	</style>
	<?php if (LS_FHCOLOR_TPL != '#494949') { ?>
	<style type="text/css">
		h1, h2, h3, h4, h5, h6 { color: <?php echo LS_FHCOLOR_TPL;?>; }
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

<div class="navbar navbar-default">
	<div class="container">
    	<div class="navbar-header">
        	<a class="navbar-brand" href="<?php echo $_SERVER['REQUEST_URI'];?>"><img src="img/logo.png" alt="logo" /> <?php echo $tl["general"]["g2"];?> - <?php echo LS_TITLE;?></a>
    	</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<p><?php echo $tl["error"]["e5"];?>
			<ul>
				<li><?php echo $tl["error"]["e6"];?></li>
				<li><?php echo $tl["error"]["e7"];?></li>
				<li><?php echo $tl["error"]["e8"];?></li>
			</ul>
			</p>
			<p>
			<!-- live support rhino button --><a href="index.php?p=start&amp;lang=en" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('index.php?p=start&amp;lang=en', 'lsr', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=780,height=550,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="index.php?p=b&amp;i=blue&amp;lang=en" width="184" height="82" alt="" /></a><!-- end live support rhino button -->
			</p>
		</div>
	</div>
	<hr>
	<!-- Do not remove or modify the copyright without copyright free license http://www.livesupportrhino.com/shop/i/6/copyright-free -->
	<footer><a href="http://www.livesupportrhino.com" target="_blank">Live Chat Rhino</a></footer>
	
</div>

</body>
</html>