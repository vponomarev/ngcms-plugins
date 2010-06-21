<div style="text-align : left;">
<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" /><a href="admin.php?mod=extras">{l_extras}</a></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" />{l_config_text}: category_access => {action} </td>
</tr>
<tr><td>&nbsp;</td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr align="center">
<td width="100%" class="contentNav" align="center" style="background-image: url({admin_url}/plugins/category_access/tpl/images/gmo1.png); background-repeat: no-repeat; background-position: left;">
<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access'" value="{l_category_access:button_general}" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access&action=list_user'" value="{l_category_access:button_list_user}" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access&action=list_category'" value="{l_category_access:button_list_category}" class="navbutton" />
&#160;&#160;|&#160;&#160;
<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access&action=add_user'" value="{l_category_access:button_add_user}" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access&action=add_category'" value="{l_category_access:button_add_category}" class="navbutton" />
</td>
</tr>
</table><br />

{entries}

</div>