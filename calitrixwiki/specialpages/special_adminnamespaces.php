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
 
include $cfg['lib_dir'].'/class_admin.php';

/**
 * This is the admin specialpage where namespaces can be created and dropped.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_adminnamespaces extends admin
{
	var $cfgTemplate = 'admin_namespaces.tpl';
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$tpl = &singleton('template');
		
		$tpl->assign('isMessage',       false);
		$tpl->assign('isError',         false);
		
		if($this->request == 'POST' && !isset($this->get['op'])) {
			$this->saveNamespace();
		} elseif(isset($this->get['op'])) {
			$op = $this->get['op'];
			
			switch($op)
			{
				case 'del': $this->removeNamespace(); break;
			}
		}
		
		$this->loadNamespaces();
	}
	
	/**
	 * Removes a namespace.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function removeNamespace()
	{
		$tpl    = &singleton('template');
		$db     = &singleton('database');
		$config = $this->getOrigConfig();
		
		if(!isset($this->get['nspace']) || !in_array($this->get['nspace'], $config['namespaces'])) {
			return false;
		}
		
		$namespace = $this->get['nspace'];
		
		foreach($config['namespaces'] as $id => $nspace)
		{
			if($nspace == $namespace) {
				if(!isset($this->get['conf'])) {
					$result = $db->queryRow('SELECT COUNT(page_id) as count FROM '.DB_PREFIX.'pages '.
					'WHERE page_namespace = "'.addslashes($namespace).'"');
					
					if($result['count'] > 0) {
						$this->cfgTemplate = 'admin_namespaces_confirm.tpl';
						$tpl->assign('nspace', htmlentities($namespace));
						return true;
					}
				}
				
				$this->removeConfigItem('namespaces', $id);
				break;
			}
		}
		
		$this->rewriteConfig();
		
		if($this->request == 'POST') {
			$do = isset($this->post['do']) ? $this->post['do'] : '';
			
			if($do == 'del') {
				$result = $db->query('SELECT page_id FROM '.DB_PREFIX.'pages '.
				'WHERE page_namespace = "'.addslashes($namespace).'"');
				$pages = array();
				
				while($row = $db->fetch($result))
				{
					$pages[] = $row['page_id'];
				}
				
				$ids = 'IN('.join(', ', $pages).')';
				
				$db->query('DELETE FROM '.DB_PREFIX.'pages WHERE page_id '.$ids);
				$db->query('DELETE FROM '.DB_PREFIX.'page_texts WHERE page_id '.$ids);
				$db->query('DELETE FROM '.DB_PREFIX.'changelog WHERE log_page_id '.$ids);
				$db->query('DELETE FROM '.DB_PREFIX.'bookmarks WHERE bm_page_id '.$ids);
				$db->query('DELETE FROM '.DB_PREFIX.'local_masks WHERE perm_page_id '.$ids);
			} elseif($do == 'move') {
				$target = isset($this->post['target_space']) ? $this->post['target_space'] : '';
				
				if(in_array($target, $config['namespaces'])) {
					$db->query('UPDATE '.DB_PREFIX.'pages '.
					'SET page_namespace = "'.addslashes($target).'" '.
					'WHERE page_namespace = "'.addslashes($namespace).'"');
				}
			}
		}
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_namespaces_updated']);
	}
	
	/**
	 * Saves a new namespace to the config table.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function saveNamespace()
	{
		$tpl = &singleton('template');
		$namespace = isset($this->post['namespace']) ? trim($this->post['namespace']) : '';
		
		if($namespace == '') {
			return false;
		}
		
		$config = $this->getOrigConfig();
		
		if(in_array($namespace, $config['namespaces'])) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  array($this->lang['admin_duplicated_namespaces']));
			return false;
		}
		
		$this->setConfigItem('namespaces', '', $namespace);
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_namespaces_updated']);
	}
	
	/**
	 * Loads all namespaces and counts the pages in every namespace.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function loadNamespaces()
	{
		$sql    = array();
		$tpl    = &singleton('template');
		$db     = &singleton('database');
		$config = $this->getOrigConfig();
		$config = $config['namespaces'];
		$spaces = array();
		
		foreach($config as $namespace)
		{
			$spaces[$namespace] = 0;
		}
		
		$result = $db->query('SELECT COUNT(page_id) AS page_count, page_namespace '.
		'FROM '.DB_PREFIX.'pages GROUP BY page_namespace ORDER BY page_namespace');
		
		while($row = $db->fetch($result))
		{
			if(isset($spaces[$row['page_namespace']])) {
				$spaces[$row['page_namespace']] = $row['page_count'];
			}
		}
		
		$tpl->assign('cfgNamespaces', $spaces);
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return $this->cfgTemplate;
	}
}
?>