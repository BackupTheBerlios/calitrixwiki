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
 * Parser class.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class parser
{
	var $preformatedTexts = array();
	var $noParseSections  = array();
	var $pageName         = '';
	var $wikiStyles       = array();
	var $headlines        = array();
	var $userVars         = array();
	var $interWiki        = array();
	var $headings         = array();
	var $existingPages    = array();
	var $linkedPages      = array();
	
	/** 
	 * Constructor; inits some variables.
	 * 
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function parser()
	{
		global $wiki;
		
		$thispage                   = $wiki->cfg['thispage_interwiki'];
		$thiswiki                   = $wiki->cfg['thiswiki_interwiki'];
		$this->interWiki            = $wiki->cfg['interwiki'];
		$this->interWiki[$thiswiki] = $wiki->cfg['url_format_short'];
		$this->interWiki[$thispage] = '';
	}
	 
	/**
	 * Creates the this->userVars array.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array $page Page date of the currently parsed page
	 * @return void
	 **/
	function createUserVars(&$page)
	{
		global $wiki;
		
		$this->userVars['WikiTitle']        = $wiki->cfg['wiki_title'];
		$this->userVars['PageName']         = $page['page_name'];
		$this->userVars['PageNameSpaced']   = $this->spaceWikiWord($this->userVars['PageName']);
		$this->userVars['PageVersion']      = $page['page_version'];
		$this->userVars['PageLastModified'] = $wiki->convertTime($page['page_last_change']);
		$this->userVars['DefaultPage']      = $wiki->cfg['default_page'];
		
		$this->userVars['CurrentMinute']    = date('i',         $wiki->time);
		$this->userVars['CurrentHour']      = date('H',         $wiki->time);
		$this->userVars['CurrentDay']       = date('d',         $wiki->time);
		$this->userVars['CurrentMonth']     = date('m',         $wiki->time);
		$this->userVars['CurrentYear']      = date('y',         $wiki->time);
		$this->userVars['CurrentDate']      = date('d.m.y',     $wiki->time);
		$this->userVars['CurrentTime']      = date('H:i',       $wiki->time);
		$this->userVars['CurrentDateTime']  = date('d.m.y H:i', $wiki->time);
		$thispage                           = $wiki->cfg['thispage_interwiki'];
		$this->interWiki[$thispage]         = $wiki->genUrl($page['page_name']).'%s';
	}
	
	/**
	 * This is the main parsing function which parses all WikiPages.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array $page Page data
	 * @return string      Parsed text
	 **/
	function parseText($page)
	{
		// Maybe this function should be split up into several smaller ones...
		global $wiki;
		
		$this->pageName  = $wiki->getUniqueName($page);
		$this->createUserVars($page);
		$this->wikiStyles = $wiki->cfg['wiki_styles'];
		
		$text = htmlspecialchars($page['page_text']);
		$text = preg_replace('/\[==(.+?)==\]/se',
		                     '$this->stripNoParseSection(\'\1\')',
		                     $text); // Strip sections which are marked to be ignored by the parser
		$text = preg_replace('/@@@(.+?)@@@/se',
		                     '$this->stripPreformated(\'\1\')',
		                     $text);  // Strip sections which shall appear as entered in the form.
		
		// After the ignored sections are stripped from the text we'll
		// have a look if there is a REDIRECT tag left somewhere in the text.
		if(preg_match('/\[REDIRECT '.$wiki->cfg['title_format'].'\]/', $text, $match)) {
			if($wiki->pageInfo['action'] == 'view') {
				$url = $wiki->genUrl($match[1].$match[2], '', array('redirect' => $wiki->getUniqueName($page)), false);
				$wiki->HTTPRedirect($url);
			}
		}
		
		$text = preg_split('/(\r\n\r\n|\n\n|\r\r)/', $text);
		$text = "<p>\n".join("\n</p>\n<p>\n", $text)."\n</p>";
		
		$text = preg_replace('/%define=([A-Za-z0-9_-]+) ((([a-z-]+):(.+?);?)+)%(\r\n|\n|\r)?/se',
		                     '$this->defineWikiStyle(\'\1\', \'\2\')',
		                     $text); // Extract wiki style classes ...
		
		// Now let us rush through the basic markups. Headings, emphasis, ...
		$text = preg_replace('/^(={2,4})(.+?)(\1)(?=$|\r\n|\n|\r)/me',
		                     '$this->createHeading(\'\1\', \'\2\')',
		                     $text);  // Headings
		
		$text = preg_replace('/&lt;br( \/)?&gt;/',                    '<br />',                              $text);  // Explizit line breaks ...
		$text = preg_replace('/^----+/m',                             '<hr />',                              $text);  // Horizontal ruler
		$text = preg_replace('/^-&gt;&lt;-(.+?)$/m',                  '<div class="wiki-centered">\1</div>', $text);  // Centered text
		$text = preg_replace('/^-&gt;(.+?)$/m',                       '<div class="wiki-right">\1</div>',    $text);  // Right-aligned text
		$text = preg_replace('/^&lt;-(.+?)$/m',                       '<div class="wiki-left">\1</div>',     $text);  // Left-aligned text
		$text = preg_replace('/\'\'\'\'\'(.+?)\'\'\'\'\'/s',          '<strong><em>\1</em></strong>',        $text);  // Double emphasis
		$text = preg_replace('/\'\'\'(.+?)\'\'\'/s',                  '<strong>\1</strong>',                 $text);  // Strong emphasis
		$text = preg_replace('/\'\'(.+?)\'\'/s',                      '<em>\1</em>',                         $text);  // Emphasized text
		$text = preg_replace('/@@(.+?)@@/s',                          '<tt>\1</tt>',                         $text);  // Monospace text
		$text = preg_replace('/^([:]+)(.+?)$/me',                     '$this->indentText(\'\1\', \'\2\')',   $text);  // Indented text
		$text = preg_replace('/\[\$([A-Za-z0-9]+)\]/e',               '$this->replaceUserVar(\'\1\')',       $text);  // Replace user vars in the text
		$text = preg_replace('/(?<=\s)([A-Za-z.-]+)\((.*?)\)/',       '<acronym title="\2">\1</acronym>',    $text);  // Acronyms
		
		// Before we start with links we must parse image tags.
		$text = preg_replace('/\[\[(([a-z]+)\:\/\/[a-zA-Z0-9\-\.]+([\S]*?)(\.(gif|jpg|jpeg|png|bmp|tiff)))'.
		                     '( (\d+)?,(\d+)?)?( (left|right|none))?( (.+?))?\]\]/ie',
		                     '$this->doImage(\'\1\', \'\7\', \'\8\', \'\10\', \'\12\')',
		                     $text); // Parse images
		
		// Hyperlinking this page with others wouldn't be such a bad idea. Lets start with external links.
		$text = preg_replace('/(?<=\s|^)(([a-z]+)\:\/\/[a-zA-Z0-9\-\.]+([\S]*))/e',
		                     'stripslashes(sprintf($wiki->cfg[\'code_snippets\'][\'link_external\'], \'\1\', \'\1\'))',
		                     $text); // Parse urls
		$text = preg_replace('/\[\[(([a-z]+)\:\/\/[a-zA-Z0-9\-\.]+([\S]*))\]\]/e',
		                     'stripslashes(sprintf($wiki->cfg[\'code_snippets\'][\'link_external\'], \'\1\', \'\1\'))',
		                     $text); // Parse urls
		$text = preg_replace('/\[\[(([a-z]+)\:\/\/[a-zA-Z0-9\-\.]+([\S]*)) (.+?)\]\]/e',
		                     'stripslashes(sprintf($wiki->cfg[\'code_snippets\'][\'link_external\'], \'\1\', \'\4\'))',
		                     $text); // Parse urls
		$text = preg_replace('/(?<=\s|^)([a-zA-Z0-9._\-]+@[a-zA-Z0-9\.\-]+)(?=\s|$)/e',
		                     'stripslashes(sprintf($wiki->cfg[\'code_snippets\'][\'link_email\'], \'\1\', \'\1\'))',
		                     $text); // Parse emails
		$text = preg_replace('/\[\[([a-zA-Z0?9._\-]+@[a-zA-Z0?9\.\-]+)\]\]/e',
		                     'stripslashes(sprintf($wiki->cfg[\'code_snippets\'][\'link_email\'], \'\1\', \'\1\'))',
		                    $text); // Parse emails
		$text = preg_replace('/\[\[([a-zA-Z0?9._\-]+@[a-zA-Z0?9\.\-]+) (.+?)\]\]/e',
		                     'stripslashes(sprintf($wiki->cfg[\'code_snippets\'][\'link_email\'], \'\1\', \'\2\'))',
		                     $text); // Parse emails
		
		// Very well. Now we are going to check which internal links target pages
		// which exist and which target those wo need to be created somewhere in the future.
		// This is done by parser::doWikiWords
		$this->existingPages = $this->doWikiWords($text);
		
		// Now lets start with interwiki links and internal hyperlinking.
		$this->parseInterWikiLinks($text);
		
		$text = preg_replace('/\[\[([^|]+?)(#[^|]+)?\|(.*?)\]\]([a-z]+)?/e',
		                     '$this->makeFreeLink(\'\1\', \'\3\', \'\2\', \'\4\')',
		                     $text); // Internal links with free-link syntax
		
		$text = preg_replace('/\[\['.$wiki->cfg['title_format'].'(#.+?)?( .+?)?\]\]([a-z]+)?/e',
		                     '$this->makeWikiLink(\'\2\', \'\4\', \'\5\', \'\1\', \'\3\')',
		                     $text); // Internal links with the double-bracket syntax
		
		if($wiki->cfg['auto_link'] == 1) {
			$text = preg_replace('/'.$wiki->cfg['title_format_search'].'/e',
			                     '$this->makeWikiLinkFirst(\'\2\', \'\1\')',
			                     $text); // Search for wiki words in the text and link them
		}
		
		// Last (but not least) we parse a few more complicated markups.
		$text = preg_replace('/\[TOC\]/ie', 
		                     '$this->createToc()', 
		                     $text); // Table of contents ...
		$text = preg_replace('/((^(\*|#)+ (.*?)(\r\n|\n|\r|$))+)/me',
		                     '$this->parseList(\'\1\')',
		                     $text); // Parse lists
		$text = preg_replace('/(?<=\r\n|\n|\r|^)\{\|(.+?)(?:\r\n|\n|\r)(.+?)(?:\r\n|\n|\r)\|\}(?=\r\n|\n|\r|$)/se',
		                     '$this->parseTable(\'\2\', \'\1\')',
		                     $text);
		$text = preg_replace('/&lt;&lt;'.$wiki->cfg['title_format'].'&gt;&gt;/e',
		                     '$this->parseWikiTrail(\'\1\', \'\2\')',
		                     $text); // Parse wiki trails
		
		
		$this->parseUploads($text);
		
		// Parse wiki style classes and wiki styles
		while(preg_match('/%[A-Za-z0-9_-]+%.+?%%/s', $text))
		{
			$text = preg_replace('/%([A-Za-z0-9_-]+)%(.+?)%%/se',
			                     '$this->getWikiStyle(\'\1\', \'\2\')',
			                     $text);
		}
		
		while(preg_match('/%((([a-z-]+):(.+?);?)+)%(.+?)%%/s', $text))
		{
			$text = preg_replace('/%((([a-z-]+):(.+?);?)+)%(.+?)%%/se',
			                     '$this->parseWikiStyle(\'\1\', \'\5\')',
			                     $text);
		}
		
		// All markups are done. Now we can safely insert plugins which may insert
		// own formatings.
		if(preg_match_all('/\{([A-Za-z0-9_]+)( (?:[A-Za-z0-9_]+=&quot;(?:.*?)&quot; ?)*)?\}(?:(.*?)\{\/\1\})?/sie', $text, $matches) > 0) {
			$pluginCalls = $this->parseWikiPlugins($matches);
			
			foreach($pluginCalls as $pluginInfo)
			{
				foreach($pluginInfo[2] as $pluginTag)
				{
					$text = str_replace($pluginTag, $pluginInfo[3], $text);
				}
			}
		}
		
		// Bring ignored sections back into the text
		foreach($this->preformatedTexts as $rand => $string)
		{
			$text = str_replace('<PRE'.$rand.'>', '<pre>'.$string.'</pre>', $text);
		}
		
		foreach($this->noParseSections as $rand => $string)
		{
			$text = str_replace('<NOPARSE'.$rand.'>', $string, $text);
		}
		
		$this->noParseSections = array();
		$this->preformatedTexts = array();
		
		// Last step: replace special characters like the german umlauts
		// with their html entities. 
		$specialChars = get_html_translation_table(HTML_SPECIALCHARS);
		$entities     = get_html_translation_table(HTML_ENTITIES);
		
		foreach($specialChars as $char => $replace)
		{
			unset($entities[$char]);
		}
		
		$search  = array_keys($entities);
		$replace = array_values($entities);
		
		$text = str_replace($search, $replace, $text);
		
		// It seriously looks like we are done => return text.
		return $text;
	}
	
	/**
	 * Converts possible signature markers in the text
	 * into signatures.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array $text Page text to parse
	 * @return string      Parsed text
	 **/
	function parseSignatures(&$text)
	{
		global $wiki;
		
		$text = preg_replace('/\[==(.+?)==\]/se',
		                     '$this->stripNoParseSection(\'\1\')',
		                     $text); // Strip sections which are marked to be ignored by the parser
		$text = preg_replace('/@@@(.+?)@@@/se',
		                     '$this->stripPreformated(\'\1\')',
		                     $text);  // Strip sections which shall appear as entered in the form.
		
		if(!$wiki->loggedIn) {
			$text = preg_replace('/~{3,5}/', '', $text);
		} else {
			$text = str_replace('~~~~~', $wiki->convertTime($wiki->time), $text);
			$text = str_replace('~~~~',  '[['.$wiki->cfg['users_namespace'].':'.
			                             $wiki->user['user_name'].' '.
			                             $wiki->user['user_name'].']]'.' '.
			                             $wiki->convertTime($wiki->time), $text);
			$text = str_replace('~~~',   '[['.$wiki->cfg['users_namespace'].':'.
			                             $wiki->user['user_name'].' '.
			                             $wiki->user['user_name'].']]', $text);
		}
		
		$text = trim($text);
		
		// Bring ignored sections back into the text
		foreach($this->preformatedTexts as $rand => $string)
		{
			$text = str_replace('<PRE'.$rand.'>', '@@@'.$string.'@@@', $text);
		}
		
		foreach($this->noParseSections as $rand => $string)
		{
			$text = str_replace('<NOPARSE'.$rand.'>', '[=='.$string.'==]', $text);
		}
		
		$this->noParseSections = array();
		$this->preformatedTexts = array();
	}
	
	/**
	 * This function strips a text, which should be preformated, from the text
	 * while other codes are parsed.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @since 1.0 Beta 1 25.05.04 19:25
	 * @param  string $text Text to strip
	 * @return string       Text mark
	 **/
	function stripPreformated($text)
	{
		mt_srand((double)microtime()*1000000);
		$rand = mt_rand(10000, 99999);
		
		$this->preformatedTexts["$rand"] = stripslashes($text);
		
		return '<PRE'.$rand.'>';
	}
	
	/**
	 * This function strips a text, which should not be parsed from the page text.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $text Text to strip
	 * @return string       Text mark
	 **/
	function stripNoParseSection($text)
	{
		mt_srand((double)microtime()*1000000);
		$rand = mt_rand(10000, 99999);
		
		$this->noParseSections["$rand"] = stripslashes($text);
		
		return '<NOPARSE'.$rand.'>';
	}
	
	/**
	 * Defines a WikiStyle class.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $styleName Name of the style class
	 * @param string $wikiStyle Style attributes
	 * @return string           Parsed text
	 **/
	function defineWikiStyle($styleName, $wikiStyle)
	{
		$styleAttributes = $this->getStyleAttributes($wikiStyle);
		
		if(count($styleAttributes) > 0) {
			$this->wikiStyles[$styleName] = $styleAttributes;
		}
		
		return '';
	}
	
	/**
	 * This functions returns the html-code for a WikiStyle class.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $wikiStyle Style name
	 * @param string $text      Text between style tags
	 * @return string           Parsed text
	 **/
	function getWikiStyle($wikiStyle, $text)
	{
		$text = stripslashes($text);
		
		if(!isset($this->wikiStyles[$wikiStyle])) {
			return $text;
		}
		
		$styleAttributes = $this->wikiStyles[$wikiStyle];
		$style           = '';
		
		foreach($styleAttributes as $key => $val)
		{
			$style .= $key.':'.$val.';';
		}
		
		return '<span style="'.$style.'">'.$text.'</span>';
	}
	
	/**
	 * This function replaces the WikiStyle-tags in the text.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $wikiStyle Style attributes
	 * @param string $text      Text between style tags
	 * @return string           Parsed text
	 **/
	function parseWikiStyle($wikiStyle, $text)
	{
		$text            = stripslashes($text);
		$styleAttributes = $this->getStyleAttributes($wikiStyle);
		$style           = '';
		
		foreach($styleAttributes as $key => $val)
		{
			$style .= $key.':'.$val.';';
		}
		
		return '<span style="'.$style.'">'.$text.'</span>';
	}
	
	/**
	 * This function extracts and validates 
	 * the style attributes from a WikiStyle string.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $wikiStyle Style attributes
	 * @return array            Attributes
	 **/
	function getStyleAttributes($wikiStyle)
	{
		$wiki = &$GLOBALS['wiki'];
		$styleAttributes = array();
		$attr            = explode(';', $wikiStyle);
		
		foreach($attr as $attribute)
		{
			$attribute = explode(':', $attribute);
			
			if(isset($attribute[0]) && isset($attribute[1])) {
				$name  = trim($attribute[0]);
				$value = trim($attribute[1]);
				
				if(isset($wiki->cfg['style_attributes'][$name])
				   && preg_match($wiki->cfg['style_attributes'][$name], $value)) {
					$styleAttributes[$name] = $value;
				}
			}
		}
		
		return $styleAttributes;
	}
	
	/**
	 * This function creates the html-code for a headline
	 * and returns it.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int    $depth Depth of the heading
	 * @param  string $text  Heading text
	 * @return string        Html-code to display the heading
	 **/
	function createHeading($depth, $text)
	{
		global $wiki;
		
		$depth  = strlen($depth) - 1;
		$anchor = preg_replace('/[^\w\s\xc0-\xff]/', '', html_entity_decode($text));
		$this->headings[] = array($depth, $text, htmlentities($anchor));
		
		return sprintf($wiki->cfg['code_snippets']['heading'],
		               preg_replace('/\s/', '_', $anchor), $depth, $text);
	}
	
	/**
	 * This function generetas a table of contents
	 * out of the headlines in the text.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string       Table of contents
	 **/
	function createToc()
	{
		$wiki     = &$GLOBALS['wiki'];
		$headings = $this->headings;
		$toc      = '';
		$hNum     = 1;
		
		foreach($headings as $heading)
		{
			$toc .= str_repeat('#', $heading[0]).' <a href="#'.preg_replace('/\s/', '_', $heading[2]).'">'.$heading[1].'</a>'."\n";
			
			$hNum++;
		}
		
		return sprintf($wiki->cfg['code_snippets']['TOC'],
		               $wiki->lang['wiki_toc'],
		               $wiki->lang['wiki_hide_toc'],
		               $toc, $wiki->lang['wiki_show_toc']);
	}
	
	/**
	 * This function calculates a text indent and returns the right html-code.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $indent Indent level
	 * @param  string $text   Text to intend
	 * @return string         Indented text
	 **/
	function indentText($indent, $text)
	{
		global $wiki;
		$indentWidth = $wiki->cfg['indent_width'] * strlen($indent);
		
		return '<div style="margin-left:'.$indentWidth.'px">'.$text.'</div>';
	}
	
	/**
	 * Replaces user variables of format [$VariableName] in the text
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $varName Variable name
	 * @return string          Indented text
	 **/
	function replaceUserVar($varName)
	{
		if(isset($this->userVars[$varName])) {
			return $this->userVars[$varName];
		} else {
			return '';
		}
	}
	
	/**
	 * Generates the html code for images in the text.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $imageUrl Url to the image file
	 * @param  int    $width    Image width in the text
	 * @param  int    $height   Image height in the text
	 * @param  string $float    Text float around the image
	 * @param  string $alt      Alternate text
	 * @return string           Image code
	 **/
	function doImage($imageUrl, $width, $height, $float, $alt)
	{
		global $wiki;
		
		$image = '<img src="'.$imageUrl.'"';
		
		if($width != '') {
			$image .= ' width="'.$width.'"';
		}
		
		if($height != '') {
			$image .= ' height="'.$height.'"';
		}
		
		if($alt != '') {
			$image .= ' alt="'.trim($alt).'" title="'.trim($alt).'"';
		}
		
		if($float != '') {
			switch($float)
			{
				case 'right': $float = 'left';  break;
				case 'left':  $float = 'right'; break;
				default:      $float = 'none';  break;
			}
			
			$image .= ' style="float:'.$float.'"';
		}
		
		$image .= ' />';
		
		$image = sprintf($wiki->cfg['code_snippets']['image'], $image, $alt, $width, $height, $float);
		
		if(preg_match('/^((([a-z]+)\:\/\/[a-zA-Z0-9\-\.]+([\S]*))|([a-zA-Z0-9._\-]+@[a-zA-Z0-9\.\-]+))$/', trim($alt))) {
			return sprintf($wiki->cfg['code_snippets']['link_external'], $alt, $image);
		} else {
			return $image;
		}
	}
	
	/**
	 * This function replaces InterWiki links in the text.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $text Text to parse
	 * @return string       Parsed text
	 **/
	function parseInterWikiLinks(&$text)
	{
		foreach($this->interWiki as $wiki => $wikiURL)
		{
			$text = preg_replace('/(?<=\s|^)('.preg_quote($wiki, '/').'):([^\s]+)(?=\s|$)/e',
			                     '$this->makeInterWikiLink(\'\1\', \'\2\', \'\')', $text);
			$text = preg_replace('/\[\[('.preg_quote($wiki, '/').'):(.+?)(#.+?)?(&gt;[A-Za-z0-9_-]+)?( (.+?))?\]\]/e',
			                     '$this->makeInterWikiLink(\'\1\', \'\2\', \'\5\', \'\3\', \'\4\')', $text);
		}
	}
	
	/**
	 * Generates the HTML-code for an InterWiki link.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $wiki      InterWiki
	 * @param  string $page      InterWiki page name
	 * @param  string $desc      Link description
	 * @return string            HTML-code
	 **/
	function makeInterWikiLink($interwiki, $page, $desc, $anchor = '', $target = '')
	{
		global $wiki;
		
		$page = $page;
		$desc = $desc;
		$url  = sprintf($this->interWiki[$interwiki], $page).$anchor;
		
		if($interwiki == $wiki->cfg['thispage_interwiki'] || $interwiki == $wiki->cfg['thiswiki_interwiki']) {
			$codeSnippet = $wiki->cfg['code_snippets']['link_internal'];
		} else {
			$codeSnippet = $wiki->cfg['code_snippets']['link_interwiki'];
		}
		
		if($target != '') {
			$target = ' target="'.$target.'"';
		}
		
		if($desc == '') {
			$link = sprintf($codeSnippet, $url, $interwiki.':'.$page, $target);
		} else {
			$link = sprintf($codeSnippet, $url, $desc, $target);
		}
		
		return $link;
	}
	
	/**
	 * This function searches for wiki words in the text and
	 * checks wether the corresponding pages exist or not.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $text Text to search for wiki words
	 * @return string       Parsed text
	 **/
	function doWikiWords(&$text)
	{
		global $wiki;
		
		$db = &singleton('database');
		
		preg_match_all('/'.$wiki->cfg['title_format_search'].'/', $text, $matches1);
		preg_match_all('/\[\['.$wiki->cfg['title_format'].'(#.+?)?(&gt;[A-Za-z0-9_-]+)?( .+?)?\]\]([a-z]+)?/', $text, $matches2);
		preg_match_all('/\[\[([^|]+?)(#[^|]+)?\|(.*?)\]\]([a-z]+)?/e', $text, $matches3);
		
		$namespaces = array();
		
		$matches1    = array_slice($matches1, 1, 2);
		$matches1    = array('namespaces' => $matches1[0], 'names' => $matches1[1]);
		$matches2    = array_slice($matches2, 1, 2);
		$matches2    = array('namespaces' => $matches2[0], 'names' => $matches2[1]);
		$matches3tmp = array('namespaces' => array(), 'names' => array());
		
		for($i = 0; $i < count($matches3[1]); $i++)
		{
			$page = $matches3[1][$i];
			
			if(strstr($page, ':') !== false) {
				$page = explode(':', $page);
				$namespace = $page[0];
				unset($page[0]);
				$page = join($page);
			} else {
				$namespace = '';
			}
			
			$matches3tmp['namespaces'][] = $namespace;
			$matches3tmp['names'][]      = str_replace(' ', '_', $page);
		}
		
		$pages = array_merge_recursive($matches1, $matches2, $matches3tmp);
		
		for($i = 0; $i < count($pages['names']); $i++) {
			$namespace = substr($pages['namespaces'][$i], 0, strlen($pages['namespaces'][$i]) - 1);
			$page      = $pages['names'][$i];
			
			if($namespace == $wiki->cfg['special_namespace']) {
				continue;
			}
			
			if($namespace == '') {
				$namespace = $wiki->cfg['default_namespace'];
			}
			
			if(!isset($namespaces[$namespace])) {
				$namespaces[$namespace] = array();
			}
			
			$namespaces[$namespace][] = $page;
		}
		
		$sql   = '';
		$first = true;
		
		foreach($namespaces as $namespace => $pages)
		{
			$pages  = array_unique($pages);
			
			if($first) {
				$sql   .= '(page_namespace = \''.$namespace.'\' AND page_name IN(\''.join('\', \'', $pages).'\'))';
				$first  = false;
			} else {
				$sql   .= ' OR (page_namespace = \''.$namespace.'\' AND page_name IN(\''.join('\', \'', $pages).'\'))';
			}
		}
		
		$existing = array();
		
		if($sql != '') {
			$result   = $db->query('SELECT page_namespace, page_name FROM '.DB_PREFIX.'pages '.
			'WHERE '.$sql);
			
			while($row = $db->fetch($result))
			{
				if(!isset($existing[$row['page_namespace']])) {
					$existing[$row['page_namespace']] = array();
				}
				
				$existing[$row['page_namespace']][strtolower($row['page_name'])] = 1;
			}
		}
		
		return $existing;
	}
	
	/**
	 * Checks wether an WikiWord shall be auto linked. This is controlled
	 * by the link_num config setting.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return
	 **/
	function makeWikiLinkFirst($page, $namespace = '')
	{
		global $wiki;
		
		$orig = $namespace.$page;
		
		if($namespace == '') {
			$namespace = $wiki->cfg['default_namespace'];
		} else {
			$namespace = substr($namespace, 0, strlen($namespace) - 1);
		}
		
		$pdata = array('page_name' => $page, 'page_namespace' => $namespace);
		$pid   = $wiki->getUniqueName($pdata);
		
		if($wiki->cfg['link_self'] == 0 && $pid == $this->pageName) {
			return $orig;
		}
		
		if($wiki->cfg['link_num'] <= 0) {
			return $this->makeWikiLink($page, '', '', $namespace.':');
		}
		
		if(!isset($this->linkedPages[$pid])) {
			$this->linkedPages[$pid] = 1;
			return $this->makeWikiLink($page, '', '', $namespace.':');
		} elseif($this->linkedPages[$pid] < $wiki->cfg['link_num']) {
			$this->linkedPages[$pid]++;
			return $this->makeWikiLink($page, '', '', $namespace.':');
		} else {
			return $orig;
		}
	}
	
	/**
	 * This function prepares a FreeLink for the makeWikiLink function.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $page   Page name
	 * @param string $anchor Anchor in target page
	 * @param string $desc   Link description
	 **/
	function makeFreeLink($page, $desc, $anchor, $ending)
	{
		global $wiki;
		
		if(strstr($page, ':') !== false) {
			$page = explode(':', $page);
			$namespace = $page[0].':';
			unset($page[0]);
			$page = join($page);
		} else {
			$namespace = '';
		}
		
		if($desc == '') {
			if($namespace != '' && $namespace != $wiki->cfg['default_namespace'] && $wiki->cfg['display_namespaces']) {
				$desc = $namespace.$page;
			} else {
				$desc = $page;
			}
		}
		
		$page = str_replace(' ', '_', $page);
		
		return $this->makeWikiLink($page, $desc, $ending, $namespace, $anchor);
	}
	
	/**
	 * This function generates the html-code for a internal link.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $page             WikiPage
	 * @param  string $description = '' Link description
	 * @param  string $ending = ''      Link ending
	 * @param  string $namespace = ''   Namespace of the page
	 * @return string                   Parsed text
	 **/
	function makeWikiLink($page, $description = '', $ending = '', $namespace = '', $anchor = '')
	{
		global $wiki;
		
		if($namespace == '') {
			$namespace = $wiki->cfg['default_namespace'];
		} else {
			$namespace = substr($namespace, 0, strlen($namespace) - 1);
		}
		
		$pageData = array('page_name' => $page, 'page_namespace' => $namespace);
		
		if($wiki->cfg['space_wiki_words'] == 1) {
			$linkText = $this->spaceWikiWord($page);
		} else {
			$linkText = $page;
		}
		
		if(!isset($this->existingPages[$namespace][strtolower($page)]) && $namespace != $wiki->cfg['special_namespace']) {
			$linkURL  = $wiki->genUrl($wiki->getUniqueName($pageData), 'edit');
		} else {
			$linkURL  = $wiki->genUrl($wiki->getUniqueName($pageData)).$anchor;
		}
		
		if($namespace != $wiki->cfg['default_namespace'] && $wiki->cfg['display_namespaces']) {
			$linkText = $namespace.':'.$page;
		}
		
		if($description != '') {
			$linkText = $description;
		}
		
		if($ending != '') {
			$linkText .= $ending;
		}
		
		if(isset($this->existingPages[$namespace][strtolower($page)]) || $namespace == $wiki->cfg['special_namespace']) {
			$link = sprintf($wiki->cfg['code_snippets']['link_internal'], $linkURL, $linkText);
		} else {
			$link = sprintf($wiki->cfg['code_snippets']['link_create'], $linkURL, $linkText);
		}
		
		return $link;
	}
	
	/**
	 * This function parses a list.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $list List items
	 * @return string       HTML-Code for the list
	 **/
	function parseList($list)
	{
		$list      = str_replace('\"', '"', $list);
		$items     = explode("\n", trim($list));
		$prevDepth = 0;
		$listCode  = '';
		$tagsClose = array();
		$closeLi   = true;
		
		for($i = 0; $i < count($items); $i++)
		{
			$item = $items[$i];
			preg_match('/(^(\*|#)+)/', $item, $match);
			$itemType    = $match[0][strlen($match[0]) - 1];
			$itemCleaned = substr($item, strlen($match[0]), strlen($item));
			$itemDepth   = strlen($match[0]);
			
			if($itemDepth > $prevDepth) {
				for($j = $prevDepth; $j < $itemDepth; $j++)
				{
					if(strlen($listCode) > 5) {
						$listCode = substr($listCode, 0, strlen($listCode) - 6);
					}
					
					if($item[$j] == '*') {
						$tagsClose[]  = '</ul></li>';
						$listCode   .= '<ul><li>';
					} elseif($item[$j] == '#') {
						$tagsClose[]  = '</ol></li>';
						$listCode   .= '<ol style="list-style-type:decimal"><li>';
					}
				}
			} elseif($itemDepth < $prevDepth) {
				$tagsClose = array_values($tagsClose);
				
				for($j = $prevDepth - 1; $j >= $itemDepth; $j--)
				{
					$listCode .= $tagsClose[$j];
					unset($tagsClose[$j]);
				}
				
				if($itemDepth == 0) {
					$closeLi   = false;
				}
				$listCode .= '<li>';
			} else {
				$listCode .= '<li>';
			}
			
			$listCode .= trim($itemCleaned).($closeLi ? '</li>' : '')."\n";
			$prevDepth = $itemDepth;
			$closeLi   = true;
		}
		
		$tagsClose = array_values($tagsClose);
		
		for($i = count($tagsClose) - 1; $i >= 0; $i--)
		{
			$listCode .= $tagsClose[$i];
			unset($tagsClose[$i]);
		}
		
		$listCode = substr($listCode, 0, strlen($listCode) - 5);
		
		return $listCode;
	}
	
	/**
	 * This function parses the table formating into an html table.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $text       Text between table formating codes
	 * @param  string $attributes Table attributes
	 * @return string             HTML-table
	 **/
	function parseTable($text, $attributes)
	{
		$text       = trim(stripslashes($text));
		$attributes = $this->parseAttributes($attributes);
		$lines      = explode("\n", $text);
		$rows       = 0;
		$cols       = 0;
		$table      = array();
		
		for($i = 0; $i < count($lines); $i++)
		{
			$line = trim($lines[$i]);
			
			if($line == '') {
				continue;
			}
			
			if(!isset($table[$rows])) {
				$table[$rows] = array();
			}
			
			if($line[0] != '|') {
				$table[$rows][($cols - 1)][1] .= $line;
				continue;
			}
			
			if($line[1] == '-') {
				$rows++;
				$cols = 0;
				continue;
			}
			
			preg_match('/^\|(?:(.+?)\|)?(.+?)$/', $line, $match);
			$match[1]    = trim($match[1]);
			$match[2]    = trim($match[2]);
			$cellAttribs = '';
			
			if($match[1] != '') {
				$cellAttribs = $this->parseAttributes($match[1]);
			}
			
			$table[$rows][$cols] = array($cellAttribs, $match[2]);
			$cols++;
		}
		
		$htmlTable = '<table'.$attributes.' class="wiki-table">'."\n";
		
		foreach($table as $cells)
		{
			$htmlTable .= '<tr>'."\n";
			
			foreach($cells as $cell)
			{
				if($cell[0] != '') {
					$htmlTable .= '<td'.$cell[0].'> '.$cell[1].' </td>'."\n";
				} else {
					$htmlTable .= '<td> '.$cell[1].' </td>'."\n";
				}
			}
			
			$htmlTable .= '</tr>'."\n";
		}
		
		$htmlTable .= '</table>'."\n";
		return $htmlTable;
	}
	
	/**
	 * Parses table attributes.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $attribs Attribute string
	 * @return string          HTML attributes
	 **/
	function parseAttributes($attribs)
	{
		$htmlAttribs = '';
		
		if(preg_match_all('/([a-z]+)=&quot;(.+?)&quot;/i', $attribs, $match)) {
			for($i = 0; $i < count($match[1]); $i++) {
				if($match[1][$i] == 'style') {
					$htmlAttribs .= $this->getTableStyle($match[2][$i]);
				} elseif($match[1][$i] == 'colspan') {
					$htmlAttribs .= ' colspan="'.intval($match[2][$i]).'"';
				} elseif($match[1][$i] == 'rowspan') {
					$htmlAttribs .= ' rowspan="'.intval($match[2][$i]).'"';
				} elseif($match[1][$i] == 'spacing') {
					$htmlAttribs .= ' cellspacing="'.intval($match[2][$i]).'"';
				} elseif($match[1][$i] == 'padding') {
					$htmlAttribs .= ' cellpadding="'.intval($match[2][$i]).'"';
				} elseif($match[1][$i] == 'border') {
					$htmlAttribs .= ' border="'.intval($match[2][$i]).'"';
				}
			}
		}
		
		return $htmlAttribs;
	}
	
	/**
	 * Validates the style attributes in a table formattin.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $style Style properties/name
	 * @return string        Style attribute
	 **/
	function getTableStyle($style)
	{
		$style     = trim($style);
		$htmlStyle = '';
		
		if(preg_match('/^[A-Za-z0-9_-]+$/', $style)) {
			$style = isset($this->wikiStyles[$style]) ? 
			         $this->wikiStyles[$style] : array();
		} else {
			$style = $this->getStyleAttributes($style);
		}
		
		foreach($style as $name => $value)
		{
			$htmlStyle .= $name.':'.$value.';';
		}
		
		if($htmlStyle != '') {
			return ' style="'.$htmlStyle.'"';
		}
	}
	
	/**
	 * Parses a WikiTrail navigation.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $namespace Namespace of the trail page
	 * @param  string $trailPage Name of the trail page
	 * @return string           Trail navigation
	 **/
	function parseWikiTrail($namespace, $trailPage)
	{
		global $wiki;
		
		$namespace = substr($namespace, 0, strlen($namespace) - 1);
		$page = $wiki->getPage($trailPage, true, $namespace);
		
		if($page['page_id'] == 0) {
			return '';
		}
		
		$trailPages = array();
		
		$text      = $page['page_text'];
		$text      = preg_replace('/^(\*|#)+ (?:\[\[)?('.$wiki->cfg['title_format'].')/me', '$this->getTrailPage(\'\2\', $trailPages)', $text);
		$linkLeft  = '';
		$linkRight = '';
		
		for($i = 0; $i < count($trailPages); $i++)
		{
			$pageInfo  = explode(':', $trailPages[$i]);
			if(count($pageInfo) == 2) {
				$pageInfo = array('page_name' => $pageInfo[1],
				                  'page_namespace' => $pageInfo[0]);
			} else {
				$pageInfo = array('page_name' => $pageInfo[0],
				                  'page_namespace' => $wiki->cfg['default_namespace']);
			}
			
			if($wiki->getUniqueName($pageInfo) == $this->pageName) {
				if($i > 0) {
					$url = $wiki->genUrl($trailPages[$i - 1]);
					$linkLeft = sprintf($wiki->cfg['code_snippets']['trail_linkleft'], 
					                    $url, $trailPages[$i - 1]);
				} else {
					$linkLeft = $wiki->cfg['code_snippets']['trail_emptyleft'];
				}
				
				if($i < count($trailPages) - 1) {
					$url = $wiki->genUrl($trailPages[$i + 1]);
					$linkRight = sprintf($wiki->cfg['code_snippets']['trail_linkright'], 
					                     $url, $trailPages[$i + 1]);
				} else {
					$linkRight = $wiki->cfg['code_snippets']['trail_emptyright'];
				}
			}
		}
		
		$url        = $wiki->genUrl($wiki->getUniqueName($page));
		$linkMiddle = sprintf($wiki->cfg['code_snippets']['trail'], 
		                      $linkLeft, $url, $trailPage, $linkRight);
		
		return $linkMiddle;
	}
	
	/**
	 * Extracts the page name at the beginning of a list item
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $listItem   Name of the trail page
	 * @param string $trailPages Reference to the trail pages array
	 * @return string            Empty string
	 **/
	function getTrailPage($trailPage, &$trailPages)
	{
		$trailPages[] = $trailPage;
		return '';
	}
	
	/**
	 * Parses the uploaded files in the page text.
	 *
	 * @param  string &$text Page text
	 * @return bool   true on success, false otherwise
	 **/
	function parseUploads(&$text)
	{
		$db = &singleton('database');
		
		if(preg_match_all('/\{\{(.+?)\}\}/e', $text, $matches) < 1) {
			return false;
		}
		
		$markers    = $matches[0];
		$fileNames  = array();
		$fileParams = array();
		$sqlFiles   = array();
		$dbFiles    = array();
		
		foreach($matches[1] as $file)
		{
			$file = explode('|', $file);
			$fileNames[] = $file[0];
			$sqlFiles[]  = addslashes($file[0]);
			
			unset($file[0]);
			
			$fileParams[] = $file;
		}
		
		$result = $db->query('SELECT file_id, file_orig_name, '.
		'file_ext, file_size, file_description '.
		'FROM '.DB_PREFIX.'uploads WHERE file_orig_name IN('.
		'"'.join('", "', array_unique($sqlFiles)).'")');
		
		while($row = $db->fetch($result))
		{
			$dbFiles[$row['file_orig_name']] = $row;
		}
		
		for($i = 0; $i < count($fileNames); $i++)
		{
			if(!isset($dbFiles[$fileNames[$i]])) {
				$text = str_replace($markers[$i], '', $text);
				continue;
			}
			
			$name      = $fileNames[$i];
			$mark      = $markers[$i];
			$params    = $fileParams[$i];
			$dbFile    = $dbFiles[$name];
			$size      = $GLOBALS['wiki']->HRFileSize($dbFile['file_size']);
			$thumb     = false;
			$float     = '';
			$width     = '';
			$height    = '';
			$desc      = '';
						
			if($dbFile['file_ext'] == 'gif'  ||
			   $dbFile['file_ext'] == 'png'  ||
			   $dbFile['file_ext'] == 'jpg'  ||
			   $dbFile['file_ext'] == 'jpeg') {
				$snippet = 'image_normal';
				$isImage = true;
			} else {
				$snippet = 'file_normal';
				$isImage = false;
			}
			
			foreach($params as $param)
			{
				if($param == 'framed') {
					$snippet = $isImage ? 'image_framed' : 'file_framed';
				} elseif($param == 'thumb') {
					$thumb = true;
				} elseif($param == 'left') {
					$float = ' style="float:left"';
				} elseif($param == 'right') {
					$float = ' style="float:right"';
				} elseif(preg_match('/^(\d+)?x(\d+)?(px|%)$/', $param, $match)) {
					if($isImage) {
						$width     = $match[1] != '' ? ' width="'.$match[1].$match[3].'"' : '';
						$height    = $match[2] != '' ? ' height="'.$match[2].$match[3].'"' : '';
					}
				} else {
					$desc = $param;
				}
			}
			
			if($isImage) {
				if($thumb) {
					$imageUrl = $GLOBALS['wiki']->cfg['url_root'].'/uploads/img/thumbs/'.
					$dbFile['file_id'].'.'.$dbFile['file_ext'];
				} else {
					$imageUrl = $GLOBALS['wiki']->cfg['url_root'].'/uploads/img/'.
					$dbFile['file_id'].'.'.$dbFile['file_ext'];
				}
			} else {
				$imageUrl = $GLOBALS['wiki']->cfg['url_root'].'/themes/'.
				$GLOBALS['wiki']->theme.'/images/mimetypes/'.$dbFile['file_ext'].'.png';
			}
			
			$fileUrl = $GLOBALS['wiki']->genUrl($GLOBALS['wiki']->cfg['special_namespace'].':Uploads', 
			           '', array('op' => 'file', 'fid' => $dbFile['file_id']));
			$snippet = $GLOBALS['wiki']->cfg['code_snippets'][$snippet];
			$code    = sprintf($snippet, $imageUrl, $width, $height, $float, $desc, $fileUrl, $name, $size);
			$text    = str_replace($mark, $code, $text);
		}
	}
	
	/**
	 * This function parses the attribute strings 
	 * and loads the plugins.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $pluginName Plugin name
	 * @return string             Plugin return value
	 **/
	function parseWikiPlugins($plugins)
	{
		$tpl = &singleton('template');
		
		$pluginCalls = array();
		
		for($i = 0; $i < count($plugins[0]); $i++) {
			$name    = $plugins[1][$i];
			$params  = $plugins[2][$i];
			$text    = $plugins[3][$i];
			$pString = '';
			
			if($params != '') {
				preg_match_all('/([A-Za-z0-9_]+)=&quot;(.*?)&quot;/', $params, $matches);
				
				for($j = 0; $j < count($matches[1]); $j++)
				{
					$pArray[$matches[1][$j]] = $matches[2][$j];
				}
			}
			
			$pArray['name'] = $name;
			$pArray['text'] = true;
			
			ksort($pArray);
			
			foreach($pArray as $key => $val) {
				$pString .= $key.'="'.$val.'" ';
			}
			
			$pluginId = md5($name.' '.trim($pString));
			
			if(!isset($pluginCalls[$pluginId])) {
				$pluginCalls[$pluginId] = array($name, $pArray, array(), $tpl->wikiPlugin($pArray, $text));
			}
			
			$pluginCalls[$pluginId][2][] = $plugins[0][$i];
		}
		
		return $pluginCalls;
	}
	
	/**
	 * This function strips all formating codes from a text.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $text     Text to parse
	 * @return string           Parsed text
	 **/
	function stripCodes($text)
	{
		global $wiki;
		
		$text = htmlentities($text);
		$text = preg_replace('/\[==(.+?)==\]/se',
		                     '$this->stripNoParseSection(\'\1\')',
		                     $text); // Strip sections which are marked to be ignored by the parser
		$text = preg_replace('/@@@(.+?)@@@/se',
		                     '$this->stripPreformated(\'\1\')',
		                     $text);  // Strip sections which shall appear as entered in the form.
		
		$text = preg_replace('/\[REDIRECT '.$wiki->cfg['title_format'].'\]/', '', $text);
		$text = preg_replace('/(%%%|&lt;br( \/)?&gt;)/',                            '',   $text);
		$text = preg_replace('/%define=([A-Za-z0-9_-]+) ((([a-z-]+):(.+?);?)+)%/s', '',   $text);
		$text = preg_replace('/\\\(?=\r\n|\n|\r|$)/',                               '',   $text);
		$text = preg_replace('/^(={2,4})(.+?)(\1)($|\r\n|\n|\r)/m',                 '\2', $text);
		$text = preg_replace('/^----+/m',                                           '',   $text);
		$text = preg_replace('/^-&gt;&lt;-(.+?)$/m',                                '\1', $text);
		$text = preg_replace('/^-&gt;(.+?)$/m',                                     '\1', $text);
		$text = preg_replace('/^&lt;-(.+?)$/m',                                     '\1', $text);
		$text = preg_replace('/\'\'\'(.+?)\'\'\'/s',                                '\1', $text);
		$text = preg_replace('/\'\'(.+?)\'\'/s',                                    '\1', $text);
		$text = preg_replace('/@@(.+?)@@/s',                                        '\1', $text);
		$text = preg_replace('/^([:]+)(.+?)$/m',                                    '\2', $text);
		$text = preg_replace('/\[\$([A-Za-z0-9]+)\]/',                              '',   $text);
		
		$text = preg_replace('/\[\[(([a-z]+)\:\/\/[a-zA-Z0-9\-\.]+([\S]*?)(\.(gif|jpg|jpeg|png|bmp|tiff)))( .+?)?\]\]/', '', $text);
		$text = preg_replace('/\[\[(([a-z]+)\:\/\/[a-zA-Z0-9\-\.]+([\S]*?))\]\]/',       '\1',  $text);
		$text = preg_replace('/\[\[(([a-z]+)\:\/\/[a-zA-Z0-9\-\.]+([\S]*?)) (.+?)\]\]/', '\4',  $text);
		$text = preg_replace('/\[\[([a-zA-Z0?9._\-]+@[a-zA-Z0?9\.\-]+)\]\]/',           '\1',  $text);
		$text = preg_replace('/\[\[([a-zA-Z0?9._\-]+@[a-zA-Z0?9\.\-]+) (.+?)\]\]/',     '\2',  $text);
		
		$this->stripInterWikiLinks($text);
		
		$text = preg_replace('/\[\['.$wiki->cfg['title_format'].'( .+?)?\]\]([a-z]+)?/e',
		                     '$this->stripWikiLinkTags(\'\1\2\', \'\3\', \'\4\')', $text);
		
		$text = preg_replace('/\[TOC\]/i', '',  $text);

		$text = preg_replace('/(?<=\r\n|\n|\r|^)\{\|(.+?)(?:\r\n|\n|\r)(.+?)(?:\r\n|\n|\r)\|\}(?=\r\n|\n|\r|$)/se',
		                     '$this->stripTableTags(\'\2\')',
		                     $text);
		$text = preg_replace('/&lt;&lt;'.$wiki->cfg['title_format'].'&gt;&gt;/',
		                     '\1\2', $text);
		
		while(preg_match('/%[A-Za-z0-9_-]+%.+?%%/s', $text))
		{
			$text = preg_replace('/%([A-Za-z0-9_-]+)%(.+?)%%/s', '\2', $text);
		}
		
		while(preg_match('/%((([a-z-]+):(.+?);?)+)%(.+?)%%/s', $text))
		{
			$text = preg_replace('/%((([a-z-]+):(.+?);?)+)%(.+?)%%/s', '\5', $text);
		}
		
		// Bring ignored sections back into the text
		foreach($this->preformatedTexts as $rand => $string)
		{
			$text = str_replace('<PRE'.$rand.'>', $string, $text);
		}
		
		foreach($this->noParseSections as $rand => $string)
		{
			$text = str_replace('<NOPARSE'.$rand.'>', $string, $text);
		}
		
		return $text;
	}
	
	/**
	 * This function strips InterWiki links in the text.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $text Text to parse
	 * @return string       Parsed text
	 **/
	function stripInterWikiLinks(&$text)
	{
		global $wiki;
		
		foreach($this->interWiki as $interwiki => $wikiURL)
		{
			$text = preg_replace('/\[\[('.preg_quote($interwiki, '/').'):(.+?)(#.+?)?(&gt;[A-Za-z0-9_-]+)?( (.+?))?\]\]/e',
			                     '$this->stripInterWikiLink(\'\1:\2\', \'\6\')', $text);
		}
	}
	
	/**
	 * This function strips InterWiki link tags.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $interWiki The InterWiki name and target page
	 * @param  string $desc      Possible link description
	 * @return string            InterWiki without tags
	 **/
	function stripInterWikiLink($interWiki, $desc)
	{
		if($desc == '') {
			return stripslashes($interWiki);
		} else {
			return stripslashes($desc);
		}
	}
	
	/**
	 * This function strips WikiLink tags and leaves their name/description
	 * in the text.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $pageName Page name where it links
	 * @param  string $linkDesc Link description
	 * @param  string $ending   Link ending
	 * @return string           Page name or link description
	 **/
	function stripWikiLinkTags($pageName, $linkDesc, $ending)
	{
		$retVal = $pageName;
		
		if($linkDesc != '') {
			$retVal = $linkDesc;
		}
		
		return $retVal.$ending;
	}
	
	/**
	 * Spaces a WikiWord
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $word WikiWord
	 * @return string       Spaced word
	 **/
	function spaceWikiWord($word)
	{
		return trim(preg_replace('/([A-Z][a-z0-9_]+)/', '\1 ', $word));
	}
	
	/**
	 * Strips table tags from a wiki page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function stripTableTags($text)
	{
		$text       = trim(stripslashes($text));
		$lines      = explode("\n", $text);
		$return     = '';
		
		for($i = 0; $i < count($lines); $i++)
		{
			$line = trim($lines[$i]);
			
			if($line == '') {
				continue;
			}
			
			if($line[0] != '|') {
				$return .= $line;
				continue;
			}
			
			if($line[1] == '-') {
				continue;
			}
			
			$line    = preg_replace('/^\|(?:(.+?)\|)?(.+?)$/', '\2', $line);
			$return .= trim($line);
		}
		
		return $return;
	}
}
?>
