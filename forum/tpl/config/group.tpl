	<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr align="center" class="contHead">
			<td>ID</td>
			<td>Наименование</td>
			<td width="160">Действие</td>
		</tr>
		{% for entry in entries %}
			<tr align="center" class="contRow1">
				<td>{{ entry.id }}</td>
				<td>{{ entry.name }}</td>
				<td><a href="admin.php?mod=extra-config&plugin=forum&action=edit_group&id={{ entry.id }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/edit.png" title="Редактировать" alt="Редактировать" border="0" /></a> <a href="admin.php?mod=extra-config&plugin=forum&action=del_group&id={{ entry.id }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/dell.png" title="Удалить" alt="Удалить" border="0" /></a></td>
			</tr>
		{% endfor %}
	</table>