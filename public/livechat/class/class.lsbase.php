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

include_once 'class.rewrite.php';

class LS_base
{
	private $data = array();
	private $usraccesspl = array();
	private $case;
	private $lsvar;
	private $lsvar1;
	protected $table = '', $itemid = '', $select = '', $where = '', $dseo = '';
	
	// This constructor can be used for all classes:
	
	public function __construct(array $options){
			
			foreach($options as $k=>$v){
				if(isset($this->$k)){
					$this->$k = $v;
				}
			}
	}
	
	public static function lsTimesince($mysqlstamp, $date, $time)
	{
	
		$today = time(); /* Current unix time  */
		$mysqlstamp = (is_numeric($mysqlstamp) ? $mysqlstamp : strtotime($mysqlstamp));
		$since = $today - $mysqlstamp;
		
		if ($since < 129600) {
		
		// array of time period chunks
		    $chunks = array(
		        array(60 * 60 * 24 , LS_DAY),
		        array(60 * 60 , LS_HOUR),
		        array(60 , LS_MINUTE),
		    );
		
		    $today = time(); /* Current unix time  */
		    $since = $today - $mysqlstamp;
		
		// $j saves performing the count function each time around the loop
		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
		
		    $seconds = $chunks[$i][0];
		    $name = $chunks[$i][1];
		
		    // finding the biggest chunk (if the chunk fits, break)
		    if (($count = floor($since / $seconds)) != 0) {
		        break;
		    }
		}
		
		$lsdata = (($count == 1) ? '1 '.$name : "$count {$name}".LS_MULTITIME).' '.LS_AGO;
		
		} else {
		
			$lsdata = date($date.$time, $mysqlstamp);
		}
		
		return $lsdata;
	
	}
	
	public static function lsCheckSession($userid,$convid)
	{
	
		$chat_ended = time() + 600;
		
		global $lsdb;
		$result = $lsdb->query('SELECT id FROM '.DB_PREFIX.'sessions WHERE userid = "'.smartsql($userid).'" AND id = "'.smartsql($convid).'" AND ended <= '.$chat_ended.' LIMIT 1');
		if ($lsdb->affected_rows == 0) {
			return true;
		}
	
	}
	
	public function lsSessiontimelimit()
	{
		
		// Start the session
		session_start();
		
		// Set new after 10 minutes
		$inactive = 600;
		
		// check to see if $_SESSION['timeout'] is set
		if(isset($_SESSION['timeout']) ) {
			$session_life = time() - $_SESSION['timeout'];
			
			if($session_life > $inactive) { 
				
				$loadnew = false;
				
				// Write the session timeout new, because the 10 minutes are over
				$_SESSION['timeout'] = time();
			} else {
				
				$loadnew = true;
			} 
		} else {
		
			// Write the session timeout new
			$_SESSION['timeout'] = time();
		
		}
		
		return $loadnew;
	}
	
	public static function lsWriteinCache($file, $content, $extra)
	{
	
		if ($file && $content) {
		
			if (isset($extra)) {
				file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
			} else {
				file_put_contents($file, $content, LOCK_EX);
			}
		}
	
	}

}
?>