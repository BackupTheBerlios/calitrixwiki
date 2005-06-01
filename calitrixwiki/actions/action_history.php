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
 * This class displays a pages history or restores an old version.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class action_history extends core
{
	var $historyTemplate = '';
	var $pageVersions    = array();
	
	/**
	 * Constructor function.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array  &$page Page data
	 * @return void
	 **/
	function start()
	{
		$this->pageAction = $this->lang['history'];
		
		if(!$this->hasPerms(PERM_HISTORY)) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$tpl = &singleton('template');
		$this->pageVersions = diff::getVersions();
		
		if($this->page['page_id'] == 0) {
			$this->HTTPRedirect($this->genUrl($this->getUniqueName($this->page)));
		}
		
		if(isset($this->get['o'])) {
			$op = $this->get['o'];
		} else {
			$op = 'list';
		}
		
		switch($op)
		{
			case 'list': $this->opList(); break;
			case 'diff': $this->opDiff(); break;
			default:     $this->opList(); break;
		}
	}
	
	/**
	 * Displays a list of all page versions.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opList()
	{
		$tpl = &singleton('template');
		$db  = &singleton('database');
		
		$row     = $db->queryRow('SELECT COUNT(*) AS count '.
		'FROM '.DB_PREFIX.'changelog '.
		'WHERE log_page_id = '.$this->page['page_id']);
		$count   = $row['count'];
		$pageUrl = $this->genUrl($this->getUniqueName($this->page), 'history', array('p' => '%s'), true, true);
		$pages   = $this->makePages($count, $this->cfg['items_per_page'], $pageUrl);
		
		$this->lang['wiki_pages'] = sprintf($this->lang['wiki_pages'], $pages[4], $pages[3]);
		
		$tpl->assign('pageLinks', $pages[0]);
		$tpl->assign('numPages',  $pages[3]);
		$tpl->assign('thisPage',  $pages[4]);
		$tpl->assign('firstPage', sprintf($pageUrl, 1));
		$tpl->assign('lastPage',  sprintf($pageUrl, $pages[3]));
		
		$versions = array_slice($this->pageVersions, $pages[1], $pages[2]);
		
		foreach($versions as $key => $val)
		{
			$versions[$key]['user_name_raw'] = $versions[$key]['user_name'];
			$versions[$key]['user_name']     = htmlentities($versions[$key]['user_name']);
			$versions[$key]['log_summary']   = htmlentities($versions[$key]['log_summary']);
			$versions[$key]['log_ip']        = htmlentities($versions[$key]['log_ip']);
			$versions[$key]['log_time']      = $this->convertTime($versions[$key]['log_time']);
		}
		
		$tpl->assign('versions', $versions);
		$this->historyTemplate = 'action_history.tpl';
		return;
	}
	
	/**
	 * This function displays the difference between two page versions.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opDiff()
	{	
		$tpl = &singleton('template');
		
		if(isset($this->get['orig'], $this->get['final'])) {
			$orig  = $this->get['orig'];
			$final = $this->get['final'];
			
			if(!preg_match('/^\d+\.\d+\.\d+$/', $orig) || !preg_match('/^\d+\.\d+\.\d+$/', $final)) {
				if(preg_match('/^\d+\.\d+\.\d+$/', $final)) {
					$orig = diff::findPrevVersion($final, $this->pageVersions);
					
					if($orig === false) {
						$this->opList();
						return;
					}
				} else {
					$this->opList();
					return;
				}
			}
			
			if(!isset($this->pageVersions[$orig]) || !isset($this->pageVersions[$final])) {
				$this->opList();
				return;
			}
			
			$origText  = diff::createVersion($this->page['page_text'], $this->pageVersions, $orig);
			$finalText = diff::createVersion($this->page['page_text'], $this->pageVersions, $final);
			
			$diff = diff::makeDiff($origText, $finalText, $this->pageVersions);
			$tpl->assign('diff_orig',  $diff['orig']);
			$tpl->assign('diff_final', $diff['final']);
			$this->historyTemplate = 'action_history_diff.tpl';
			
			$this->lang['history_original'] = sprintf($this->lang['history_original'], $orig, $final, $this->pageVersions[$final]['log_time']);
			$this->lang['history_final']    = sprintf($this->lang['history_final'],    $orig, $final, $this->pageVersions[$final]['log_time']);
			return;
		} else {
			$this->opList();
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
		return $this->historyTemplate;
	}
}
?>