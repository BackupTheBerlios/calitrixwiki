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
 * This is the "edit"-Action. It displays a edit form and
 * saves the page to the database.
 *
 * @author Johannes Klose <exe@calitrix.de>
 * @since 1.0 Beta 1 25.05.04 20:00
 **/
class action_edit extends core
{
	var $editText     = '';
	var $editSummary  = '';
	var $editUserId   = 0;
	var $editUsername = '';
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array  &$page Page data
	 * @return void
	 **/
	function start()
	{
		$this->pageAction = $this->lang['edit_page'];
		
		if(!$this->hasPerms(PERM_VIEW)) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$parser = &singleton('parser');
		$tpl    = &singleton('template');
		
		$tpl->assign('isMessage', false);
		$tpl->assign('isPreview', false);
		$editText = $this->page['page_text'];
		
		/**
		 * If the user wants to edit a old page version we create the
		 * text of this version and generate a general warning.
		 **/
		if(isset($this->get['v']) && preg_match('/^\d+\.\d+\.\d+$/', $this->get['v'])) {
			if(!$this->hasPerms(PERM_EDIT | PERM_RESTORE)) {
				$this->messageEnd('wiki_perm_denied');
			}
			
			$oldVersion = $this->getOldPageText($this->get['v']);
			
			if($oldVersion !== false) {
				$tpl->assign('isMessage', true);
				$tpl->assign('message', sprintf($this->lang['wiki_edit_old_version'], $oldVersion['version'], $oldVersion['time']));
				$editText = $oldVersion['text'];
			}
		}
		
		$tpl->assign('editText',    htmlentities($editText));
		
		if(!$this->hasPerms(PERM_EDIT)) {
			$tpl->assign('allowSubmit', false);
			$tpl->assign('isMessage',   true);
			$tpl->assign('message',     $this->lang['edit_not_editable']);
		} else {
			$tpl->assign('allowSubmit', true);
		}
		
		/**
		 * Save page if changes are submited ..
		 **/
		if($this->request == 'POST' && $this->hasPerms(PERM_EDIT)) {
			if($this->validatePageData()) {
				if(isset($this->post['preview'])) {
					$previewPage = $this->page;
					$previewPage['page_text'] = $this->editText;
					$parser->parseSignatures($previewPage['page_text']);
					$previewText = $parser->parseText($previewPage);
					
					$tpl->assign('isPreview',   true);
					$tpl->assign('previewText', $previewText);
					$tpl->assign('editText',    htmlentities($this->editText));
				} else {
					$parser->parseSignatures($this->editText);
					
					$this->savePageData($this->page['page_id'], $this->page['page_name'], $this->page['page_namespace'], 
					                    $this->editText, $this->page['page_text'], $this->page['page_version'],
					                    $this->editUserId, $this->editUsername, $this->editSummary);
					
					$this->sendNotifications();
					$this->HTTPRedirect($this->genUrl($this->getUniqueName($this->page), '', array(), false));
				}
			} else {
				// unused yet
			}
		}
	}
	
	/**
	 * This functions validates the page data.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return bool         True on success, false otherwise
	 **/
	function validatePageData()
	{
		$this->editText    = isset($this->post['page_text'])    ? trim($this->post['page_text'])    : '';
		$this->editSummary = isset($this->post['page_summary']) ? trim($this->post['page_summary']) : '';
		
		if($this->loggedIn) {
			$this->editUserId   = $this->user['user_id'];
			$this->editUsername = '';
		} else {
			$this->editUserId = 0;
			$this->editUsername = isset($this->post['page_author']) ? trim($this->post['page_author']) : '';
		}
		
		return true;
	}
	
	/**
	 * Sends a notification to users who subscrited to.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function sendNotifications()
	{
		$db = &singleton('database');
		
		if($this->loggedIn) {
			$result = $db->query('SELECT u.user_id, u.user_name, u.user_email, u.user_last_visit, u.user_language '.
			'FROM '.DB_PREFIX.'subscriptions s LEFT JOIN '.DB_PREFIX.'users u ON u.user_id = s.subs_user_id '.
			'WHERE s.subs_sent = "0" AND s.subs_user_id != '.$this->user['user_id']);
		} else {
			$result = $db->query('SELECT u.user_id, u.user_name, u.user_email, u.user_last_visit, u.user_language '.
			'FROM '.DB_PREFIX.'subscriptions s LEFT JOIN '.DB_PREFIX.'users u ON u.user_id = s.subs_user_id '.
			'WHERE s.subs_sent = "0"');
		}
		
		if($db->numRows($result) < 1) {
			return;
		}
		
		$langs       = array();
		$usersSent   = array();
		$messageFrom = 'From: '.$this->cfg['mailer_from'].' <'.$this->cfg['mail_from'].'>';
		$rcUrl       = $this->genUrl($this->cfg['special_namespace'].':RecentChanges', '', array(), false);
		$hpUrl       = $this->genUrl($this->cfg['default_page'], '', array(), false);
		$dnUrl       = $this->genUrl($this->cfg['special_namespace'].':Preferences', '', array('op' => 'prefs'), false);
		
		while($row = $db->fetch($result))
		{
			if($row['user_language'] == '') {
				$row['user_language'] = $this->cfg['default_lang'];
			}
			
			if(!isset($this->langs[$row['user_language']])) {
				$filename = $this->cfg['lang_dir'].'/'.$row['user_language'].'_mails.php';
				
				include $filename;
				$langs[$row['user_language']] = $$row['user_language'];
				unset($$row['user_language']);
			}
			
			$messageSubject = $langs[$row['user_language']]['notification_subject'];
			$messageBody    = $langs[$row['user_language']]['notification_body'];
			$messageTo      = $row['user_name'].' <'.$row['user_email'].'>';
			
			$messageSubject = sprintf($messageSubject, $this->cfg['wiki_title']);
			$messageBody    = sprintf($messageBody, $row['user_name'], 
			                  $this->convertTime($row['user_last_visit']),
			                  $rcUrl,  $this->cfg['wiki_title'], $hpUrl, $dnUrl);
			@mail($messageTo, $messageSubject, $messageBody, $messageFrom);
			$usersSent[] = $row['user_id'];
		}
		
		$db->query('UPDATE '.DB_PREFIX.'subscriptions '.
		'SET subs_sent = "1" '.
		'WHERE subs_user_id IN('.join(', ', $usersSent).')');
	}
	
	/**
	 * Returns the template name for this action.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'action_edit.tpl';
	}
}
?>