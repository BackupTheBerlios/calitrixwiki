{include file="header.tpl"}
{if $isOldVersion}
<span class="light-grey">{$versionDesc}</span><br /><br />
{/if}
{include file="inline_message.tpl"}
{$pageText}

{include file="page_cmds.tpl"}

{include file="footer.tpl"}