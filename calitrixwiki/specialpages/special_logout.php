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
 * This is the logout specialpage. It removes the current user session.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_logout extends core
{
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$db   = &singleton('database');
		$tpl  = &singleton('template');
		
		$tpl->assign('logoutMsg', $this->lang['logout']);
		
		if($this->loggedIn) {
			$result = $db->query('SELECT subs_user_id FROM '.DB_PREFIX.'subscriptions '.
			'WHERE subs_user_id = '.$this->user['user_id'].' AND subs_sent = "1"');
			
			if($db->numRows($result) == 1) {
				$db->query('UPDATE '.DB_PREFIX.'subscriptions '.
				'SET subs_sent = "0" '.
				'WHERE subs_user_id = '.$this->user['user_id']);
			}
			
			$this->removeSession($this->session['session_user_id']);
		}
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'special_logout.tpl';
	}
}
?>
