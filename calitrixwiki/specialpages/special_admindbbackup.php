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
 
include $cfg['lib_dir'].'/class_admin.php';

/**
 * This is the admin specialpage for backuping the database of the Wiki.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_admindbbackup extends admin
{
	var $dbTableList = array();
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$tpl = &singleton('template');
		$tpl->assign('isMessage', false);
		
		$this->lang['admin_db_backup_desc'] = sprintf($this->lang['admin_db_backup_desc'], ini_get('memory_limit'));
		
		$this->dbTableList = $this->createDbTableList();
		$tpl->assign('dbTables', $this->dbTableList);
		
		if($this->request == 'POST') {
			$this->createDbBackup();
		}
	}
	
	/**
	 * Generates a backup file of the selected tables.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function createDbBackup()
	{
		$tables  = isset($this->post['tables']) && is_array($this->post['tables']) ? $this->post['tables'] : array();
		$db      = &singleton('database');
		$tpl     = &singleton('template');
		$backup  = '# CalitrixWiki database backup'."\n";
		$backup .= '# Created on '.$this->convertTime($this->time)."\n";
		$backup .= '# Created for CalitrixWiki '.CWIKI_VERSION."\n";
		
		@set_time_limit(0);
		
		foreach($tables as $table)
		{
			if(!isset($this->dbTableList[$table])) {
				continue;
			}
			
			$backup .= $this->createTableDefinition($table);
			$backup .= $this->createTableBackup($table);
		}
		
		$fname    = 'calitrixwiki_backup_'.$this->time.'.sql';
		$sendType = 'none';
		
		if(isset($this->post['sendtype'])) {
			$sendType = $this->post['sendtype'];
		}
		
		if($sendType == 'gzip') {
			header('Content-Type: application/x-gzip');
			
			if(strpos('MSIE', $this->server['HTTP_USER_AGENT']) !== false) {
				header('Content-Disposition: inline; filename="'.$fname.'.gz"');
			} else {
				header('Content-Disposition: attachment; filename="'.$fname.'.gz"');
			}
			
			echo gzencode($backup, 9);
		} elseif($sendType == 'raw') {
			if(strpos('MSIE', $this->server['HTTP_USER_AGENT']) !== false) {
				header('Content-Type: application/octetstream');
				header('Content-Disposition: inline; filename="'.$fname.'"');
			} else {
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.$fname.'"');
			}
			
			echo $backup;
		} else {
			header('Content-Type: text/plain');
			echo $backup;
		}
		
		exit;
	}
	
	/**
	 * Creates the sql statements needed to restore the data
	 * of the table in $table.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $table Name of the table which shall be backuped
	 * @return void
	 **/
	function createTableBackup($table)
	{
		$db     = &singleton('database');
		$data   = '';
		$first  = true;
		$fields = array();
		
		$result = $db->query('SHOW COLUMNS FROM '.$table);
		
		while($row = $db->fetch($result))
		{
			$name = $row['Field'];
			$type = $row['Type'];
			
			$type = preg_replace('/^([a-z]+)(\(.+?\))?$/', '\1', $type);
			
			if($type == 'tinyint' || $type == 'smallint' || $type == 'mediumint' ||
			   $type == 'int'     || $type == 'integer'  || $type == 'bigint'    ||
			   $type == 'real'    || $type == 'double'   || $type == 'float'     ||
			   $type == 'decimal' || $type == 'numeric'  || $type == 'timestamp') {
				$type = 'noquote';
			} elseif($type == 'char'       || $type == 'varchar'    || $type == 'date'     ||
			         $type == 'time'       || $type == 'datetime'   || $type == 'blob'     ||
			         $type == 'mediumblob' || $type == 'longblob'   || $type == 'tynitext' ||
			         $type == 'text'       || $type == 'mediumtext' || $type == 'longtext' ||
			         $type == 'enum'       || $type == 'set') {
				$type = 'quote';
			}
			
			$fields[$name] = $type;
		}
		
		$result = $db->query('SELECT * FROM '.$table);
		
		while($row = $db->fetch($result))
		{
			$insert = 'INSERT INTO '.$table.'(';
			$values = 'VALUES(';
			
			foreach($row as $field => $val)
			{
				$insert .= $field.', ';
				
				if($fields[$field] == 'quote') {
					$val = addslashes($val);
					$val = str_replace("\r\n", '\r\n', $val);
					$val = str_replace("\n",   '\n',   $val);
					$values .= '\''.$val.'\', ';
				} else {
					$values .= $val.', ';
				}
			}
			
			$values = substr($values, 0, strlen($values) - 2);
			$insert = substr($insert, 0, strlen($insert) - 2);
			$data  .= $insert.') '.$values.');'."\n";
		}
		
		return $data;
	}
	
	/**
	 * Creates a 'CREATE TABLE ...' sql statement restoring 
	 * the structure of the table in $table.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $table Name of the table which shall be backuped
	 * @return void
	 **/
	function createTableDefinition($table)
	{
		$db = &singleton('database');
		$def     = 'DROP TABLE IF EXISTS '.$table.';'."\n".'CREATE TABLE '.$table.'(';
		$inserts = '';
		$isAi    = false;
		
		// Dump column definitions of this table.
		$result = $db->query('SHOW COLUMNS FROM '.$table);
		
		while($row = $db->fetch($result))
		{
			$def .= $row['Field'].' '.$row['Type'].' ';
			
			if($row['Null'] == 'YES') {
				$def .= 'NULL ';
			} else {
				$def .= 'NOT NULL ';
			}
			
			if($row['Extra'] == 'auto_increment') {
				$def .= 'auto_increment, ';
				$isAi = true;
			} else {
				$def .= 'default \''.addslashes($row['Default']).'\', ';
			}
		}
		
		// Dump indexes for this table.
		$result  = $db->query('SHOW INDEX FROM '.$table);
		$indexes = array();
		
		while($row = $db->fetch($result))
		{
			if(!isset($indexes[$row['Key_name']])) {
				$indexes[$row['Key_name']] = array(
				                                   'unique'  => $row['Non_unique'] == 1 ? false : true,
				                                   'columns' => array($row['Column_name']),
				                                   'type'    => $row['Index_type']
				                                   );
				continue;
			}
			
			$indexes[$row['Key_name']]['columns'][] = $row['Column_name'];
		}
		
		foreach($indexes as $indexName => $indexData)
		{
			if($indexName == 'PRIMARY') {
				$def .= 'PRIMARY KEY ('.join(', ', $indexData['columns']).'), ';
			} elseif(!$indexData['unique']) {
				if($indexData['type'] == 'FULLTEXT') {
					$def .= 'FULLTEXT KEY '.$indexName.' ('.join(', ', $indexData['columns']).'), ';
				} else {
					$def .= 'KEY '.$indexName.' ('.join(', ', $indexData['columns']).'), ';
				}
			} else {
				$def .= 'UNIQUE KEY '.$indexName.' ('.join(', ', $indexData['columns']).'), ';
			}
		}
		
		$def  = substr($def, 0, strlen($def) - 2);
		$def .= ') TYPE='.$this->dbTableList[$table]['table_type'];
		
		if($isAi) {
			$def .= ' AUTO_INCREMENT='.$this->dbTableList[$table]['auto_increment'];
		}
		
		$def .= ";\n";
		return $def;
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'admin_dbbackup.tpl';
	}
}
?>