<tr align="center" [if_active]class="contRow1"[/if_active] [if_not_active]class="contRow2"[/if_not_active]>
<td><input type="image" src="{admin_url}/skins/default/images/up.gif" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=gmanager&action=move_up&id={id}'" />&#160;<input type="image" src="{admin_url}/skins/default/images/down.gif" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=gmanager&action=move_down&id={id}'" /></td>
<td>{name}</td>
<td>{title}</td>
<td>
<input type="image" src="{admin_url}/plugins/gmanager/tpl/images/edit.png" title="{l_gmanager:button_edit}"  onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=gmanager&action=edit&id={id}'" />&#160;
<input type="image" src="{admin_url}/plugins/gmanager/tpl/images/dell.png" title="{l_gmanager:button_dell}"  onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=gmanager&action=dell&id={id}'" />
</td>
</tr>