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
		$this->pageVersions = $this->getVersions();
		
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
		$pageUrl = $this->genUrl($this->getUniqueName($this->page), 'history', array('p' => '%s'));
		$pages   = $this->makePages($count, $this->cfg['items_per_page'], $pageUrl);
		
		$this->lang['wiki_pages'] = sprintf($this->lang['wiki_pages'], $pages[4], $pages[3]);
		
		$tpl->assign('pageLinks', $pages[0]);
		$tpl->assign('numPages',  $pages[3]);
		$tpl->assign('thisPage',  $pages[4]);
		$tpl->assign('firstPage', sprintf($pageUrl, 1));
		$tpl->assign('lastPage',  sprintf($pageUrl, $pages[3]));
		
		$tpl->assign('versions', array_slice($this->pageVersions, $pages[1], $pages[2]));
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
					$orig = $this->findPrevVersion($final);
					
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
			
			$diff = $this->makeDiff($orig, $final);
			$tpl->assign('diff_orig',  $diff['orig']);
			$tpl->assign('diff_final', $diff['final']);
			$this->historyTemplate = 'action_history_diff.tpl';
			
			$this->lang['history_original'] = sprintf($this->lang['history_original'], $orig);
			$this->lang['history_final']    = sprintf($this->lang['history_final'],    $final);
			return;
		} else {
			$this->opList();
		}
	}
	
	/**
	 * Finds the previos version number of a version.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $version Version number from which the previos should be found
	 * @return string          Previous version number
	 */
	function findPrevVersion($version)
	{
		$captureVersion = false;
		
		foreach($this->pageVersions as $key => $val)
		{
			if($captureVersion) {
				return $key;
			} elseif($key == $version) {
				$captureVersion = true;
			}
		}
		
		return false;
	}
	
	/**
	 * Creates the array which contains two compared page versions
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $orig  Original page version
	 * @param  string $final Final page version
	 * @return array         Page differences
	 **/
	function makeDiff($orig, $final)
	{
		$versions  = $this->pageVersions;
		$origText  = $this->page['page_text'];
		$finalText = $this->page['page_text'];
		
		$origText  = diff::createVersion($origText,  $versions, $orig);
		$finalText = diff::createVersion($finalText, $versions, $final);
		
		$diff = diff::getDiff($finalText, $origText);
		
		$origLines  = explode("\n", $origText);
		$finalLines = explode("\n", $finalText);
		$lineCount  = count($origLines) > count($finalLines) ? count($origLines) : count($finalLines);
		$origTextT  = array();
		$finalTextT = array();
		$ol         = 0;
		$fl         = 0;
		
		for($i = 0; $i <= $lineCount; $i++)
		{
			if(isset($diff[$i])) {
				$opType = $diff[$i][0];
				$opVal  = isset($diff[$i][1]) ? $diff[$i][1] : '';
				
				if($opType == '~') {
					$origTextT[]  = htmlentities($origLines[$ol]);
					$finalTextT[] = array('type' => 'edit', 'line' => htmlentities($opVal));
					$ol++;
					$fl++;
				} elseif($opType == '+') {
					$origTextT[]  = '';
					$finalTextT[] = array('type' => 'add', 'line' => htmlentities($opVal));
					$fl++;
				} else {
					$origTextT[]  = htmlentities($origLines[$ol]);
					$finalTextT[] = array('type' => 'subs', 'line' => htmlentities($origLines[$ol]));
					$ol++;
				}
			} else {
				if(isset($origLines[$ol])) {
					$origTextT[] = $origLines[$ol];
					$ol++;
				}
				
				if(isset($finalLines[$fl])) {
					$finalTextT[] = array('type' => 'none', 'line' => $finalLines[$fl]);
					$fl++;
				}
			}
		}
		
		return array('orig' => $origTextT, 'final' => $finalTextT);
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