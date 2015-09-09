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

class LS_rewrite
{

	private $url_seg;
	private $data = array();
	
	// This constructor can be used for all classes:
	
	public function __construct($url){
			
			$this->url = $url;
	}
	
	public function lsGetseg($var)
	{
	
			// get the url and parse it
			$parseurl = parse_url($this->url);
			
			// get only the query
			$parameters = $parseurl["query"];
			parse_str($parameters, $data);
			
			// Now we have to set the array to basic keys
			foreach($data as $d)
			{
				$data[] = $d;
			}
		
			$url_seg = $data[$var];
		
		return $url_seg;
	}
	
	public function lsGetsegAdmin($var)
	{
	
			// get the url and parse it
			$parseurl = parse_url($this->url);
			
			// get only the query
			$parameters = $parseurl["query"];
			parse_str($parameters, $data);
			
			// Now we have to set the array to basic keys
			foreach($data as $d)
			{
				$data[] = $d;
			}
		
			$url_seg = $data[$var];
		
		return $url_seg;
	}
	
	public static function lsParseurl($var, $var1, $var2, $var3, $var4)
	{
		
			// Set v to zero
			$v = '';
			$v1 = '';
			$v2 = '';
				
			if ($var1) {
				$v = '&amp;w='.$var1;
			}
			if ($var2) {
				$v1 = '&amp;id='.$var2;
			}
			if ($var3) {
				$v2 = '&amp;s='.$var3;
			}
			
			if ($var4) {
				$v3 = '&amp;b='.$var4;
			}
			
			
			$var = 'p='.$var;
			
			// Now se the var for none apache
			$varname = 'index.php?'.$var.$v.$v1.$v2.$v3;
		
		return $varname;
		
	}
	
	public function lsParseurlpaginate($var)
	{
		
		// Check if is/not apache and create url
		if ($var) {
			
			// Now se the var for none apache
			$varname = '&amp;page='.$var;
		
		} elseif (LS_PAGINATE_ADMIN) {
			
			// Now se the var for none apache
			$varname = '&amp;page='.$var;
			
		} else {
				
			// Now se the var for none apache
			$varname = '&amp;page='.$var;
				
		}
		
		return $varname;
	
	}
	
	public function lsRealrequest()
	{
		$r = str_replace(_APP_MAIN_DIR, '', $this->url);
		
		return $r;
	}
}
?>