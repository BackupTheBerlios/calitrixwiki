<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" lang="de">
 <head>
  <title>{$pageTitle} | {$wikiTitle}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  <link rel="stylesheet" href="{$urlRoot}/themes/cwiki/style.css" />
  <script type="text/javascript" src="{$urlRoot}/libs/lib_jsfunctions.js"></script>
</head>

<body>
<div id="header">
 <img src="{$urlRoot}/themes/cwiki/images/logo.gif" alt="{$wikiTitle}" id="logo" />
 <div id="searchbox">
  <a href="{wikiurl page="`$cfg.special_namespace`:Search"}">{$lang.wiki_search}:</a>
  <form method="post" action="{wikiurl page="`$cfg.special_namespace`:Search"}">
  <input type="text" size="15" maxlength="512" name="q" />
  <input type="submit" value="{$lang.wiki_search_submit}" />
  </form>
 </div>
</div>

<div id="menu">
 <h1>{$lang.admin_config}</h1>
 <ul>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}" class="wiki-internal">{$lang.admin_config_general}</a>
   <ul>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}#config-misc">{$lang.admin_config_misc}</a></li>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}#config-ui">{$lang.admin_config_ui}</a></li>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}#config-users">{$lang.admin_config_users}</a></li>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}#config-mailing">{$lang.admin_config_mailing}</a></li>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}#config-namespaces">{$lang.admin_config_namespaces}</a></li>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}#config-paths">{$lang.admin_config_paths}</a></li>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}#config-parser">{$lang.admin_config_parser}</a></li>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}#config-urlrewrite">{$lang.admin_config_urlrewrite}</a></li>
   </ul>
  </li>
 </ul>
 <br />
 <h1>{$lang.admin_manage}</h1>
 <ul>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminInterWiki"}" class="wiki-internal">{$lang.admin_manage_interwiki}</a></li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminWikiStyles"}" class="wiki-internal">{$lang.admin_manage_wikistyles}</a>
   <ul>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminWikiStyles" op="addstyle"}" class="wiki-internal">{$lang.admin_manage_wikistyles_add}</a></li>
	<li><a href="{wikiurl page="`$cfg.special_namespace`:AdminWikiStyles" op="editattribs"}" class="wiki-internal">{$lang.admin_manage_wikistyles_edit}</a></li>
   </ul>
  </li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminNamespaces"}" class="wiki-internal">{$lang.admin_manage_namespaces}</a></li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminThemes"}" class="wiki-internal">{$lang.admin_manage_themes}</a></li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminLanguages"}" class="wiki-internal">{$lang.admin_manage_languages}</a></li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminSnippets"}" class="wiki-internal">{$lang.admin_manage_snippets}</a></li>
 </ul>
 <br />
 <h1>{$lang.admin_users}</h1>
 <ul>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminGroups"}" class="wiki-internal">{$lang.admin_users_groups}</a>
   <ul>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminGroups" op="add"}">{$lang.admin_add_group}</a></li>
   </ul>
  </li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminUsers"}" class="wiki-internal">{$lang.admin_users_users}</a>
   <ul>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminUsers" op="add"}">{$lang.admin_add_user}</a></li>
   </ul>
  </li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminMailUsers"}" class="wiki-internal">{$lang.admin_users_email}</a></li>
 </ul>
 <br />
 <h1>{$lang.admin_database}</h1>
 <ul>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminDbOptimize"}" class="wiki-internal">{$lang.admin_db_optimize}</a></li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminDbBackup"}" class="wiki-internal">{$lang.admin_db_backup}</a>
   <ul>
    <li><a href="{wikiurl page="`$cfg.special_namespace`:AdminDbRestore"}" class="wiki-internal">{$lang.admin_db_restore}</a></li>
   </ul>
  </li>
 </ul>
 <br />
 <h1>{$lang.admin_admin}</h1>
 <ul>
  <li><a href="{wikiurl page="`$cfg.default_page`"}" class="wiki-internal">{$lang.admin_exit}</a></li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:Logout"}" class="wiki-internal">{$lang.wiki_logout}</a></li>
 </ul>
</div>
<div id="content">
 <h1 class="page-title">{$pageTitle}{if $pageAction != ''} - {$pageAction}{/if}</h1>