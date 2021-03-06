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

/* Create custom exception classes */
class ConnectException extends Exception {}
class QueryException extends Exception {}

class ls_mysql
{
	var $link = null;
	private $host;
	private $username;
	private $passwd;
	private $dbname;
	private $dbport;
	
	function __construct($host, $username, $passwd, $dbname, $dbport)
	{
		$this->link = mysql_connect($host.':'.$dbport, $username, $passwd, $dbport);

	    /* Throw an error if the connection fails */ 
		if(!$this->link)
		{
			throw new ConnectException(mysql_error($this->link), mysql_errno($this->link)); 
		}
		if(!mysql_select_db($dbname,$this->link))
		{
			//throw new ConnectException('Cannot select db');
			$this->ls_throw_error(mysql_error($this->link), mysql_errno($this->link));
		}
	}
	
	public function set_charset($char)
	{
		$charset = mysql_set_charset($char, $this->link);
		return $charset;
	}
	
	public function query($query)
	{
		$result = mysql_query($query,$this->link); 
	  if(mysql_error($this->link))
		{
			// throw new QueryException(mysqli_error($this), mysqli_errno($this));
			//throw new QueryException(mysql_error($this->link), mysql_errno($this->link));
			$this->ls_throw_error(mysql_error($this->link), mysql_errno($this->link));
		}
		return new DbRowSet($result);
	}
	
	public function queryRow($query)
	{
		$result = mysql_query($query,$this->link);
		$lssql = mysql_fetch_array($result, MYSQL_ASSOC);
		return $lssql;
	}
	
	public function ls_last_id()
	{
		return mysql_insert_id($this->link);
	}
	
	public function ls_close()
	{
		if (!@mysql_close($this->link)) {
			//throw new Exception('Database query error');
			$this->ls_throw_error('Database query error');
		}
	}
	public function real_escape_string($lsvar)
	{
		$lsvar = mysql_real_escape_string($lsvar);
		return $lsvar;
	}
	public function __get($name)
		{
		   if(isset($this->$name)) {
	                return $this->$name;
		   }
	
		   if(!$this->link) {
		       return null;
		   }
	
		   switch($name) {
		       case 'affected_rows':   return mysql_affected_rows($this->link);
		       default:                return null;
		   }
		}
	
	public function ls_throw_error($msg='') {
		?>
			<table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
			<tr><th colspan=2>DB Error</th></tr>
			<tr><td align="right" valign="top">Message:</td><td><?php echo $msg; ?></td></tr>
			<?php if(strlen($this->error)>0) echo '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>'.$this->error.'</td></tr>'; ?>
			<tr><td align="right">Date:</td><td><?php echo date("l, F j, Y \a\\t g:i:s A"); ?></td></tr>
			<tr><td align="right">Script:</td><td><a href="<?php echo @$_SERVER['REQUEST_URI']; ?>"><?php echo @$_SERVER['REQUEST_URI']; ?></a></td></tr>
			<?php if(strlen(@$_SERVER['HTTP_REFERER'])>0) echo '<tr><td align="right">Referer:</td><td><a href="'.@$_SERVER['HTTP_REFERER'].'">'.@$_SERVER['HTTP_REFERER'].'</a></td></tr>'; ?>
			</table>
		<?php
		exit;
	}
	
}

class DbRowSet {

    var $result;

    function __construct($result) {
        $this->result = $result;
    }

    function fetch_assoc() {
        if($this->result) {
            return mysql_fetch_assoc($this->result);
        } else {
            return false;
        }
    }
}

?>