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

class installer_admin extends installer
{
	var $adminTemplate = 'installer_admin.tpl';
	
	/**
	 * Does everything needed to be done for this step.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$tpl = &singleton('template');
		$tpl->assign('valName', '');
		$tpl->assign('valMail', '');
		$tpl->assign('isError', false);
		
		if($this->request == 'POST') {
			$this->createAdmin();
		}
	}
	
	/**
	 * Creates the administration account.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return bool true on success, false otherwise
	 **/
	function createAdmin()
	{
		$tpl = &singleton('template');
		
		include CWIKI_SET_DIR.'/dbconfig.php';
		include CWIKI_SET_DIR.'/stdconfig.php';
		
		$tpl->assign('wikiUrl', $cfg['url_root']);
		
		$adminName  = isset($this->post['admin_name'])   ? trim($this->post['admin_name'])   : '';
		$adminMail  = isset($this->post['admin_mail'])   ? trim($this->post['admin_mail'])   : '';
		$adminPass  = isset($this->post['admin_pass'])   ? trim($this->post['admin_pass'])   : '';
		$adminPassC = isset($this->post['admin_pass_c']) ? trim($this->post['admin_pass_c']) : '';
		$errors     = array();
		
		if(!preg_match('/^'.$cfg['title_format'].'$/', $cfg['users_namespace'].':'.$adminName)) {
				$errors[] = $this->lang['admin_invalid_name'];
		}
		
		if(!preg_match($cfg['match_email'], $adminMail)) {
				$errors[] = $this->lang['admin_invalid_email'];
		}
		
		if(strlen($adminPass) < $cfg['min_password_length']) {
				$errors[] = sprintf($this->lang['admin_password_short'], $cfg['min_password_length']);
		}
		
		if($adminPass != $adminPassC) {
				$errors[] = $this->lang['admin_passwords_dont_match'];
		}
		
		if(count($errors) > 0) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  $errors);
			$tpl->assign('valName', htmlentities($adminName));
			$tpl->assign('valMail', htmlentities($adminMail));
			return false;
		}
		
		$sql = 'INSERT INTO '.DB_PREFIX.'users(user_group_id, user_name, user_password, user_email, '.
		'user_reg_time, user_last_visit) VALUES('.CWIKI_ADMIN_GROUP.', "'.addslashes($adminName).'", "'.sha1($adminPass).'", '.
		'"'.addslashes($adminMail).'", '.time().', '.time().')';
		
		mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_NAME);
		
		if(@mysql_query($sql)) {
			$tpl->assign('isError', false);
			$this->adminTemplate = 'installer_ni_finished.tpl';
			$this->lockInstaller();
		} else {
			$tpl->assign('isError', true);
			$tpl->assign('error',   mysql_error());
			$this->adminTemplate = 'installer_ni_finished.tpl';
		}
	}
	
	/**
	 * Returns the template name for this installation step.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function getTemplate()
	{
		return $this->adminTemplate;
	}
}
?>