{include file="admin_header.tpl"}

{include file="form_errors.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminUsers" op="`$cfgOp`" uid="`$cfgUserId`" p="`$thisPage`"}">
<fieldset>
<legend>{$lang.admin_user_details}</legend>
<span class="light-grey">{$lang.admin_user_details_desc}</span><br /><br />

{$lang.admin_user_name}<br />
<input type="text" name="user_name" size="40" value="{$cfgUserName}" /><br /><br />

{$lang.admin_user_email}<br />
<input type="text" name="user_email" size="40" value="{$cfgUserEmail}" /><br /><br />

{$lang.admin_user_group}<br />
<select name="user_group">
{foreach from="$cfgGroups" item="cfgGroup"}
<option value="{$cfgGroup.group_id}"{if $cfgGroup.group_id == $cfgGroupId} selected="selected"{/if}>{$cfgGroup.group_name}</option>
{/foreach}
</select><br /><br />

{$lang.admin_user_language}<br />
<select name="language">
<option value=""{if $cfgUserLang == ""} selected="selected"{/if}>{$lang.admin_default}</option>
{foreach from="$cfgLanguages" key="langCode" item="langName"}
<option value="{$langCode}"{if $langCode == $cfgUserLang} selected="selected"{/if}>{$langName}</option>
{/foreach}
</select><br /><br />

{$lang.admin_user_theme}<br />
<select name="theme">
<option value=""{if $cfgUserTheme == ""} selected="selected"{/if}>{$lang.admin_default}</option>
{foreach from="$cfgThemes" key="themeDir" item="themeName"}
<option value="{$themeDir}"{if $themeDir == $cfgUserTheme} selected="selected"{/if}>{$themeName}</option>
{/foreach}
</select><br /><br />

{$lang.admin_user_items_pp}<br />
<select name="items_pp">
<option value=""{if $cfgItemsPP == 0} selected="selected"{/if}>{$lang.admin_default}</option>
{foreach from="$cfgPPSelect" item="itemsCount"}
<option value="{$itemsCount}"{if $itemsCount == $cfgItemsPP} selected="selected"{/if}>{$itemsCount}</option>
{/foreach}
</select><br /><br />

<input type="checkbox" name="dblclick_editing" id="dblclick"{if $cfgDblClick == 1} checked="checked"{/if} /><label for="dblclick">{$lang.admin_user_dblclick_editing}</label><br /><br />
<input type="checkbox" name="use_cookies" id="use-cookies"{if $cfgUseCookies == 1} checked="checked"{/if} /><label for="use-cookies">{$lang.admin_user_use_cookies}</label><br /><br />
<input type="checkbox" name="enable_mails" id="enable-mails"{if $cfgEnableMails == 1} checked="checked"{/if} /><label for="enable-mails">{$lang.admin_user_enable_mails}</label>
</fieldset>

<br>

<fieldset>
<legend>{$lang.admin_user_change_pw}</legend>
<span class="light-grey">{$lang.admin_user_change_pw_desc}</span><br /><br />

{$lang.admin_user_password}<br />
<input type="password" name="password" size="40" /><br /><br />

{$lang.admin_user_password_confirm}<br />
<input type="password" name="password_confirm" size="40" /><br /><br />
</fieldset>

<br />

<fieldset>
<legend>{$lang.admin_user_permissions}</legend>
<span class="light-grey">{$lang.admin_user_permissions_desc}</span><br /><br />

<input type="radio" name="use_what" value="group" id="use-group"{if $permUse == 'group'} checked="checked"{/if} /><label for="use-group">{$lang.admin_user_use_group}</label><br />
<input type="radio" name="use_what" value="own" id="use-own"{if $permUse == 'own'} checked="checked"{/if} /><label for="use-own">{$lang.admin_user_use_own}</label><br /><br />

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.admin_perms_right}</td>
  <td class="td-head" width="1%">{$lang.perms_set}</td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_user_right_view}</td>
  <td class="td-last"><input type="checkbox" name="perm_view"{if $cfgPermView} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_user_right_edit}</td>
  <td class="td-last"><input type="checkbox" name="perm_edit"{if $cfgPermEdit} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_user_right_history}</td>
  <td class="td-last"><input type="checkbox" name="perm_history"{if $cfgPermHistory} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_user_right_restore}</td>
  <td class="td-last"><input type="checkbox" name="perm_restore"{if $cfgPermRestore} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_user_right_rename}</td>
  <td class="td-last"><input type="checkbox" name="perm_rename"{if $cfgPermRename} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_user_right_delete}</td>
  <td class="td-last"><input type="checkbox" name="perm_delete"{if $cfgPermDelete} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_user_right_ignore_local}</td>
  <td class="td-last"><input type="checkbox" name="perm_iglocal"{if $cfgPermIgLocal} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_user_right_set_local}</td>
  <td class="td-last"><input type="checkbox" name="perm_setlocal"{if $cfgPermSetLocal} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_user_right_use_acp}</td>
  <td class="td-last"><input type="checkbox" name="perm_useacp"{if $cfgPermUseAcp} checked="checked"{/if} /></td>
 </tr>
</table>
<br /><br />
<input type="submit" value="{$lang.admin_user_submit}" />
</fieldset>
</form>

{include file="admin_footer.tpl"}