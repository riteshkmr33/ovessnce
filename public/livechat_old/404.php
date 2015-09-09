<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $tl["general"]["g2"];?> - <?php echo LS_TITLE;?></title>
	<meta charset="utf-8">
	<meta name="author" content="Live Support Rhino" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="css/stylesheet.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
	
	<!--[if lt IE 9]>
	<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	 <![endif]-->
	 
</head>
<body>

<div class="container">
	<div class="row">
		<div class="span12">
			<h3><img src="img/logo.png" alt="logo" /> <?php echo $tl["general"]["g2"];?> - <?php echo LS_TITLE;?></h3>
			<p><?php echo $tl["error"]["e5"];?>
			<ul>
				<li><?php echo $tl["error"]["e6"];?></li>
				<li><?php echo $tl["error"]["e7"];?></li>
				<li><?php echo $tl["error"]["e8"];?></li>
			</ul>
			</p>
			<p>
			<!-- live support rhino button --><a href="index.php?p=start&amp;lang=en" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('index.php?p=start&amp;lang=en', 'lsr', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=540,height=550,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="index.php?p=b&amp;i=blue&amp;lang=en" width="184" height="82" alt="" /></a><!-- end live support rhino button -->
			</p>
		</div>
	</div>
	<hr class="soften">
	
	<!-- Do not remove or modify the copyright without copyright free license http://www.livesupportrhino.com/shop/i/6/copyright-free -->
	<footer><a href="http://www.livesupportrhino.com" target="_blank">Live Support powered by Rhino</a></footer>
	
</div>
</body>
</html>