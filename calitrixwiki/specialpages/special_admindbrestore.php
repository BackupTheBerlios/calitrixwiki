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
 * This is the admin specialpage for restoring a backup.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_admindbrestore extends admin
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
		
		$this->lang['admin_db_restore_desc'] = sprintf($this->lang['admin_db_restore_desc'], ini_get('upload_max_filesize'));
		
		if($this->request == 'POST') {
			$this->importDbBackup();
		}
	}
	
	/**
	 * Imports a database backup.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function importDbBackup()
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		if(!isset($this->files['upload_file'])) {
			return false;
		}
		
		$fileName = $this->files['upload_file']['tmp_name'];
		$fileType = $this->files['upload_file']['type'];
		
		if($fileType == 'application/gzip' || $fileType == 'application/x-gzip') {
			$backup = $this->readCompressedBackup($fileName);
		} elseif($fileType == 'text/x-sql' || $fileType == 'text/plain') {
			$backup = $this->readRawBackup($fileName);
		} else {
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_db_unknown_backup']);
			return false;
		}
		
		$sql = '';
		
		foreach($backup as $line)
		{
			$line = $line;
			
			if($line == '' || $line[0] == '#') {
				continue;
			}
			
			$sql .= $line;
			
			if(substr($line, strlen($line) - 1, strlen($line)) == ';') {
				$db->query($sql);
				$sql = '';
			}
		}
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_db_backup_restored']);
	}
	
	/**
	 * Reads a compressed backup file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  $fileName Backup file
	 * @return void
	 **/
	function readCompressedBackup($fileName)
	{
		$fp = gzopen($fileName, 'rb');
		
		if(!$fp) {
			return false;
		}
		
		$backup = array();
		$buff   = '';
		
		while(!gzeof($fp))
		{
			$buff .= gzgets($fp, 4096);
			
			if(substr($buff, strlen($buff) - 1, strlen($buff)) == "\n") {
				$backup[] = trim($buff);
				$buff     = '';
			}
		}
		
		gzclose($fp);
		
		return $backup;
	}
	
	/**
	 * Reads an uncompressed (raw) backup file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  $fileName Backup file
	 * @return void
	 **/
	function readRawBackup($fileName)
	{
		$fp = fopen($fileName, 'r');
		
		if(!$fp) {
			return false;
		}
		
		$backup = array();
		$buff   = '';
		
		while(!feof($fp))
		{
			$buff .= fgets($fp, 4096);
			
			if(substr($buff, strlen($buff) - 1, strlen($buff)) == "\n") {
				$backup[] = trim($buff);
				$buff     = '';
			}
		}
		
		return $backup;
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'admin_dbrestore.tpl';
	}
}
?>