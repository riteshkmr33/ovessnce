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

if (!file_exists('../../config.php')) {
    die('[loadmap.php] config.php not exist');
}
require_once '../../config.php';

if(!isset($_GET['ajax'])) {
	die("Nothing to see here");
}

// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

global $lsdb;

$sql = sprintf('SELECT id, countrycode, country, city, name, latitude, longitude FROM '.DB_PREFIX.'sessions WHERE longitude != "" ORDER BY id DESC LIMIT 50');
$result = $lsdb->query($sql);

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = $result->fetch_assoc()) {
	
	// Create the markers
  	$node = $dom->createElement("marker");
  	$newnode = $parnode->appendChild($node);
  	$newnode->setAttribute("country", $row['country']);
  	$newnode->setAttribute("city", $row['city']);
  	$newnode->setAttribute("countryflag", $row['countrycode']);
  	$newnode->setAttribute("username", $row['name']);
  	$newnode->setAttribute("lat", $row['latitude']);
  	$newnode->setAttribute("lng", $row['longitude']);
}

echo $dom->saveXML();

?>