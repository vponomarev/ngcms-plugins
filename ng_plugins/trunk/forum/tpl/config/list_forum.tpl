<tr align="center" class="contRow1">
	<td>{{ forum_id }}</td>
	<td><input type="text" name="position[{{ forum_id }}]" value="{{ pos }}" maxlength="5" size="5" /></td>
	<td>{{ forum_name }}<br/><small><a href="{{ forum_link }}">{{ home_url }}{{ forum_link }}</a></small></td>
	<td>{{ num_topic }}</td>
	<td>{{ num_post }}</td>
	<td><a href="admin.php?mod=extra-config&plugin=forum&action=edit_section&id={{ forum_id }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/edit.png" title="�������������" alt="�������������" border="0" /></a> <a href="admin.php?mod=extra-config&plugin=forum&action=del_section&id={{ forum_id }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/dell.png" title="�������" alt="�������" border="0" /></a><a href="admin.php?mod=extra-config&plugin=forum&action=send_forum&id={{ forum_id }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/add.png" title="�������� �����" alt="�������� �����" border="0" /></a></td>
</tr>
{{ entries.print }}