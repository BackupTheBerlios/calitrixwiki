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
 * This is the "view"-Action. It simply parses the WikiPage and 
 * assigns it to the template.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class action_view extends core
{
	var $viewTemplate = 'action_view.tpl';
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		if(!$this->hasPerms(PERM_VIEW)) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$tpl    = &singleton('template');
		$parser = &singleton('parser');
		
		$page = $this->page;
		
		$tpl->assign('isOldVersion', false);
		
		// If the user wants to view an old version we check
		// if he has the right to do so and display the old text.
		if(isset($this->get['v']) && preg_match('/^\d+\.\d+\.\d+$/', $this->get['v'])) {
			if(!$this->hasPerms(PERM_HISTORY)) {
				$this->messageEnd('wiki_perm_denied');
			}
			
			$oldVersion = $this->getOldPageText($this->get['v']);
			
			if($oldVersion !== false) {
				$tpl->assign('isOldVersion', true);
				$tpl->assign('versionDesc', sprintf($this->lang['wiki_old_version'], $oldVersion['version'], $oldVersion['time']));
				$page['page_text'] = $oldVersion['text'];
			}
		}
		
		$page['page_text'] = $parser->parseText($page);
		
		// If the client was redirected to this page we create
		// a backlink to the edit form of the referring page.
		if(isset($this->get['redirect'])) {
			if(preg_match('/^((([A-Z][a-z0-9_]+)+)+)$/', $this->get['redirect'])) {
				$url   = sprintf($this->cfg['url_format'], $this->get['redirect'], 'edit');
				$link  = '<a href="'.$url.'" class="wiki-internal">'.$this->get['redirect'].'</a>';
				$alert = sprintf($this->lang['wiki_redirected'], $link);
				$page['page_text'] = '<span class="light-grey">'.$alert.'</span><br /><br />'.$page['page_text'];
			}
		}
		
		$tpl->assign('pageText',  $page['page_text']);
		$tpl->assign('isMessage', false);
		
		// Now we check if there is some additional stuff to be done.
		if($this->pageInfo['action'] == 'bookmark') {
			$this->bookmarkPage();
		} elseif($this->pageInfo['action'] == 'print') {
			$this->viewTemplate = 'action_print.tpl';
		}
		
		if(!headers_sent()) {
			header('Content-Disposition: inline; filename='.$this->page['page_name'].'.html');
		}
	}
	
	/**
	 * Bookmarks a page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function bookmarkPage()
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		if(!$this->loggedIn) {
			$this->messageEnd('wiki_perm_denied');
		} elseif($this->page['page_id'] < 1) {
			return;
		}
		
		$result = $db->query('SELECT bm_page_id FROM '.DB_PREFIX.'bookmarks '.
		'WHERE bm_user_id = '.$this->user['user_id'].' AND bm_page_id = '.$this->page['page_id']);
		
		if($db->numRows($result) > 0) {
			$tpl->assign('isMessage', true);
			$tpl->assign('message', $this->lang['wiki_page_already_bookmarked']);
		} else {
			$db->query('INSERT INTO '.DB_PREFIX.'bookmarks(bm_user_id, bm_page_id) '.
			'VALUES('.$this->user['user_id'].', '.$this->page['page_id'].')');
			
			$tpl->assign('isMessage', true);
			$tpl->assign('message', $this->lang['wiki_page_bookmarked']);
		}
	}
	
	/**
	 * Returns the template name for this action.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return $this->viewTemplate;
	}
}
?>