<?PHP
/*
 * CalitrixWiki (c) Copyright 2004 by Johannes Klose
 * E-Mail: exe@calitrix.de
 * Project page: http://developer.berlios.de/projects/calitrixwiki
 * 
 * CalitrixWiki is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * CalitrixWiki is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CalitrixWiki; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **/

/**
 * This is the database abstraction class. It abstracts
 * the common sql database functions to make it possible
 * to switch between different sql database servers.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class database
{
	var $conn;
	var $queries = 0;
	var $runtimes = 0;
	var $host;
	var $user;
	var $password;
	var $database;
	var $query_debug;
	var $serverVersion = '';
	var $shortVersion  = '';
	
	/**
	 * Class constructor; sets variables
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function database()
	{
		$this->host     = DB_HOST;
		$this->user     = DB_USER;
		$this->password = DB_PASS;
		$this->database = DB_NAME;
		
		$this->connect();
	}
	
	/** Sets up a connection to the database server
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function connect()
	{
		$this->conn          = mysql_connect($this->host, $this->user, $this->password) or $this->error(mysql_error(), __LINE__, __FILE__);
		$this->serverVersion = explode('.', preg_replace('/^(\d+)\.(\d+)\.(\d+)(.*?)$/', '\1.\2.\3', mysql_get_server_info()));
		$this->select_db();
	}
	
	/**
	 * Selects a database
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function select_db()
	{
		@mysql_select_db($this->database, $this->conn) or $this->error(mysql_error(), __LINE__, __FILE__);
	}
	
	/**
	 * Sends a query to the sql server and returns the result resource
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $query_string
	 * @return resource result set
	 **/
	function query($query_string)
	{
		$starttime = $starttime = explode(' ', microtime());
		$starttime = $starttime[1] + $starttime[0];	
		
		$result = mysql_query($query_string, $this->conn) or $this->error(mysql_error(), __LINE__, __FILE__, $query_string);
		
		$endtime   = explode(' ',microtime());
		$endtime   = $endtime[1] + $endtime[0];
		$totaltime = $endtime - $starttime;
		
		$this->runtimes += $totaltime;
		$this->queries++;
		$this->query_debug .= round($totaltime, 5).': '.$query_string.'<br>';
		
		return $result;
	}
	
	/**
	 * Executes a query and returns the first returned
	 * row as an array, if the query returned at least one.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $queryString
	 * @return array result row
	 **/
	function queryRow($queryString)
	{
		$result = $this->query($queryString);
		
		if($this->numRows($result) > 0) {
			return $this->fetch($result);
		} else {
			return false;
		}
	}
	
	/**
	 * Fetches the next row and returns it as an array
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param resource $result
	 * @return array fetched row
	 **/
	function fetch($result)
	{
		return mysql_fetch_assoc($result);
	}
	
	/**
	 * Returns the number of rows of a result set
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param resource $result
	 * @return int number of rows
	 **/
	function numRows($result)
	{
		return mysql_num_rows($result);
	}
	
	/**
	 * Returns the auto id of the last inserted row
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return int last insert id
	 **/
	function insertId()
	{
		return mysql_insert_id($this->conn);
	}
	
	/**
	 * Outputs an error message and cancels the script
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $err_msg
	 * @param int $line
	 * @param string $file
	 * @return void
	 **/
	function error($err_msg, $line, $file, $query_string = null)
	{
		echo '<html>'."\n".'<head>'."\n".'<title>Database error</title>'."\n".'</head>'."\n\n".
		     '<body bgcolor="#ffffff">'."\n".'<span style="font-family:arial,helvetica,sans-serif;font-weight:bold">'.
		     'There was an error in the database and the scripts execution was halted.<br><br>'.
		     '<span style="color:#ff0000">'.$err_msg.'</span><br><br>'.
		     ($query_string != '' ? 'Query was: '.$query_string.'<br>' : '').
		     'In '.$file.' on line '.$line.':<br><hr width="90%">';
		
		$lines = file($file);
		for($i = $line-3; $i < $line+2; $i++)
		{
			$lines[$i] = str_replace(' ', '&nbsp;', $lines[$i]);
			$lines[$i] = str_replace("\t", '&nbsp&nbsp;&nbsp;&nbsp;', $lines[$i]);
			
			if(($i+1) == $line) {
				echo ($i+1).': <span style="font-weight:bold;background-color:#ffef8c;color:#ff0000">'.$lines[$i].'</span><br>';
			} else {
				echo ($i+1).': <span style="font-weight:normal">'.$lines[$i].'</span><br>';
			}
		}
		
		echo '<hr width="90%"><br><br><span style="font-weight:normal">'.$this->query_debug.'</span></span>'."\n".'</body>'."\n".'</html>';		
		exit;
	}
}
?>