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
 * This is the admin specialpage for sending a newsletter to users.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_adminmailusers extends admin
{
	var $cfgGroups = array();
	
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
		
		$this->getGroupsList();
		
		$tpl->assign('selectGroups', $this->cfgGroups);
		
		if($this->request == 'POST') {
			$this->sendNewsletter();
		}
	}
	
	/**
	 * Sends the email to the selected users.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function sendNewsletter()
	{
		$tpl  = &singleton('template');
		$rcpt = $this->getRecipients();
		
		$subjectTpl = isset($this->post['mail_subject']) ? $this->post['mail_subject'] : '';
		$bodyTpl    = isset($this->post['mail_body'])    ? $this->post['mail_body']    : '';
		$sentCount  = 0;
		
		if(count($rcpt) < 1) {
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_mail_no_rcpt']);
		}
		
		foreach($rcpt as $user)
		{
			$messageTo      = $user['user_name'].' <'.$user['user_email'].'>';
			$messageFrom    = $this->cfg['mailer_from'].' <'.$this->cfg['mail_from'].'>';
			$messageSubject = str_replace('{userid}',    $user['user_id'],         $subjectTpl);
			$messageSubject = str_replace('{username}',  $user['user_name'],       $messageSubject);
			$messageSubject = str_replace('{useremail}', $user['user_email'],      $messageSubject);
			$messageSubject = str_replace('{usermail}',  $user['user_email'],      $messageSubject);
			$messageSubject = str_replace('{regdate}',   $user['user_reg_time'],   $messageSubject);
			$messageSubject = str_replace('{lastvisit}', $user['user_last_visit'], $messageSubject);
			$messageBody    = str_replace('{userid}',    $user['user_id'],         $bodyTpl);
			$messageBody    = str_replace('{username}',  $user['user_name'],       $messageBody);
			$messageBody    = str_replace('{useremail}', $user['user_email'],      $messageBody);
			$messageBody    = str_replace('{usermail}',  $user['user_email'],      $messageBody);
			$messageBody    = str_replace('{regdate}',   $user['user_reg_time'],   $messageBody);
			$messageBody    = str_replace('{lastvisit}', $user['user_last_visit'], $messageBody);
			
			@mail($messageTo, $messageSubject, $messageBody, $messageFrom);
			
			$sentCount++;
		}
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   sprintf($this->lang['admin_mail_sent'], $sentCount));
	}
	
	/**
	 * Makes a list of the selected recipients.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function getRecipients()
	{
		$rcptGroups = isset($this->post['rcpt_groups']) && is_array($this->post['rcpt_groups']) ? $this->post['rcpt_groups']              : array();
		$rcptUsers  = isset($this->post['rcpt_users'])                                          ? explode(';', $this->post['rcpt_users']) : array();
		$sql        = '';
		$sendGroups = array();
		$tmpUsers   = array();
		$sendUsers  = array();
		
		$db = &singleton('database');
		
		foreach($rcptGroups as $group)
		{
			if(isset($this->cfgGroups[$group])) {
				$sendGroups[] = $group;
			}
		}
		
		foreach($rcptUsers as $user)
		{
			if(preg_match('/^'.$this->cfg['title_format'].'$/', $this->cfg['users_namespace'].':'.$user)) {
				$tmpUsers[] = addslashes($user);
			}
		}
		
		if(count($sendGroups) < 1 && count($tmpUsers) < 1) {
			return array();
		}
		
		$sql = 'SELECT DISTINCT user_id, user_name, user_email, user_reg_time, user_last_visit '.
		'FROM '.DB_PREFIX.'users WHERE ';
		
		if(count($sendGroups) > 0) {
			$sql .= 'user_group_id IN('.join(', ', $sendGroups).') ';
		}
		if(count($tmpUsers) > 0) {
			$sql .= 'user_name IN("'.join('", "', $tmpUsers).'") ';
		}
		
		$result = $db->query($sql.'AND user_enable_mails = 1');
		
		if($db->numRows($result) < 1) {
			return array();
		}
		
		while($row = $db->fetch($result))
		{
			$row['user_reg_time']   = $this->convertTime($row['user_reg_time']);
			$row['user_last_visit'] = $this->convertTime($row['user_last_visit']);
			
			$sendUsers[] = $row;
		}
		
		return $sendUsers;
	}
	
	/**
	 * Makes a list of all user groups.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function getGroupsList()
	{
		$db = &singleton('database');
		$result = $db->query('SELECT group_id, group_name '.
		'FROM '.DB_PREFIX.'groups '.
		'ORDER BY group_id');
		
		while($row = $db->fetch($result))
		{
			$this->cfgGroups[$row['group_id']] = htmlentities($row['group_name']);
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
		return 'admin_mailusers.tpl';
	}
}
?>