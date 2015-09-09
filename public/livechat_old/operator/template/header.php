<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php if ($page) { ?><?php echo ucwords($page);?> - <?php } echo LS_TITLE;?></title>
	<meta charset="utf-8">
	<meta name="description" content="Live Support Rhino" />
	<meta name="keywords" content="Your premium Live Support from Rhino" />
	<meta name="author" content="Live Support Rhino" />
	<?php if ($page == 'success' or $page == 'logout' or $page == '404' or $page == 'error') { ?>
	<meta http-equiv="refresh" content="1;URL=<?php if ($page == '404') { echo BASE_URL_ADMIN; } else { echo $_SERVER['HTTP_REFERER']; } ?>" />
	<?php } ?>
	<link rel="shortcut icon" href="<?php echo BASE_URL_ORIG;?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="../css/stylesheet.css?=<?php echo LS_UPDATED;?>" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/screen.css?=<?php echo LS_UPDATED;?>" type="text/css" media="screen" />	
	<script type="text/javascript" src="../js/jquery.js?=<?php echo LS_UPDATED;?>"></script>
	<script type="text/javascript" src="../js/functions.js?=<?php echo LS_UPDATED;?>"></script>
	
	<!--[if lt IE 9]>
	<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	 <![endif]-->
	 
</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
        	<a class="btn btn-navbar" data-target=".nav-collapse" data-toggle="collapse">
        	<span class="icon-bar"></span>
        	<span class="icon-bar"></span>
        	<span class="icon-bar"></span>
        	</a>
        	<?php if (!$LS_PROVED) { ?>
          		<a class="brand" href="<?php echo BASE_URL;?>"><?php echo LS_TITLE;?></a>
          	<?php } ?>
          <div class="nav-collapse collapse">
          <?php if ($LS_PROVED) { ?>
            <p class="navbar-text pull-right">
              <?php echo $LS_WELCOME_NAME;?> <a class="navbar-link" href="index.php?p=logout" onclick="if(!confirm('<?php echo $tl["logout"]["l2"];?>'))return false;"><i class="icon-off icon-white"></i> <?php echo $tl["logout"]["l"];?></a>
            </p>

            <ul class="nav">
	        	<?php include_once APP_PATH.'operator/template/navbar.php';?>
            </ul>
          <?php } ?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<div class="container">
	<?php if ($LS_PROVED) { ?>
      <div class="row">
      	 	<div class="span12">
    <?php } ?>