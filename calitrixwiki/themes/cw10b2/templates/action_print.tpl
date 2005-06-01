<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" lang="de">
 <head>
  <title>{$pageTitle} | {$wikiTitle}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  <link rel="stylesheet" href="{$urlRoot}/themes/cwiki/print.css" />
</head>

<body>
<h1 class="page-title">{$pageTitle}{if $actionTitle != ''} - {$actionTitle}{/if}</h1>
{$pageText}
<hr />
{wikiurl page="`$pageNameUnique`" hide_session=""}{if $pageId > 0}<br />
{$lang.last_modified}: {$lastModified} {$lang.page_version} {$pageVersion}{/if}
<br /><br />
<a href="{wikiurl page="`$pageNameUnique`"}">{$lang.wiki_back}</a>
</body>
</html>