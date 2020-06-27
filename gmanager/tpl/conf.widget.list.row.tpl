<tr align="center" [if_active]class="contRow1"[/if_active] [if_not_active]class="contRow2"[/if_not_active]>
<td>{name}</td>
<td>{title}</td>
<td>{galery}</td>
<td>{skin}</td>
<td>{grid}</td>
<td>{rand}</td>
<td>
<input type="image" src="{admin_url}/plugins/gmanager/tpl/images/edit.png" title="{l_gmanager:button_edit}"  onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=gmanager&action=widget_add&id={id}'" />&#160;
<input type="image" src="{admin_url}/plugins/gmanager/tpl/images/dell.png" title="{l_gmanager:button_dell}"  onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=gmanager&action=widget_dell&id={id}'" />
</td>
</tr>
