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
 {wikiplugin name="include" page="SideBar"}
</div>
<div id="userbox">
 {if $loggedIn}
 <h1>{$user.user_name}</h1>
 <ul>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:Preferences"}">{$lang.wiki_user_prefs}</a></li>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:Logout"}">{$lang.wiki_logout}</a></li>
 </ul>
 {else}
 <h1>{$lang.wiki_login}</h1>
 <form method="post" action="{wikiurl page="`$cfg.special_namespace`:Login"}">
 {$lang.login_username}<br />
 <input type="text" size="15" name="username" maxlength="50" /><br />
 {$lang.login_password}<br />
 <input type="password" size="15" name="password" /><br />
 <input type="checkbox" name="remember" id="ql-c1"><label for="ql-c1"><small>{$lang.login_remember}</small></label><br />
 <input type="submit" value="{$lang.login_submit}">
 </form>
 <ul>
  <li><a href="{wikiurl page="`$cfg.special_namespace`:Register"}">{$lang.wiki_register}</a></li>
 </ul>
 {/if}
</div>
<div id="content">
 <h1 class="page-title">{$pageTitle}{if $pageAction != ''} - {$pageAction}{/if}</h1>