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

class installer_requirements extends installer
{
	/**
	 * Does everything needed to be done for this step.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$tpl = &singleton('template');
		$requirements = $this->checkRequirements();
		$tpl->assign('requirements', $requirements);
	}
	
	/**
	 * Checks the system requirements for the installation 
	 * and returns an array including all of them marked ok or failed.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return array Requirements
	 **/
	function checkRequirements()
	{
		$requirements = array();
		
		if(version_compare('4.3.0', phpversion(), '>')) {
			$requirements[] = array('name' => $this->lang['req_php_version'], 'ok' => false);
		} else {
			$requirements[] = array('name' => $this->lang['req_php_version'], 'ok' => true);
		}
		
		if(!extension_loaded('mysql')) {
			$requirements[] = array('name' => $this->lang['req_mysql_ext'], 'ok' => false);
		} else {
			$requirements[] = array('name' => $this->lang['req_mysql_ext'], 'ok' => true);
		}
		
		if(!is_writeable(CWIKI_SET_DIR)) {
			$requirements[] = array('name' => $this->lang['req_set_dir'], 'ok' => false);
		} else {
			$requirements[] = array('name' => $this->lang['req_set_dir'], 'ok' => true);
		}
		
		if(!is_writeable(CWIKI_INSTALL_DIR)) {
			$requirements[] = array('name' => $this->lang['req_install_dir'], 'ok' => false);
		} else {
			$requirements[] = array('name' => $this->lang['req_install_dir'], 'ok' => true);
		}
		
		return $requirements;
	}
	
	/**
	 * Returns the template name for this installation step.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function getTemplate()
	{
		return 'installer_requirements.tpl';
	}
}
?>