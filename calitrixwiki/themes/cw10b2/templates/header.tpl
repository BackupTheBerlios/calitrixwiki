<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" lang="de">
 <head>
  <title>{$pageTitle} | {$wikiTitle}</title>
  
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  {if $pageAction == 'history'}<meta name="robots" content="noindex,nofollow" />{/if}
  
  <link rel="stylesheet" id="cssbase" href="{$urlRoot}/themes/cw10b2/style.css" />
  <script type="text/javascript" src="{$urlRoot}/libs/lib_jsfunctions.js"></script>
  
</head>

<body>

<div id="wrapper">
 <div id="header">
  <img src="{$urlRoot}/themes/cw10b2/images/logo.gif" id="logo" />
  <div id="searchbox">
   <form method="get" action="{wikiurl page="`$cfg.special_namespace`:Search"}">
   <input type="hidden" name="page" value="{$cfg.special_namespace}:Search" />
   <input type="text" size="15" maxlength="512" name="q" value="{$lang.search}..." onfocus="this.value='';" onblur="if(this.value == '') this.value = '{$lang.search}...';" />
    {if $sessionId != ''}<input type="hidden" name="s" value="{$sessionId}">{/if}
   <input type="submit" value="{$lang.search_submit}" />
   </form>
  </div>
 </div>
 <div id="menu">
  {wikiplugin name="include" page="SideBar"}
  
  {if $loggedIn}<h1>{$user.user_name}</h1>
  <ul>{if $canUseAcp}
   <li><a href="{wikiurl page="`$cfg.special_namespace`:Admin"}" class="wiki-internal">{$lang.wiki_admin_cp}</a></li>{/if}
   <li><a href="{wikiurl page="`$cfg.special_namespace`:Preferences"}" class="wiki-internal">{$lang.wiki_user_prefs}</a></li>
   <li><a href="{wikiurl page="`$cfg.special_namespace`:Logout"}" class="wiki-internal">{$lang.wiki_logout}</a></li>
  </ul>{else}<h1>{$lang.wiki_login}</h1>
  <form method="post" action="{wikiurl page="`$cfg.special_namespace`:Login"}">
  {$lang.login_username}<br />
  <input type="text" size="15" name="username" maxlength="50" /><br />
  {$lang.login_password}<br />
  <input type="password" size="15" name="password" /><br />
  <input type="checkbox" name="remember" id="ql-c1" /><label for="ql-c1"><small>{$lang.login_remember}</small></label><br />
  <input type="submit" value="{$lang.login_submit}" />
  </form>
  <ul>
   <li><a href="{wikiurl page="`$cfg.special_namespace`:Register"}" class="wiki-internal">{$lang.wiki_register}</a></li>
  </ul>{/if}
 </div>
 <div id="content"{if $cfg.dblclick_editing == 1 && $pageNamespace != $cfg.special_namespace} onDblClick="document.location.href='{wikiurl page="`$pageName`" action="edit"}'"{/if}>
    <h1 class="page-title">{$pageTitle}{if $actionTitle != ''} - {$actionTitle}{/if}</h1>