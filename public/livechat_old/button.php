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

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";

if ($referer) {

$image = verifyparam(isset($_GET['image']) ? "image" : "i", "/^\w+$/", "blue");
$lang = verifyparam(isset($_GET['language']) ? "language" : "lang", "/^[\w-]{2,5}$/", "");
if(!$lang || !folder_lang_button($lang)) {
	$lang = 'en';
}

$image_postfix = online_operators() ? "on" : "off";
$filename = "./img/buttons/".$lang."/".$image."_".$image_postfix.".png";

ob_clean();
$fp = fopen($filename, 'rb') or die("no image");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: image/png");
header("Content-Length: ".filesize($filename));
if(function_exists('fpassthru')){
	@fpassthru($fp);
} else {
	while ((!feof($fp)) && (connection_status()==0)) {
		print(fread($fp, 1024*8));
		flush();
	}
	fclose($fp);
}
exit;
}
?>