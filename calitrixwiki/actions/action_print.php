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
 * This is the "printv"-Action. It parses a WikiPage and displays
 * it with the print template
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class action_print extends core
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
		$this->pageAction = $this->lang['print_page'];
		
		if(!$this->hasPerms(PERM_VIEW)) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$parser = &singleton('parser');
		$tpl    = &singleton('template');
		
		$tpl->assign('printText', $parser->parseText($this->page));
	}
	
	/**
	 * Returns the template name for this action.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'action_print.tpl';
	}
}
?>
