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
 * This is the register specialpage. It allows it for visitors to register
 * and protect their name.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_register extends core
{
	var $regErrors   = array();
	var $regUser     = '';
	var $regEmail    = '';
	var $regPassword = '';
	var $regCookies  = false;
	
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
		
		$tpl->assign('isError',        false);
		$tpl->assign('regDone',        false);
		$tpl->assign('errors',         array());
		$tpl->assign('valUser',        '');
		$tpl->assign('valEmail',       '');
		$tpl->assign('cookiesChecked', 'checked="checked" ');
		
		// If the user is already logged in we print a 
		// short error message.
		if($this->loggedIn) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  array($this->lang['register_registered']));
			return;
		}
		
		// If this is a post request we check the user submited
		// a valid registration.
		if($this->request == 'POST') {
			if($this->validateRegistration()) {
				$this->saveRegistration();
				$tpl->assign('regDone', true);
			} else {
				$tpl->assign('isError', true);
				$tpl->assign('errors',  $this->regErrors);
			}
		}
	}
	
	/**
	 * Validates a submited registration.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return bool True on success, false otherwise
	 **/
	function validateRegistration()
	{
		$tpl = &singleton('template');
		
		$success           = true;
		$this->regUser     = isset($this->post['username'])    ? trim($this->post['username']) : '';
		$this->regPassword = isset($this->post['password'])    ? $this->post['password']       : '';
		$passConfirm       = isset($this->post['confirm'])     ? $this->post['confirm']        : '';
		$this->regEmail    = isset($this->post['email'])       ? $this->post['email']          : '';
		$this->regCookies  = isset($this->post['use_cookies']) ? '1'                           : '0';
		
		$tpl->assign('valUser',        htmlentities($this->regUser));
		$tpl->assign('valEmail',       htmlentities($this->regEmail));
		$tpl->assign('cookiesChecked', $this->regCookies == '1' ? 'checked="checked" ' : '');
		
		// Validate that the username is neither to short nor to long and
		// ensure it doesnt exist already.
		if(strlen($this->regUser) < $this->cfg['min_username_length']) {
			$this->regErrors[] = sprintf($this->lang['register_short_username'], $this->cfg['min_username_length']);
			$success = false;
		} elseif(strlen($this->regUser) > $this->cfg['max_username_length']) {
			$this->regErrors[] = sprintf($this->lang['register_long_username'], $this->cfg['max_username_length']);
			$success = false;
		} elseif(!preg_match('/^'.$this->cfg['title_format'].'$/', $this->cfg['users_namespace'].':'.$this->regUser)) {
			$this->regErrors[] = $this->lang['register_invalid_username'];
			$success = false;
		} elseif(is_array($this->getUser($this->regUser))) {
			$this->regErrors[] = $this->lang['register_username_taken'];
			$success = false;
		}
		
		// Validate that the password is not to short and check wether the user
		// entered two matching passwords.
		if(strlen($this->regPassword) < $this->cfg['min_password_length']) {
			$this->regErrors[] = sprintf($this->lang['register_short_password'], $this->cfg['min_password_length']);
			$success = false;
		} elseif($this->regPassword != $passConfirm) {
			$this->regErrors[] = $this->lang['register_wrong_password'];
			$success = false;
		}
		
		// Validate the email adress.
		if(!preg_match($this->cfg['match_email'], $this->regEmail)) {
			$this->regErrors[] = $this->lang['register_invalid_email'];
			$success = false;
		}
		
		return $success;
	}
	
	/**
	 * Save a validated registration to the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function saveRegistration()
	{
		$db = &singleton('database');
		
		$db->query('INSERT INTO '.DB_PREFIX.'users(user_group_id, user_name, '.
		'user_password, user_email, user_reg_time, user_last_visit, user_use_cookies) '.
		'VALUES('.$this->cfg['default_user_group'].', "'.addslashes($this->regUser).'", "'.sha1($this->regPassword).'", '.
		'"'.$this->regEmail.'", '.$this->time.', '.$this->time.', "'.$this->regCookies.'")');
		
		return $db->insertId();
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'special_register.tpl';
	}
}
?>