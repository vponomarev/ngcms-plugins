<form method="post" id="commit_delete" action="admin.php?mod=extra-config&amp;plugin=category_access&amp;action=dell_category">
<input type="hidden" name="category" value="{category}" />
<input type="hidden" id="commit" name="commit" value="no" />
<div align="center"><font color="red" size="+2">{commit}</font></div>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="{l_category_access:button_cancel}" class="button" />&#160;
<input type="submit" onclick="document.forms['commit_delete'].elements['commit'].value='yes'; return true;" value="{l_category_access:button_dell}" class="button" />
</td>
</tr>
</table>
</form>