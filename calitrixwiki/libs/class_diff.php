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
 * This file provides a extending class for the Text_Diff library.
 *
 * @author Johannes Klose <exe@calitrix.de>
 * @since 1.0 Beta 1 21.03.04 11:55
 **/

include $cfg['lib_dir'].'/diff/lib_diff.php';

/**
 * Common diff (diff/patch) functions.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class diff
{
	/**
	 * Computes the two textes and returns an array with the changes needed
	 * to trade back to the old text.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $text1 The old text
	 * @param  string $text2 The new text
	 * @return array         Differences between $text1 and $text2
	 **/
	function getDiff($text1, $text2)
	{
		$lines1 = explode("\n", $text1);
		$lines2 = explode("\n", $text2);
		
		$obj   = new Text_Diff($lines2, $lines1);
		$diff  = $obj->getDiff();
		$ndiff = array();
		$lines = 0;
		
		/**
		 * Take the array with the differences and strip
		 * informations (unchanged lines, old values on changed lines) 
		 * we do not need to store in the database to get from the
		 * new page version to the old one.
		 **/
		foreach($diff as $op)
		{
			if(strtolower(get_class($op)) == 'text_diff_op_copy') {
				$lines += count($op->orig);
				continue;
			} elseif(strtolower(get_class($op)) == 'text_diff_op_change') {
				if(count($op->orig) == count($op->final)) {
					foreach($op->final as $key => $val)
					{
						if(isset($op->orig[$key])) {
							$ndiff[$lines + $key] = array('~', $val);
						} else {
							$ndiff[$lines + $key] = array('+', $val);
						}
					}
				} elseif(count($op->orig) > count($op->final)) {
					foreach($op->orig as $key => $val)
					{
						if(isset($op->final[$key])) {
							$ndiff[$lines + $key] = array('~', $op->final[$key]);
						} else {
							$ndiff[$lines + $key] = array('-');
						}
					}
				} else {
					foreach($op->final as $key => $val)
					{
						if(isset($op->orig[$key])) {
							$ndiff[$lines + $key] = array('~', $op->final[$key]);
						} else {
							$ndiff[$lines + $key] = array('+', $op->final[$key]);
						}
					}
				}
			} elseif(strtolower(get_class($op)) == 'text_diff_op_add') {
				foreach($op->final as $key => $val)
				{
					$ndiff[$lines + $key] = array('+', $val);
				}
			} elseif(strtolower(get_class($op)) == 'text_diff_op_delete') {
				foreach($op->orig as $key => $val)
				{
					$ndiff[$lines + $key] = array('-');
				}
			}
			
			$lines += count($op->orig) > count($op->final) ? count($op->orig) : count($op->final);
		}
		
		return $ndiff;
	}
	
	/**
	 * Changes a text by an array with differences.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array  $text Text to patch
	 * @param  array  $diff Differences array which should be applied to $text
	 * @return string       Patched text
	 **/
	function patchText($text, $diff)
	{
		$lines   = explode("\n", $text);
		$patched = '';
		
		foreach($diff as $line => $op)
		{
			if($op[0] == '+') {
				if(isset($lines[$line])) {
					$start = array_slice($lines, 0, $line);
					$end   = array_slice($lines, $line, count($lines));
					$start[] = $op[1];
					
					$lines = array_merge($start, $end);
				} else {
					$lines[] = $op[1];
				}
			} elseif($op[0] == '~') {
				$lines[$line] = $op[1];
			} elseif($op[0] == '-') {
				unset($lines[$line]);
			}
		}
		
		return join("\n", $lines);
	}
	
	/**
	 * Creates the text of a specific page version.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $pageText Current page text
	 * @param  array  $versions All versions of the page
	 * @param  string $version  Final version
	 * @return string           Final text
	 **/
	function createVersion($pageText, &$versions, $version)
	{
		foreach($versions as $thisVersion => $log)
		{
			if($thisVersion == $version) {
				break;
			}
			
			$diff     = unserialize($log['log_diff']);
			$pageText = diff::patchText($pageText, $diff);
			$pageText = trim($pageText);
		}
		return $pageText;
	}
	
	/**
	 * Loads all versions of the current page from the database
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return array         Versions of the page
	 **/
	function getVersions()
	{
		$db     = &singleton('database');
		
		$versions = array();
		$result   = $db->query('SELECT l.log_page_version, l.log_time, l.log_diff, '.
		'l.log_user_id, l.log_user_name, l.log_summary, l.log_ip, u.user_name '.
		'FROM '.DB_PREFIX.'changelog l LEFT JOIN '.DB_PREFIX.'users u ON u.user_id = l.log_user_id '.
		'WHERE l.log_page_id = '.$this->page['page_id'].' '.
		'ORDER BY log_time DESC');
		
		while($row = $db->fetch($result))
		{
			if($row['log_user_name'] != '' && $row['user_name'] == '') {
				$row['user_name'] = $row['log_user_name'];
			}
			
			$row['user_name']   = htmlentities($row['user_name']);
			$row['log_summary'] = htmlentities($row['log_summary']);
			$row['log_ip']      = htmlentities($row['log_ip']);
			$row['log_time']    = $this->convertTime($row['log_time']);
			$row['view_title']  = sprintf($this->lang['history_view'], $row['log_page_version']);
			$versions[$row['log_page_version']] = $row;
		}
		
		return $versions;
	}
	
	/**
	 * Creates the array which contains two compared page versions
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $origText  Original page text
	 * @param  string $finalText Final page text
	 * @param  array  $versions  All page versions
	 * @return array         Page differences
	 **/
	function makeDiff($origText, $finalText, &$versions)
	{
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
					$origTextT[] = htmlentities($origLines[$ol]);
					$ol++;
				}
				
				if(isset($finalLines[$fl])) {
					$finalTextT[] = array('type' => 'none', 'line' => htmlentities($finalLines[$fl]));
					$fl++;
				}
			}
		}
		
		return array('orig' => $origTextT, 'final' => $finalTextT);
	}
	
	/**
	 * Finds the previous version number of a version.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $version  Version number from which the previous should be found
	 * @param  string $versions Versions array where the previous version should be found
	 * @return string           Previous version number
	 */
	function findPrevVersion($version, &$versions)
	{
		$captureVersion = false;
		
		foreach($versions as $key => $val)
		{
			if($captureVersion) {
				return $key;
			} elseif($key == $version) {
				$captureVersion = true;
			}
		}
		
		return false;
	}
}
?>