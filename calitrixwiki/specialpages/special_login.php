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
 * This is the login specialpage. It provides a login form for loggin
 * a user in.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_login extends core
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
		
		if($this->loggedIn) {
			$tpl->assign('loginMsg', $this->lang['login_already_logged_in']);
			return;
		}
		
		$tpl->assign('loginMsg', '');
		
		if($this->request == 'POST') {
			$username = isset($this->post['username']) ? trim($this->post['username']) : '';
			$password = isset($this->post['password']) ? trim($this->post['password']) : '';
			
			if($username == '' || $password == '') {
				$tpl->assign('loginMsg', $this->lang['login_invalid']);
			}
			
			$result = $db->query('SELECT u.*, g.* FROM '.DB_PREFIX.'users u '.
			'LEFT JOIN '.DB_PREFIX.'groups g ON g.group_id = u.user_group_id '.
			'WHERE u.user_name = \''.addslashes($username).'\' AND u.user_password = \''.sha1($password).'\'');
			
			if($db->numRows($result) == 1) {
				$row = $db->fetch($result);
				$this->removeSession($row['user_id']);
				
				if($row['user_use_cookies'] == 1) {
					$useCookies = true;
				} else {
					$useCookies = false;
				}
				
				if(isset($this->post['remember'])) {
					$this->createSession($row['user_id'], $row['user_name'], $useCookies, true, $row['user_password']);
				} else {
					$this->createSession($row['user_id'], $row['user_name'], $useCookies);
				}
				
				$this->user     = $row;
				$this->loggedIn = true;
				$tpl->assign('loginMsg', $this->lang['login_success']);
			} else {
				$tpl->assign('loginMsg', $this->lang['login_invalid']);
			}
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
		return 'special_login.tpl';
	}
}
?>