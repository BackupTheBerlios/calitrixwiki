{include file="header.tpl"}

<p>
{$lang.config_desc}
</p>

<form method="post" action="{wikiurl step="config" lang="`$lang`"}">
<fieldset>
<legend>{$lang.config_paths}</legend>
{$lang.config_paths_url_root}<br />
<input type="text" name="url_root" value="{$cfgUrlRoot}" size="40" /><br /><br />

{$lang.config_paths_doc_root}<br />
<input type="text" name="doc_root" value="{$cfgDocRoot}" size="40" />
</fieldset>

<fieldset>
<legend>{$lang.config_db}</legend>
{$lang.config_db_host}<br />
<input type="text" name="db_host" value="{$cfgDbHost}" size="40" /><br /><br />

{$lang.config_db_name}<br />
<input type="text" name="db_name" value="{$cfgDbName}" size="40" /><br /><br />

{$lang.config_db_user}<br />
<input type="text" name="db_user" value="{$cfgDbUser}" size="40" /><br /><br />

{$lang.config_db_pass}<br />
<input type="password" name="db_pass" value="" size="40" /><br /><br />

{$lang.config_db_prefix}<br />
<input type="text" name="db_prefix" value="{$cfgDbPrefix}" size="40" /><br /><br />
</fieldset>

<fieldset>
<legend>{$lang.config_options}</legend>

{$lang.config_db_create_desc}<br />
<input type="checkbox" name="db_create" id="db-create"{if $dbCreateChecked} checked="checked"{/if} /><label for="db-create">{$lang.config_db_create}</label><br /><br />

{$lang.config_default_pages_desc}<br />
<input type="checkbox" name="default_pages" id="default-pages"{if $defaultPagesChecked} checked="checked"{/if} /><label for="default-pages">{$lang.config_default_pages}</label><br /><br />

<input type="submit" value="{$lang.config_write}" />
</fieldset>
</form>

{include file="footer.tpl"}