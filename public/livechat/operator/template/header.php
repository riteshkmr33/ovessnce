<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php if ($page) { ?><?php echo ucwords($page);?> - <?php } echo LS_TITLE;?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Live Support Rhino" />
	<meta name="keywords" content="Your premium Live Support from Rhino" />
	<meta name="author" content="Live Support Rhino" />
	<?php if ($page == 'success' or $page == 'logout' or $page == '404' or $page == 'error') { ?>
	<meta http-equiv="refresh" content="1;URL=<?php if ($page == '404') { echo BASE_URL_ADMIN; } else { echo $_SERVER['HTTP_REFERER']; } ?>" />
	<?php } ?>
	<link rel="stylesheet" href="../css/stylesheet.css?=<?php echo LS_UPDATED;?>" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/app.css?=<?php echo LS_UPDATED;?>" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/awesome/css/style.css?=<?php echo LS_UPDATED;?>" type="text/css" media="screen" />
	<link href="../css/dropzone.css?=<?php echo LS_UPDATED;?>" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="css/changer.css?=<?php echo LS_UPDATED;?>" />
	<link rel="stylesheet" type="text/css" href="css/minicolor.css?=<?php echo LS_UPDATED;?>" />
	
	<?php if (!LS_LANGDIRECTION) { ?>
	<!-- RTL Support -->
	<style>body { direction: rtl; }</style>
	<!-- End RTL Support -->
	<?php } ?>
	
	<script type="text/javascript" src="../js/jquery.js?=<?php echo LS_UPDATED;?>"></script>
	<script type="text/javascript" src="../js/functions.js?=<?php echo LS_UPDATED;?>"></script>
	
	<!-- Le fav and touch icons -->
	<link rel="shortcut icon" href="../img/ico/favicon.ico">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../img/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../img/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../img/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="../img/ico/apple-touch-icon-57-precomposed.png">
	 
</head>
<body>

<?php if ($LS_PROVED) { ?>

<section class="wrapper">

	<?php include_once APP_PATH.'operator/template/navbar.php';?>
	
<section>
<!-- START Page content-->
<section class="main-content">

<?php } ?>