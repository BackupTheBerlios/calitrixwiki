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
 * This is the search specialpage. It Provides the main search function.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_search extends core
{
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param object &$core Core class object
	 * @return void
	 **/
	function start()
	{
		$parser    = &singleton('parser');
		$tpl       = &singleton('template');
		$db        = &singleton('database');
		
		$query = isset($this->get['q']) && trim($this->get['q']) != ''  ? $this->get['q']  : '';
		$query = urldecode($query);
		$sw    = isset($this->get['sw'])                                ? $this->get['sw'] : 'ft';
		
		if($sw != 'ft' && $sw != 'title') {
			$sw = 'ft';
		}
		
		if($query == '') {
			$tpl->assign('search', false);
			return;
		}
		
		$tpl->assign('search', true);
		
		$sql    = $this->makeSearchSql($query, $sw);
		$count  = $this->getSearchResultCount($query, $sw, $sql[1]);
		$pages  = array();
		$result = $db->query($sql[0].' LIMIT '.$count[1].','.$count[2]);
		
		while($row = $db->fetch($result))
		{
			$row['page_text']  = $parser->stripCodes($row['page_text']);
			$row['page_text']  = substr($row['page_text'], 0, $this->cfg['teaser_length']).'...';
			$row['page_name']  = $this->getUniqueName($row);
			$row['page_title'] = $row['page_name'];
			//$row['page_text']  = $this->summarizeText($row['page_text'], $query, '<span class="highlight">%s</span>', $this->cfg['teaser_length']);
			
			$pages[] = $row;
		}
		
		$this->lang['search_results'] = sprintf($this->lang['search_results'], $count[0]);
		$tpl->assign('searchResult', $pages);
	}
	
	/**
	 * Generates the WHERE clause for the search.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $query Search query
	 * @param  string $sw    Additional search parameter
	 * @return string        WHERE clause
	 **/
	function makeSearchSql($query, $sw)
	{
		$sql1       = '';
		$sql2       = '';
		$where      = '';
		$namespace  = '';
		
		$tmp = explode(':', $query);
		
		if(count($namespace) > 1 && in_array($namespace[0], $this->cfg['namespaces'])) {
			$namespace = $namespace[0];
		}
		
		if($sw == 'title') {
			if($namespace != '') {
				$where .= 'WHERE p.page_namespace = "'.addslashes($namespace).'" AND p.page_name LIKE "%'.addslashes($query).'%" ';
			} else {
				$where .= 'WHERE p.page_name LIKE "%'.addslashes($query).'%" ';
			}
			
			$where .= 'AND (m.perm_access_mask IS NULL OR '.PERM_VIEW.' & m.perm_access_mask = '.PERM_VIEW.')';
			
			$sql1 .= 'SELECT p.page_id, p.page_namespace, p.page_name, pt.page_text '.
			         'FROM '.DB_PREFIX.'pages p LEFT JOIN '.DB_PREFIX.'page_texts pt ON pt.page_id = p.page_id '.
			         'LEFT JOIN '.DB_PREFIX.'local_masks m ON m.perm_page_id = p.page_id '.
			         'AND m.perm_group_id = '.$this->user['group_id'].' '.$where.' ORDER BY p.page_name';
			
			$sql2 .= 'SELECT COUNT(*) as count FROM '.DB_PREFIX.'pages p '.
			         'LEFT JOIN '.DB_PREFIX.'page_texts pt ON pt.page_id = p.page_id '.
			         'LEFT JOIN '.DB_PREFIX.'local_masks m ON m.perm_page_id = p.page_id '.
			         'AND m.perm_group_id = '.$this->user['group_id'].' '.$where.' ORDER BY p.page_name';
		} else {
			$where .= 'WHERE MATCH(pt.page_text) AGAINST(\''.addslashes($query).'\') ';
			
			if($namespace != '') {
				$where .= 'AND p.page_namespace = "'.addslashes($namespace).'" ';
			}
			
			$where .= 'AND (m.perm_access_mask IS NULL OR '.PERM_VIEW.' & m.perm_access_mask = '.PERM_VIEW.')';
			
			$sql1 .= 'SELECT pt.page_text, p.page_id, p.page_namespace, p.page_name, '.
			         'MATCH(pt.page_text) AGAINST(\''.addslashes($query).'\') as relevancy '.
			         'FROM '.DB_PREFIX.'page_texts pt LEFT JOIN '.DB_PREFIX.'pages p ON p.page_id = pt.page_id '.
			         'LEFT JOIN '.DB_PREFIX.'local_masks m ON m.perm_page_id = p.page_id '.
			         'AND m.perm_group_id = '.$this->user['group_id'].' '.$where.' ORDER BY relevancy DESC';
			
			$sql2 = 'SELECT COUNT(*) as count FROM '.DB_PREFIX.'page_texts pt '.
			        'LEFT JOIN '.DB_PREFIX.'pages p ON p.page_id = pt.page_id '.
			        'LEFT JOIN '.DB_PREFIX.'local_masks m ON m.perm_page_id = p.page_id '.
			        'AND m.perm_group_id = '.$this->user['group_id'].' '.$where;
		}
		
		return array($sql1, $sql2);
	}
	
	/**
	 * Gets the result count for a search query
	 * and assigns the variables for the page_link.tpl template.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $query Search query
	 * @param  string $sw    Additional search parameter
	 * @param  string $sql   Sql WHERE clause
	 * @return array         Number of search results and sql limits
	 **/
	function getSearchResultCount($query, $sw, $sql)
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		$result = $db->query($sql);
		$row     = $db->fetch($result);
		$count   = $row['count'];
		$query   = str_replace('%', '%%', urlencode($query));
		$pageUrl = $this->genUrl($this->getUniqueName($this->page), '', array('q' => $query, 'sw' => $sw, 'p' => '%s'));
		$pages   = $this->makePages($count, $this->cfg['items_per_page'], $pageUrl);
		
		$this->lang['wiki_pages'] = sprintf($this->lang['wiki_pages'], $pages[4], $pages[3]);
		
		$tpl->assign('pageLinks', $pages[0]);
		$tpl->assign('numPages',  $pages[3]);
		$tpl->assign('thisPage',  $pages[4]);
		$tpl->assign('firstPage', sprintf($pageUrl, 1));
		$tpl->assign('lastPage',  sprintf($pageUrl, $pages[3]));
		
		return array($count, $pages[1], $pages[2]);
	}
	
	/**
	 * Summarizes a text to the matching words.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $text          Text which should be summarized
	 * @param  mixed  $query         The search query, either as array of search words or query string
	 * @param  string $highlight     Highlight template with a %s spacer for the search word
	 * @param  int    $teaserLength  Maximum length of the teaser (defaults to 300)
	 * @param  int    $minGap        Minimum of characters as space between two extracts
	 * @param  int    $minWordLength Minimum length of a search word (defaults to 3)
	 * @return mixed                 Googlified text as string or boolean false on failure
	 *
	function summarizeText($text, $query, $highlight, $teaserLength = 400, $minGap = 5, $minWordLength = 3)
	{
		if(!is_array($query)) {
			$query    = preg_replace('/[^\w\s]/',                            ' ', $query);
			$query    = preg_replace('/\b\w{0,'.($minWordLength - 1).'}\b/', ' ', $query);
			$query    = preg_replace('/\s/',                                 ' ', $query);
			$query    = preg_replace('/\s{2,}/',                             ' ', $query);
			$words    = array_unique(explode(' ', trim($query)));
			$numWords = count($words);
		} else {
			$words    = $query;
			$numWords = count($words);
		}
		
		if($numWords <= 0) {
			return false;
		}
		
		$loText   = strtolower($text);
		$wordPos  = array();
		
		foreach($words as $word)
		{
			$word = strtolower($word);
			$pos  = strpos($loText, $word);
			
			if($pos !== false) {
				$wordPos[$word] = $pos;
			}
		}
		
		$numWords = count($wordPos);
		
		if($numWords <= 0) {
			return false;
		}
		
		asort($wordPos, SORT_NUMERIC);
		reset($wordPos);
		
		$pre      = round(((($teaserLength / $numWords) / 2) - 6 + $minGap), 0);
		$pre      = $pre < 0 ? 0 : $pre;
		$searchHi = '/('.join('|', $words).')/ie';
		$result   = '';
		$textLen  = strlen($text);
		
		list($key, $value) = each($wordPos);
		$lastOffset = -$minGap;
		
		for($i = $value; $i < $textLen; $i++)
		{
			$start = $i - $pre;
			
			if($start < ($lastOffset + $minGap)) {
				$start = $lastOffset + $minGap;
			}
			
			$start  = $start < 0 ? 0 : $start;
			$length = strlen($key) + ($pre * 2);
			
			$tmp      = ' ... '.substr($text, $start, $length);
			$result  .= preg_replace($searchHi, 'sprintf($highlight, stripslashes("\1"))', $tmp);
			list($key, $value) = each($wordPos);
			
			if(!$key) {
				break;
			} else {
				$lastOffset = $start + $length;
				$i = $value;
			}
		}
		
		return $result.' ...';
	}*/
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'special_search.tpl';
	}
}
?>