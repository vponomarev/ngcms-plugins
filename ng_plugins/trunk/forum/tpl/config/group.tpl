	<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr align="center" class="contHead">
			<td>ID</td>
			<td>������������</td>
			<td width="160">��������</td>
		</tr>
		{% for entry in entries %}
			<tr align="center" class="contRow1">
				<td>{{ entry.id }}</td>
				<td>{{ entry.name }}</td>
				<td><a href="admin.php?mod=extra-config&plugin=forum&action=edit_group&id={{ entry.id }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/edit.png" title="�������������" alt="�������������" border="0" /></a> <a href="admin.php?mod=extra-config&plugin=forum&action=del_group&id={{ entry.id }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/dell.png" title="�������" alt="�������" border="0" /></a></td>
			</tr>
		{% endfor %}
	</table>