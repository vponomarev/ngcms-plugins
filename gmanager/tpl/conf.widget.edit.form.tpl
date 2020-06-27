<form method="post" action="admin.php?mod=extra-config&amp;plugin=gmanager&amp;action=widget_edit_submit">
<input type="hidden" name="id" value="{id}" />
<fieldset>
<legend><b>{l_gmanager:legend_general}</b></legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_gmanager:label_widget_name}<br /><small>{l_gmanager:desc_widget_name}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="25" name="name" value="{name}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_gmanager:label_widget_title}<br /><small>{l_gmanager:desc_widget_title}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="title" value="{title}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_gmanager:label_widget_if_active}<br /><small>{l_gmanager:desc_widget_if_active}</small></td>
<td width="50%" class="contentEntry2">{if_active_list}</td>
</tr>

</table></fieldset><br />
<fieldset>
<legend><b>{l_gmanager:legend_gallery_one}</b></legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_gmanager:label_skin}<br /><small>{l_gmanager:desc_skin}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="25" name="skin" value="{skin}" /></td>
</tr><tr>
<td width="50%" class="contentEntry1">{l_gmanager:label_cell}<br /><small>{l_gmanager:desc_cell}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="25" name="cells" value="{cells}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_gmanager:label_row}<br /><small>{l_gmanager:desc_row}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="25" name="rows" value="{rows}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Сортировка<br /><small>Порядок вывода изображений</small></td>
<td width="50%" class="contentEntry2">{if_rand_list}</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Метод вывода<br /></td>
<td width="50%" class="contentEntry2">{output_method_list}</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_gmanager:label_description}<br /></td>
<td width="50%" class="contentEntry2"><input type="text" size="25" name="description" value="{description}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_gmanager:label_keywords}<br /></td>
<td width="50%" class="contentEntry2"><input type="text" size="25" name="keywords" value="{keywords}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_gmanager:label_galery}<br /><small>{l_gmanager:desc_galery}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="galery" value="{galery}" /></td>
</tr>
</table></fieldset>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="{l_gmanager:button_widget_edit}" class="button" />
</td>
</tr>
</table>
</form>
