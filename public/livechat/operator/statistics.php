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
if (!defined('LS_ADMIN_PREVENT_ACCESS')) die('You cannot access this file directly.');

// Check if the user has access to this file
if (!ls_get_access("statistic", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) ls_redirect(BASE_URL);

// All the tables we need for this plugin
$errors = array();
$lstable = DB_PREFIX.'sessions';
$lstable1 = DB_PREFIX.'user_stats';
$lstable2 = DB_PREFIX.'user';
$lstable3 = DB_PREFIX.'departments';

if (ls_get_access("statistic_all", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)) {

	$country_sql = "";
	$support_sql = "";
	$feedback_sql = "";
	$button_sql = "";
	
	$result = $lsdb->query('SELECT id, title FROM '.$lstable3.' ORDER BY dorder ASC');
	while ($row = $result->fetch_assoc()) {
		// collect each record into $_data
	    $LS_DEPARTMENTS[] = $row;
	}
	
} else {
	
	$country_sql = ' operatorid = "'.LS_USERID_RHINO.'" AND';
	$support_sql = ' operatorid = "'.LS_USERID_RHINO.'" AND';
	$feedback_sql = ' userid = "'.LS_USERID_RHINO.'" AND';
	$button_sql = ' WHERE opid = "'.LS_USERID_RHINO.'"';
}

$_SESSION["stat_start_date"] = '';
$_SESSION["stat_end_date"] = date("Y-m-d");

// Get the time into a session
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($_POST["start_date"]) {

	$start_date = smartsql($_POST["start_date"]);
	$end_date = smartsql($_POST["end_date"]);
	
	$country_sql = " (initiated BETWEEN UNIX_TIMESTAMP('".$start_date."') AND UNIX_TIMESTAMP('".$end_date."')) AND";
	$support_sql = " (t1.initiated BETWEEN UNIX_TIMESTAMP('".$start_date."') AND UNIX_TIMESTAMP('".$end_date."')) AND";
	$feedback_sql = " (UNIX_TIMESTAMP(t1.time) BETWEEN UNIX_TIMESTAMP('".$start_date."') AND UNIX_TIMESTAMP('".$end_date."')) AND";
	$button_sql = $button_sql ? $button_sql." AND (UNIX_TIMESTAMP(time) BETWEEN UNIX_TIMESTAMP('".$start_date."') AND UNIX_TIMESTAMP('".$end_date."'))" : " WHERE (UNIX_TIMESTAMP(time) BETWEEN UNIX_TIMESTAMP('".$start_date."') AND UNIX_TIMESTAMP('".$end_date."'))";
	
	$_SESSION["stat_start_date"] = $start_date;
	$_SESSION["stat_end_date"] = $end_date;
	
	}
	
	if ($_POST["jak_depid"]) {
	
		$department = smartsql($_POST["jak_depid"]);
	
		$country_sql = $country_sql.' department = "'.$department.'" AND';
		$support_sql = $support_sql.' t1.department = "'.$department.'" AND';
		$button_sql = $button_sql ? $button_sql.' AND depid = "'.$department.'"' : ' WHERE depid = "'.$department.'"';
	
	}

}
		 
		 // Get the country list
		 $result = $lsdb->query('SELECT COUNT(id) AS total_country, countrycode, country FROM '.$lstable.' WHERE'.$country_sql.' countrycode != "" GROUP BY countrycode ORDER BY total_country DESC LIMIT 10');
		 
		 // Iterate through the rows
		 while ($row = $result->fetch_assoc()) {
		 	
		 	$arraydata = "['".$row['country']."', ".$row['total_country']."]";	
		 	
		 	$lsdata[] = $arraydata;
		 }
		 
		 // Get the support statistic
		 $resultst1 = $lsdb->query('SELECT SUM(t1.ended - t1.initiated) AS total_support, t2.username FROM '.$lstable.' AS t1 LEFT JOIN '.$lstable2.' AS t2 ON(t1.operatorid = t2.id) WHERE'.$support_sql.' ended > 0 AND t1.operatorid != 0 GROUP BY operatorid ORDER BY operatorid ASC LIMIT 20');
		 if ($lsdb->affected_rows > 0) {
		 while ($rowst1 = $resultst1->fetch_assoc()) {
		         
		         // get the operators in one table
		         $arrayoperator[] = array('operator' => $rowst1['username'], 'total_support' => $rowst1['total_support']);
		         
		     }
		 }
		 
		 // Get the feedback statistic
		 $resultst4 = $lsdb->query('SELECT COUNT(t1.id) AS total_id, SUM(t1.vote) AS total_vote, t2.username FROM '.$lstable1.' AS t1 LEFT JOIN '.$lstable2.' AS t2 ON(t1.userid = t2.id) WHERE'.$feedback_sql.' t1.userid != 0 GROUP BY t1.userid ORDER BY t1.userid DESC LIMIT 20');
		 if ($lsdb->affected_rows > 0) {
		 while ($rowst4 = $resultst4->fetch_assoc()) {
		         // collect each record into $_data
		         
		         $average_vote = round(($rowst4['total_vote'] / $rowst4['total_id']), 2);
		         
		         // get the operators in one table
		         $arrayfeedback[] = array('operator' => $rowst4['username'], 'vote' => $average_vote);
		         
		     }
		 }
		 
		 // Get the button statistic
		 $resultst3 = $lsdb->query('SELECT SUM(hits) AS total_hits, referrer FROM '.DB_PREFIX.'buttonstats'.$button_sql.' GROUP BY referrer, depid ORDER BY total_hits DESC LIMIT 10');
		 if ($lsdb->affected_rows > 0) {
		 while ($rowst3 = $resultst3->fetch_assoc()) {
		         // collect each record into $_data
		         
		         $arraydata = "['".parse_url($rowst3['referrer'], PHP_URL_PATH)."', ".$rowst3['total_hits']."]";
		         
		         // total for each day
		         $fostat13[] = $arraydata;
		         
		     }
		 }
		 
		 if ($arrayfeedback) $arrayoperator = array_replace_recursive($arrayoperator, $arrayfeedback);
		 
		 // Load all countries
		 if ($lsdata) $stat1country = join(", ", $lsdata);
		 
		 // Load referrer
		 if ($fostat13) $stat1ref = join(", ", $fostat13);
		 
		// Call the template
		$template = 'statistics.php';

?>