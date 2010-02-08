<tr align="center" class="contRow1">
<td>{id}</td>
<td>{name}</td>
<td>{is_on}</td>

<td>
<input type="image" src="{tpl_url}/images/edit.png" title="{l_cat_description:button_edit}" onClick="document.forms['options_bar'].action.value = 'edit'; document.forms['options_bar'].id.value = '{id}';" />&#160;
<input type="image" src="{tpl_url}/images/dell.png" title="{l_cat_description:button_dell}" onClick="document.forms['options_bar'].action.value = 'delete'; document.forms['options_bar'].id.value = '{id}';" />
</td>
</tr>