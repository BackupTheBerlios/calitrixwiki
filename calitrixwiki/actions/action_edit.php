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
		$tpl->assign('isError',    false);
		$tpl->assign('isMessage',  false);
		$tpl->assign('isPreview',  false);
		$tpl->assign('valSummary', '');
		$tpl->assign('valAuthor',  '');
		$tpl->assign('editStart',  $this->page['page_version']);
		$tpl->assign('isConflict', false);
		
		if($this->loggedIn) {
			$tpl->assign('valAuthor', htmlentities($this->user['user_name']));
		}
		
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
		
		$tpl->assign('editText', htmlentities($editText));
		
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
		if($this->request == 'POST') {
			$this->savePageFirst();
		}
	}
	
	/**
	 * This function prepares a page for saving. If generates a preview
	 * if requested and checks edit conflicts before saving the page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function savePageFirst()
	{
		if(!$this->hasPerms(PERM_EDIT)) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$parser = &singleton('parser');
		$tpl    = &singleton('template');
		
		$editText    = isset($this->post['page_text'])    ? trim($this->post['page_text'])    : '';
		$editSummary = isset($this->post['page_summary']) ? trim($this->post['page_summary']) : '';
		$editStart   = isset($this->post['edit_start'])   ? $this->post['edit_start']         : $this->page['page_version'];
		
		if(!preg_match('/^\d+\.\d+\.\d+$/', $editStart)) {
			$editStart = $this->page['page_version'];
		}
		
		if($this->loggedIn) {
			$editUserId   = $this->user['user_id'];
			$editUsername = '';
		} else {
			$editUserId = 0;
			$editUsername = isset($this->post['page_author']) ? trim($this->post['page_author']) : '';
		}
		
		if($editUsername != '') {
			if(is_array($this->getUser($editUsername, true))) {
				$tpl->assign('isError', true);
				$tpl->assign('errors',  array($this->lang['edit_username_taken']));
				$tpl->assign('editText',   htmlentities($editText));
				$tpl->assign('valAuthor',  htmlentities($editUsername));
				$tpl->assign('valSummary', htmlentities($editSummary));
				$tpl->assign('editStart',  $editStart);
				return false;
			}
		}
		
		if(isset($this->post['preview'])) {
			$this->makePreview($editText, $editUsername, $editSummary, $editStart);
		} else {
			if(!$this->editConflicts($editText, $editUsername, $editSummary, $editStart)) {
				$parser->parseSignatures($editText);
				
				$this->savePageData($this->page['page_id'], $this->page['page_name'], $this->page['page_namespace'], 
				                    $editText, $this->page['page_text'], $this->page['page_version'],
				                    $editUserId, $editUsername, $editSummary);
				
				$this->sendNotifications();
				$this->HTTPRedirect($this->genUrl($this->getUniqueName($this->page), '', array(), false));
			}
		}
	}
	
	/**
	 * Generates a preview of the changes.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function makePreview($editText, $editUsername, $editSummary, $editStart)
	{
		$parser      = &singleton('parser');
		$tpl         = &singleton('template');
		$previewPage = $this->page;
		$previewPage['page_text'] = $editText;
		$parser->parseSignatures($previewPage['page_text']);
		$previewText = $parser->parseText($previewPage);
		
		$tpl->assign('isPreview',   true);
		$tpl->assign('previewText', $previewText);
		$tpl->assign('editText',    htmlentities($editText));
		$tpl->assign('valAuthor',   htmlentities($editUsername));
		$tpl->assign('valSummary',  htmlentities($editSummary));
		$tpl->assign('editStart',   $editStart);
	}
	
	/**
	 * Checks if the changes conflict to a version submited
	 * while editing.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return bool True if there is a conflict, false otherwise
	 **/
	function editConflicts($editText, $editUsername, $editSummary, $editStart)
	{
		$tpl = &singleton('template');
		
		if($this->page['page_version'] != $editStart) {
			$pageVersions = diff::getVersions();
			$orig         = $editStart;
			$final        = $this->page['page_version'];
			$finalText    = $this->page['page_text'];
			$origText     = diff::createVersion($this->page['page_text'], $pageVersions, $orig);
			
			$diff = diff::makeDiff($origText, $finalText, $pageVersions);
			$tpl->assign('diffOrig',   $diff['orig']);
			$tpl->assign('diffFinal',  $diff['final']);
			$tpl->assign('isConflict', true);
			$tpl->assign('editStart',  $this->time);
			$tpl->assign('editText',    htmlentities($editText));
			$tpl->assign('valAuthor',   htmlentities($editUsername));
			$tpl->assign('valSummary',  htmlentities($editSummary));
			$tpl->assign('isMessage',   true);
			$tpl->assign('message',     $this->lang['edit_conflicts']);
			
			$this->lang['history_original'] = sprintf($this->lang['history_original'], $orig, $final, $pageVersions[$final]['log_time']);
			$this->lang['history_final']    = sprintf($this->lang['history_final'],    $orig, $final, $pageVersions[$final]['log_time']);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Creates a changelog and version information
	 * for a edited page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int    $pageId   The page id
	 * @param  string $oldText  Old page text
	 * @param  string $newText  New page text
	 * @param  string $version  Old page version
	 * @param  int    $userId   The authors id (if logged in)
	 * @param  string $userName The authors name (if it is an unregistered author)
	 * @param  string $summary  Summary of changes
	 * @return string           New page version
	 **/
	function logChanges($pageId, $oldText, $newText, $version, $userId, $userName, $summary)
	{
		$db   = &singleton('database');
		
		$pageVersion = $version;
		$linesOld    = explode("\n", $newText);
		
		$diff = diff::getDiff($oldText, $newText);
			
		if(count($diff) > 0) {
			$oldLineCount     = count($linesOld);
			$changedLineCount = count($diff);
			
			$percentSteps   = $oldLineCount / 100;
			$percentChanged = $changedLineCount / $percentSteps;
			
			if($version != '') {
				$pageVersion = explode('.', $version);
			} else {
				$pageVersion = array(0, 0, 0);
			}
			
			if($percentChanged <= 5 || count($diff) <= 5) {
				$pageVersion[2]++;
			} elseif($percentChanged <= 25) {
				$pageVersion[1]++;
				$pageVersion[2] = 0;
			} else {
				$pageVersion[0]++;
				$pageVersion[1] = 0;
				$pageVersion[2] = 0;
			}
			
			$pageVersion = join('.', $pageVersion);
			
			$db->query('INSERT INTO '.DB_PREFIX.'changelog(log_page_id, '.
			'log_page_version, log_time, log_diff, log_user_id, log_user_name, '.
			'log_summary, log_ip) '.
			'VALUES('.$pageId.', \''.$pageVersion.'\', '.$this->time.', '.
			'\''.addslashes(serialize($diff)).'\', '.$userId.', '.
			'\''.addslashes($userName).'\', \''.addslashes($summary).'\', '.
			'\''.addslashes($this->server['REMOTE_ADDR']).'\')');
		}
		
		return $pageVersion;
	}
	
	/**
	 * Saves a page to the database
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int    $pageId        The page id
	 * @param  string $pageName      Current page name
	 * @param  string $namespace     The pages namespace
	 * @param  string $newText       New page text
	 * @param  string $oldText       The old page text
	 * @param  string $version       Old page version
	 * @param  int    $userId = 0    The authors id (if logged in)
	 * @param  string $userName = '' The authors name (if it is an unregistered author)
	 * @param  string $summary = ''  Summary of changes
	 * @return void
	 **/
	function savePageData($pageId, $pageName, $namespace, $newText, $oldText, $version, $userId = 0, $userName = '', $summary = '')
	{
		$db = &singleton('database');
		
		if($pageId > 0) {
			$pageVersion = $this->logChanges($pageId, $oldText, $newText, $version, $userId, $userName, $summary);
			
			$db->query('UPDATE '.DB_PREFIX.'pages '.
			'SET page_name = \''.addslashes($pageName).'\', '.
			'page_last_change = '.$this->time.', '.
			'page_version = \''.$pageVersion.'\' '.
			'WHERE page_id = '.$pageId);
			
			$pageCache = '';
			
			$db->query('UPDATE '.DB_PREFIX.'page_texts '.
			'SET page_text = \''.addslashes($newText).'\', '.
			'page_cache = \''.addslashes($pageCache).'\' '.
			'WHERE page_id = '.$pageId);
		} else {
			$db->query('INSERT INTO '.DB_PREFIX.'pages(page_namespace, page_name, '.
			'page_time, page_last_change, page_version) '.
			'VALUES(\''.addslashes($namespace).'\', \''.addslashes($pageName).'\', '.
			$this->time.', '.$this->time.', \'1.0.0\')');
			
			$page_id   = $db->insertId();
			$pageCache = '';
			
			
			$db->query('INSERT INTO '.DB_PREFIX.'page_texts(page_id, page_text, page_cache) '.
			'VALUES('.$page_id.', \''.addslashes($newText).'\', \''.addslashes($pageCache).'\')');
			
			$db->query('INSERT INTO '.DB_PREFIX.'changelog(log_page_id, '.
			'log_page_version, log_time, log_diff, log_user_id, log_user_name, '.
			'log_summary, log_ip) '.
			'VALUES('.$page_id.', \'1.0.0\', '.$this->time.', '.
			'\''.serialize(array()).'\', '.$userId.', '.
			'\''.addslashes($userName).'\', \''.addslashes($summary).'\', '.
			'\''.addslashes($this->server['REMOTE_ADDR']).'\')');
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