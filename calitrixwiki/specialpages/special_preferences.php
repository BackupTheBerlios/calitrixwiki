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
 * This is the preferences specialpage. Registered users can edit
 * their details and settings here.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_preferences extends core
{
	var $prefsTemplate = 'special_preferences.tpl';
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		if(!$this->loggedIn) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$tpl = &singleton('template');
		$tpl->assign('isMessage', false);
		$tpl->assign('isError',   false);
		
		$op = isset($this->get['op']) ? $this->get['op'] : '';
		
		switch($op)
		{
			case 'details':   $this->opDetails();   break;
			case 'prefs':     $this->opPrefs();     break;
			case 'bookmarks': $this->opBookmarks(); break;
		}
	}
	
	/**
	 * Starting function for the details page.
	 * Decides wether to display or to save the 
	 * users details.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opDetails()
	{
		$tpl    = &singleton('template');
		
		$tpl->assign('emailValue', htmlentities($this->user['user_email']));
		$tpl->assign('isMessage',  false);
		
		$this->prefsTemplate = 'special_preferences_details.tpl';
		
		if($this->request == 'POST' && isset($this->post['change'])) {
			$change = $this->post['change'];
			
			if($change == 'email') {
				$this->opDetailsChangeEmail();
			} elseif($change == 'password') {
				$this->opDetailsChangePassword();
			}
		}
	}
	
	/**
	 * Changes the email adress ...
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opDetailsChangeEmail()
	{
		$tpl    = &singleton('template');
		$db     = &singleton('database');
		$email  = isset($this->post['email']) ? $this->post['email'] : '';
		
		if(!preg_match($this->cfg['match_email'], $email)) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  array($this->lang['prefs_details_invalid_email']));
		} else {
			$db->query('UPDATE '.DB_PREFIX.'users '.
			'SET user_email = "'.addslashes($email).'" '.
			'WHERE user_id = '.$this->user['user_id']);
			
			$tpl->assign('emailValue', htmlentities($email));
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['prefs_details_updated']);
		}
	}
	
	/**
	 * Validates and sets a new password ..
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opDetailsChangePassword()
	{
		$tpl = &singleton('template');
		$db  = &singleton('database');
		
		$oldPassword = isset($this->post['old_password']) ? $this->post['old_password'] : '';
		$newPassword = isset($this->post['new_password']) ? $this->post['new_password'] : '';
		$newConfirm  = isset($this->post['new_confirm'])  ? $this->post['new_confirm']  : '';
		$isError     = false;
		$errors      = array();
		
		if(sha1($oldPassword) != $this->user['user_password']) {
			$isError  = true;
			$errors[] = $this->lang['prefs_details_invalid_pw'];
		} elseif(strlen($newPassword) < $this->cfg['min_password_length']) {
			$isError  = true;
			$errors[] = sprintf($this->lang['prefs_details_short_pw'], $this->cfg['min_password_length']);
		} elseif($newPassword != $newConfirm) {
			$isError  = true;
			$errors[] = $this->lang['prefs_details_different_pws'];
		}
		
		if($isError) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  $errors);
		} else {
			$db->query('UPDATE '.DB_PREFIX.'users '.
			'SET user_password = "'.sha1($newPassword).'" '.
			'WHERE user_id = '.$this->user['user_id']);
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['prefs_details_updated']);
			
			$autoCookieUser = $this->cfg['cookie_prefix'].'autologin_user';
			$autoCookiePass = $this->cfg['cookie_prefix'].'autologin_pass';
		
			if(isset($this->cookie[$autoCookieUser]) && isset($this->cookie[$autoCookiePass])) {
				$this->setAutoLoginCookie($this->user['user_id'], sha1($newPassword));
			}
		}
	}
	
	/**
	 * Starting function for the bookmarks page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opBookmarks()
	{
		$this->prefsTemplate = 'special_preferences_bookmarks.tpl';
		
		if($this->request == 'POST') {
			$so = isset($this->post['so']) ? $this->post['so'] : '';
			
			if($so == 'add') {
				$this->opBookmarksAdd();
			} elseif($so == 'change') {
				$this->opBookmarksChange();
			}
		}
		
		$tpl = &singleton('template');
		$tpl->assign('bookmarks', $this->opBookmarksLoad());
		$this->lang['prefs_bookmarks_info'] = sprintf($this->lang['prefs_bookmarks_info'],
		                                      $this->convertTime($this->user['user_last_visit']));
	}
	
	/**
	 * Adds a page to the bookmarks list.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opBookmarksAdd()
	{
		$tpl = &singleton('template');
		$db  = &singleton('database');
		
		if(!isset($this->post['page_name'])) {
			return;
		}
		
		$pageName = trim($this->post['page_name']);
		
		if(preg_match('/^([A-Z][a-z]+:)?(([A-Z][A-Za-z0-9_]+)+)$/', $pageName, $match)) {
			$match[1] = substr($match[1], 0, strlen($match[1]) - 1);
			$page = $this->getPage($match[2], true, $match[1]);
		} else {
			$page = array('page_id' => 0);
		}
		
		if($page['page_id'] < 1) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  array($this->lang['prefs_bookmarks_no_page']));
		} else {
			$result = $db->query('SELECT bm_page_id FROM '.DB_PREFIX.'bookmarks '.
			'WHERE bm_user_id = '.$this->user['user_id'].' AND bm_page_id = '.$page['page_id']);
			
			if($db->numRows($result) > 0) {
				$tpl->assign('isMessage', true);
				$tpl->assign('message', $this->lang['wiki_page_already_bookmarked']);
			} else {
				$db->query('INSERT INTO '.DB_PREFIX.'bookmarks(bm_user_id, bm_page_id) '.
				'VALUES('.$this->user['user_id'].', '.$page['page_id'].')');
				
				$tpl->assign('isMessage', true);
				$tpl->assign('message', $this->lang['wiki_page_bookmarked']);
			}
		}
	}
	
	/**
	 * Deletes or unmarks bookmarks.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opBookmarksChange()
	{
		$tpl     = &singleton('template');
		$db      = &singleton('database');
		$pageIds = isset($this->post['pid']) && is_array($this->post['pid']) ? $this->post['pid'] : array();
		$do      = isset($this->post['change_action']) ? $this->post['change_action'] : '';
		
		if(count($pageIds) < 1 || ($do != 'del' && $do != 'delmark')) {
			return;
		}
		
		$validPageIds = array();
		
		foreach($pageIds as $id)
		{
			$id = intval($id);
			
			if($id > 0) {
				$validPageIds[] = $id;
			}
		}
		
		if($do == 'del' && count($validPageIds) > 0) {
			$db->query('DELETE FROM '.DB_PREFIX.'bookmarks '.
			'WHERE bm_user_id = '.$this->user['user_id'].' '.
			'AND bm_page_id IN('.join(', ', $validPageIds).')');
		} elseif($do == 'delmark' && count($validPageIds) > 0) {
			$db->query('UPDATE '.DB_PREFIX.'bookmarks '.
			'SET bm_mark_old = '.$this->time.' '.
			'WHERE bm_user_id = '.$this->user['user_id'].' '.
			'AND bm_page_id IN('.join(', ', $validPageIds).')');
		} else {
			return;
		}
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['prefs_bookmarks_changed']);
	}
	
	/**
	 * Loads the current page of bookmarks from the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return array Bookmarks
	 **/
	function opBookmarksLoad()
	{
		$db     = &singleton('database');
		$tpl    = &singleton('template');
		$parser = &singleton('parser');
		
		$row     = $db->queryRow('SELECT COUNT(*) AS count FROM '.DB_PREFIX.'bookmarks '.
		'WHERE bm_user_id = '.$this->user['user_id']);
		$count   = $row['count'];
		$pageUrl = $this->genUrl($this->getUniqueName($this->page), '', array('op' => 'bookmarks', 'p' => '%s'), true, true);
		$pages   = $this->makePages($count, $this->cfg['items_per_page'], $pageUrl);
		
		$this->lang['wiki_pages'] = sprintf($this->lang['wiki_pages'], $pages[4], $pages[3]);
		
		$tpl->assign('pageLinks', $pages[0]);
		$tpl->assign('numPages',  $pages[3]);
		$tpl->assign('thisPage',  $pages[4]);
		$tpl->assign('firstPage', sprintf($pageUrl, 1));
		$tpl->assign('lastPage',  sprintf($pageUrl, $pages[3]));
		
		$bookmarks = array();
		$result    = $db->query('SELECT b.bm_mark_old, p.page_id, p.page_namespace, p.page_name, '.
		'p.page_version, p.page_last_change '.
		'FROM '.DB_PREFIX.'bookmarks b LEFT JOIN '.DB_PREFIX.'pages p ON p.page_id = b.bm_page_id '.
		'WHERE b.bm_user_id = '.$this->user['user_id'].' ORDER BY p.page_last_change DESC '.
		'LIMIT '.$pages[1].','.$pages[2]);
		
		while($row = $db->fetch($result))
		{
			if($row['page_last_change'] > $this->user['user_last_visit'] && $row['bm_mark_old'] < $this->user['user_last_visit']) {
				$row['mark_new'] = true;
			} else {
				$row['mark_new'] = false;
			}
			
			$row['page_name_raw']    = $this->getUniqueName($row);
			$row['page_name']        = htmlentities(str_replace('_', ' ', $this->getUniqueName($row)));
			$row['page_last_change'] = $this->convertTime($row['page_last_change']);
			
			$bookmarks[] = $row;
		}
		
		return $bookmarks;
	}
	
	/**
	 * Start function for the preferences page. Registered
	 * users can edit preferences like ui language or theme there.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opPrefs()
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		$this->prefsTemplate = 'special_preferences_prefs.tpl';
		
		// Check if the user subscribed for notifications about edits
		$result = $db->query('SELECT * FROM '.DB_PREFIX.'subscriptions '.
		'WHERE subs_user_id = '.$this->user['user_id']);
		
		if($db->numRows($result) == 1) {
			$tpl->assign('subsChecked', true);
			$subs = 1;
		} else {
			$tpl->assign('subsChecked', false);
			$subs = 0;
		}
		
		if($this->request == 'POST' && isset($this->post['change'])) {
			$do = $this->post['change'];
			
			switch($do)
			{
				case 'interface': $this->opPrefsInterface();    break;
				case 'mailing':   $this->opPrefsMailing($subs); break;
				case 'misc':      $this->opPrefsMisc();         break;
			}
		}
	}
	
	/**
	 * Changes the interface settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opPrefsInterface()
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		$lang     = isset($this->post['language']) ? $this->post['language'] : '';
		$theme    = isset($this->post['theme'])    ? $this->post['theme']    : '';
		$items_pp = isset($this->post['items_pp']) ? $this->post['items_pp'] : '';
		$update   = false;
		$items_pp = intval($items_pp);
		
		if($lang != $this->user['user_language'] && (isset($this->cfg['languages'][$lang]) || $lang == '')) {
			$update = true;
		} else {
			$lang = $this->user['user_language'];
		}
		
		if($theme != $this->user['user_theme'] && (isset($this->cfg['themes'][$theme]) || $theme == '')) {
			$update = true;
		} else {
			$theme = $this->user['user_theme'];
		}
		
		if($items_pp != $this->user['user_items_pp'] && (in_array($items_pp, $this->cfg['items_pp_select']) || $items_pp == 0)) {
			$update = true;
		} else {
			$items_pp = $this->user['user_items_pp'];
		}
		
		if($update) {
			$db->query('UPDATE '.DB_PREFIX.'users '.
			'SET user_language = "'.addslashes($lang).'", '.
			'user_theme = "'.addslashes($theme).'", '.
			'user_items_pp = '.$items_pp.' '.
			'WHERE user_id = '.$this->user['user_id']);
			
			$this->user['user_language'] = $lang;
			$this->user['user_theme']    = $theme;
			$this->user['user_items_pp'] = $items_pp;
			
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['prefs_prefs_updated']);
		}
	}
	
	/**
	 * Changes the mailing settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $subsEnabled Sets wether or not the user subscribed to edit notifications
	 * @return void
	 **/
	function opPrefsMailing($subsEnabled)
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		$enableSubs  = isset($this->post['enable_subs'])  ? 1 : 0;
		$receiveNews = isset($this->post['receive_news']) ? 1 : 0;
		$update      = false;
		
		if($enableSubs == 1 && $subsEnabled == 0) {
			$db->query('INSERT INTO '.DB_PREFIX.'subscriptions(subs_user_id) '.
			'VALUES('.$this->user['user_id'].')');
			$update = true;
		} elseif($enableSubs == 0 && $subsEnabled == 1) {
			$db->query('DELETE FROM '.DB_PREFIX.'subscriptions '.
			'WHERE subs_user_id = '.$this->user['user_id']);
			$update = true;
		}
		
		if($receiveNews != $this->user['user_enable_mails']) {
			$db->query('UPDATE '.DB_PREFIX.'users '.
			'SET user_enable_mails = "'.$receiveNews.'" '.
			'WHERE user_id = '.$this->user['user_id']);
			$update = true;
		}
		
		if($update) {
			$this->user['user_enable_mails'] = $receiveNews;
			$tpl->assign('subsChecked', $enableSubs == 1 ? true : false);
			$tpl->assign('isMessage',   true);
			$tpl->assign('message',     $this->lang['prefs_prefs_updated']);
		}
	}
	
	/**
	 * Changes misc. settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opPrefsMisc()
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		$useCookies = isset($this->post['use_cookies'])      ? 1 : 0;
		$dblEditing = isset($this->post['dblclick_editing']) ? 1 : 0;
		$update     = false;
		
		if($useCookies == 0 && $this->user['user_use_cookies'] == 1) {
			$this->removeSessionCookie();
			$this->sid = 's='.$this->session['session_id'];
			$update = true;
		} elseif($useCookies == 1 && $this->user['user_use_cookies'] == 0) {
			$this->setSessionCookie($this->session['session_id']);
			$this->sid = '';
			$update = true;
		}
		
		if($dblEditing != $this->user['user_dblclick_editing']) {
			$update = true;
		}
		
		if($update) {
			$db->query('UPDATE '.DB_PREFIX.'users '.
			'SET user_use_cookies = "'.$useCookies.'", '.
			'user_dblclick_editing = "'.$dblEditing.'" '.
			'WHERE user_id = '.$this->user['user_id']);
			
			$this->user['user_use_cookies']      = $useCookies;
			$this->user['user_dblclick_editing'] = $dblEditing;
			
			$tpl->assign('isMessage',   true);
			$tpl->assign('message',     $this->lang['prefs_prefs_updated']);
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
		return $this->prefsTemplate;
	}
}
?>