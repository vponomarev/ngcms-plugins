<form method="post" action="admin.php?mod=extra-config&amp;plugin=multi_main&amp;action=add_form[edit]&amp;cat={cat}[/edit]">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_multi_main:label_cat}<br /><small>{l_multi_main:desc_cat}</small></td>
<td width="50%" class="contentEntry2">{cat_list}</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_multi_main:label_tpl}<br /><small>{l_multi_main:desc_tpl}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="tpl" value="{tpl}" /></td>
</tr>
</table><br />


<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="[add]{l_multi_main:button_add_submit}[/add][edit]{l_multi_main:button_edit_submit}[/edit]" class="button" />
</td>
</tr>
</table>
</form>
