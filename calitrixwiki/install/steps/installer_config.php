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

class installer_config extends installer
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
		
		$dirname = dirname($this->server['PHP_SELF']);
		
		$tpl->assign('cfgUrlRoot',          'http://'.$this->server['HTTP_HOST'].substr($dirname, 0, strlen($dirname) - 8));
		$tpl->assign('cfgDocRoot',          realpath(dirname($this->server['SCRIPT_FILENAME']).'/..'));
		$tpl->assign('cfgDbHost',           'localhost');
		$tpl->assign('cfgDbName',           'cwiki');
		$tpl->assign('cfgDbUser',           'root');
		$tpl->assign('cfgDbPrefix',         'cwiki_');
		$tpl->assign('dbCreateChecked',     false);
		$tpl->assign('defaultPagesChecked', false);
	}
	
	/**
	 * Returns the template name for this installation step.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function getTemplate()
	{
		return 'installer_config.tpl';
	}
}
?>